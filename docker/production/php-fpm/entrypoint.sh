#!/bin/sh
set -e

# Initialize storage directory if empty
# -----------------------------------------------------------
# If the storage directory is empty, copy the initial contents
# -----------------------------------------------------------
if [ ! "$(ls -A /var/www/storage)" ]; then
  echo "Initializing storage directory..."
  cp -R /var/www/storage-init/. /var/www/storage
  chown -R www-data:www-data /var/www/storage
  chmod -R 775 /var/www/storage
fi

# Remove storage-init directory
rm -rf /var/www/storage-init

# Ensure storage and bootstrap/cache are writable
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

echo "Waiting for database to be ready..."
# Wait for database connection with timeout
max_attempts=30
attempt=0
until php /usr/local/bin/check-db.php > /dev/null 2>&1 || [ $attempt -eq $max_attempts ]; do
  attempt=$((attempt + 1))
  echo "Database not ready, attempt $attempt of $max_attempts..."
  sleep 2
done

if [ $attempt -eq $max_attempts ]; then
  echo "WARNING: Could not connect to database after $max_attempts attempts. Continuing anyway..."
else
  echo "Database is ready (connected in $attempt attempts)!"

  # Run Laravel migrations
  # -----------------------------------------------------------
  # Ensure the database schema is up to date.
  # -----------------------------------------------------------
  echo "Running migrations..."
  gosu www-data php artisan migrate --force || echo "WARNING: Migrations failed, continuing anyway..."
fi

# Clear and cache configurations
# -----------------------------------------------------------
# Improves performance by caching config and routes.
# -----------------------------------------------------------
echo "Optimizing application..."
gosu www-data php artisan optimize:clear || true
gosu www-data php artisan optimize || true
# gosu www-data php artisan config:cache
# gosu www-data php artisan route:cache

echo "Starting PHP-FPM..."
# Run the default command (PHP-FPM will run as www-data via its config)
exec "$@"
