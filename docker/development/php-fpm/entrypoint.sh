#!/bin/sh
#==============================================================================
# Development PHP-FPM Entrypoint
#==============================================================================
# Lightweight initialization for development environment:
# - Syncs file permissions with host user
# - Clears Laravel caches for fresh state
#==============================================================================

# Note: set -e disabled to continue on non-critical errors
# set -e

#------------------------------------------------------------------------------
# 1. Sync File Permissions with Host
#------------------------------------------------------------------------------
# Match container user with host UID/GID to avoid permission issues
USER_ID=${UID:-1000}
GROUP_ID=${GID:-1000}

echo "[INIT] Setting file ownership (UID=${USER_ID}, GID=${GROUP_ID})..."
chown -R ${USER_ID}:${GROUP_ID} /var/www || \
  echo "[WARNING] Some files could not be changed"

#------------------------------------------------------------------------------
# 2. Clear Laravel Caches
#------------------------------------------------------------------------------
# Ensure fresh state for development (no stale cached configs/routes)
echo "[LARAVEL] Clearing cached configurations..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

#------------------------------------------------------------------------------
# 3. Start PHP-FPM
#------------------------------------------------------------------------------
echo "[STARTUP] Starting PHP-FPM in development mode..."
exec "$@"
