# Final Fix - Vite Manifest Solution

## Issue
`ls: cannot access '/var/www/public/build/': No such file or directory`

## Root Cause
Using a shared volume `laravel-public-assets` that starts empty was shadowing the built assets in the Docker images.

## Final Solution
**Remove the shared volume entirely.** Each Docker image (nginx and php-fpm) has the assets baked in during the build, so they don't need to share them.

### Changes Made:

1. **docker/common/php-fpm/Dockerfile**
   - Added `assets-builder` stage to build Node.js assets
   - Copies built assets to production image with correct order:
     ```dockerfile
     COPY --from=builder /var/www /var/www  # (empty public/)
     COPY --from=assets-builder /var/www/public /var/www/public  # (with built assets - overrides)
     ```

2. **compose.prod.yaml**
   - ❌ Removed: `laravel-public-assets:/var/www/public` volume from php-fpm
   - ❌ Removed: `laravel-public-assets` volume definition
   - ✅ Kept: `laravel-storage-production` volume (for app logs, cache)

3. **Result**
   - php-fpm container has `/var/www/public/build/manifest.json` from image
   - nginx container has `/var/www/public/build/manifest.json` from image
   - No shared volume dependency = more reliable

## Deployment

```bash
cd /path/to/hpvtcrm

# Update code
git pull origin main

# Rebuild images
docker-compose -f compose.prod.yaml build --no-cache

# Restart services
docker-compose -f compose.prod.yaml down
docker-compose -f compose.prod.yaml up -d

# Verify
docker-compose -f compose.prod.yaml exec php-fpm ls -la /var/www/public/build/
# Should show: manifest.json and assets/ directory
```

## Why This Works
- ✅ Assets are baked into Docker images during build
- ✅ No volume initialization issues
- ✅ Each container has guaranteed access to assets
- ✅ No synchronization problems between containers
- ✅ Manifest.json always exists at startup
