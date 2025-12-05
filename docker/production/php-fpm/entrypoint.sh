#!/bin/sh
#==============================================================================
# Production PHP-FPM Entrypoint
#==============================================================================
# Initializes Laravel application for production deployment:
# - Sets up storage directories with proper permissions
# - Installs dependencies if missing
# - Waits for database connectivity
# - Runs migrations
# - Optimizes application caching
#
# Note: Runs as root for initialization, then PHP-FPM process manager
# automatically runs worker processes as www-data (configured in www.conf)
#==============================================================================

set -e

#------------------------------------------------------------------------------
# 1. Initialize Storage Directory
#------------------------------------------------------------------------------
# Copy initial storage structure if directory is empty (first-time setup)
if [ ! "$(ls -A /var/www/storage)" ]; then
  echo "[INIT] Setting up storage directory..."
  cp -R /var/www/storage-init/. /var/www/storage
  chown -R www-data:www-data /var/www/storage
  chmod -R 775 /var/www/storage
fi

# Clean up initialization template
rm -rf /var/www/storage-init

#------------------------------------------------------------------------------
# 2. Set Directory Permissions
#------------------------------------------------------------------------------
# Ensure Laravel's writable directories have correct permissions
echo "[INIT] Setting directory permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Ensure specific writable subdirectories exist and have correct permissions
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/storage/app/public

chown -R www-data:www-data /var/www/storage
find /var/www/storage -type d -exec chmod 775 {} \;
find /var/www/storage -type f -exec chmod 664 {} \;

#------------------------------------------------------------------------------
# 3. Install Composer Dependencies
#------------------------------------------------------------------------------
# Ensures vendor directory exists (useful if volume-mounted or missing)
if [ ! -d "/var/www/vendor" ] || [ ! -f "/var/www/vendor/autoload.php" ]; then
  echo "[COMPOSER] Installing dependencies..."
  cd /var/www
  su -s /bin/sh www-data -c "composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist"
else
  echo "[COMPOSER] Dependencies already installed, skipping..."
fi

#------------------------------------------------------------------------------
# 4. Build Frontend Assets (Optional)
#------------------------------------------------------------------------------
# Rebuilds assets if public/build directory is missing
# Note: Typically handled by Nginx container, but useful as fallback
if [ ! -d "/var/www/public/build" ] || [ ! -f "/var/www/public/build/manifest.json" ]; then
  echo "[ASSETS] Building frontend assets..."
  cd /var/www
  if command -v npm > /dev/null 2>&1; then
    su -s /bin/sh www-data -c "npm install" || echo "[WARNING] npm install failed"
    su -s /bin/sh www-data -c "npm run build" || echo "[WARNING] npm run build failed"
  else
    echo "[WARNING] npm not found, skipping frontend build"
  fi
else
  echo "[ASSETS] Frontend assets already built, skipping..."
fi

#------------------------------------------------------------------------------
# 5. Wait for Database Connectivity
#------------------------------------------------------------------------------
echo "[DATABASE] Waiting for database connection..."
max_attempts=30
attempt=0

until php /usr/local/bin/check-db.php > /dev/null 2>&1 || [ $attempt -eq $max_attempts ]; do
  attempt=$((attempt + 1))
  echo "[DATABASE] Connection attempt $attempt of $max_attempts..."
  sleep 2
done

if [ $attempt -eq $max_attempts ]; then
  echo "[WARNING] Database connection timeout after $max_attempts attempts"
  echo "[WARNING] Clearing cached config and continuing..."
  su -s /bin/sh www-data -c "php artisan config:clear" || true
else
  echo "[DATABASE] Connected successfully (attempt $attempt)"

  #----------------------------------------------------------------------------
  # 6. Run Database Migrations
  #----------------------------------------------------------------------------
  echo "[LARAVEL] Clearing config cache..."
  su -s /bin/sh www-data -c "php artisan config:clear" || true

  echo "[LARAVEL] Running migrations..."
  su -s /bin/sh www-data -c "php artisan migrate --force" || \
    echo "[WARNING] Migrations failed, continuing anyway..."
fi

#------------------------------------------------------------------------------
# 7. Optimize Application
#------------------------------------------------------------------------------
# Cache routes and views for better performance
# Note: Config caching disabled to allow runtime configuration changes
echo "[LARAVEL] Optimizing application..."
su -s /bin/sh www-data -c "php artisan optimize:clear" || true
su -s /bin/sh www-data -c "php artisan route:cache" || true
su -s /bin/sh www-data -c "php artisan view:cache" || true

#------------------------------------------------------------------------------
# 8. Start PHP-FPM
#------------------------------------------------------------------------------
echo "[STARTUP] Starting PHP-FPM..."
exec "$@"
