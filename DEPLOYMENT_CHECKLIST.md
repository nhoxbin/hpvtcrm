# Production Deployment Checklist - Asset Fix

## Pre-Deployment (Local)

- [x] Assets build successfully locally
  ```bash
  docker build -f docker/production/nginx/Dockerfile .
  ```

- [x] PHP-FPM image includes manifest.json
  ```bash
  docker build -f docker/common/php-fpm/Dockerfile --target production .
  ```

- [x] Files modified:
  - [x] `docker/common/php-fpm/Dockerfile` - Added assets-builder stage
  - [x] `docker/production/nginx/Dockerfile` - Already correct
  - [x] `docker/production/php-fpm/entrypoint.sh` - Added build folder permissions
  - [x] `compose.prod.yaml` - Removed nginx volume mount

## VPS Deployment

### Step 1: Backup Current State
```bash
cd /path/to/hpvtcrm
docker-compose -f compose.prod.yaml ps  # Check running containers
docker images | grep hpvtcrm              # Note current image IDs
```

### Step 2: Update Code
```bash
git pull origin main
# Verify the following files are updated:
git diff docker/common/php-fpm/Dockerfile
git diff docker/production/php-fpm/entrypoint.sh
git diff compose.prod.yaml
```

### Step 3: Rebuild Images
```bash
docker-compose -f compose.prod.yaml build --no-cache web php-fpm
```

### Step 4: Stop Current Services
```bash
docker-compose -f compose.prod.yaml down
```

### Step 5: Start New Services
```bash
docker-compose -f compose.prod.yaml up -d
```

### Step 6: Verify Deployment

**Check containers are running:**
```bash
docker-compose -f compose.prod.yaml ps
```

**Verify assets in php-fpm:**
```bash
docker-compose -f compose.prod.yaml exec php-fpm ls -la /var/www/public/build/
# Should show:
# - manifest.json
# - assets/ directory
```

**Verify assets in nginx:**
```bash
docker-compose -f compose.prod.yaml exec web ls -la /var/www/public/build/
```

**Test manifest.json HTTP access:**
```bash
curl http://your-domain/build/manifest.json | jq .
# Should return valid JSON with asset mappings
```

**Check Laravel logs for errors:**
```bash
docker-compose -f compose.prod.yaml logs php-fpm | head -50
docker-compose -f compose.prod.yaml logs web | head -50
```

## Post-Deployment Testing

### Test Asset Loading
1. Open application in browser
2. Right-click → Inspect → Network tab
3. Verify CSS/JS files load with 200 status
4. No 404 errors for assets

### Test Laravel Asset Helper
```bash
docker-compose -f compose.prod.yaml exec php-fpm php artisan tinker
>>> asset('build/manifest.json')
# Should return: /build/manifest.json
>>> asset('app.css')
# Should return a valid asset path like: /build/assets/app-ABC123.css
```

### Monitor Logs for 24 Hours
```bash
docker-compose -f compose.prod.yaml logs -f --tail=100
# Watch for any "Vite manifest not found" errors
```

## Rollback Plan (If Issues Occur)

If you encounter problems:

```bash
# Stop current containers
docker-compose -f compose.prod.yaml down

# Switch back to previous code
git checkout HEAD~1

# Rebuild with previous configuration
docker-compose -f compose.prod.yaml build --no-cache

# Restart
docker-compose -f compose.prod.yaml up -d

# Verify old state
docker-compose -f compose.prod.yaml logs -f
```

## Troubleshooting Guide

### Issue: "Vite manifest not found at: /var/www/public/build/manifest.json"

**Solution:**
```bash
# 1. Verify file exists in containers
docker-compose -f compose.prod.yaml exec php-fpm test -f /var/www/public/build/manifest.json && echo "✓ File exists"

# 2. Check permissions
docker-compose -f compose.prod.yaml exec php-fpm stat /var/www/public/build/manifest.json

# 3. Force container rebuild
docker-compose -f compose.prod.yaml down
docker system prune -f
docker-compose -f compose.prod.yaml build --no-cache
docker-compose -f compose.prod.yaml up -d
```

### Issue: Assets showing 404

**Solution:**
```bash
# 1. Verify nginx can access assets
docker-compose -f compose.prod.yaml exec web ls -la /var/www/public/build/

# 2. Check nginx config includes build assets
docker-compose -f compose.prod.yaml exec web cat /etc/nginx/nginx.conf | grep -A 5 "location ~"

# 3. View nginx error logs
docker-compose -f compose.prod.yaml logs web
```

### Issue: Build takes very long on VPS

**Expected behavior:**
- Composer install: ~30 seconds
- npm ci: ~5 seconds
- npm run build: ~10 seconds
- Total build time: 2-5 minutes per image

**If it's taking longer:**
- Check disk space: `df -h`
- Check network: `ping docker.io`
- Check CPU: `top`

## Success Criteria

✅ All deployments checks pass:
- [ ] Containers running
- [ ] manifest.json exists in both containers
- [ ] Assets accessible via HTTP
- [ ] No 404 errors in logs
- [ ] No "Vite manifest not found" errors
- [ ] CSS and JS files load correctly

## Notes

- This fix ensures assets are **baked into Docker images**, not dependent on volumes
- Build assets are now **guaranteed to exist** at container startup
- No manual asset initialization needed on VPS
- Assets are immutable once container starts (per Docker image)

## Questions or Issues?

Reference: `PRODUCTION_BUILD_FIX.md` for detailed explanation of changes
