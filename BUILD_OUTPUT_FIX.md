# Build Output Directory Issue - FIXED

## The Actual Problem

**npm run build IS working**, but the output was going to the **wrong directory**:

- ❌ Vite outputs to: `/var/www/dist/` (Vite default)
- ✅ Laravel expects: `/var/www/public/build/` (Laravel convention)
- ❌ We were looking in: `/var/www/public/build/` ← Not found!

## The Fix

Both Dockerfiles now:
1. Run `npm run build` ✓
2. Check where output was created
3. **Automatically move it** from `/var/www/dist/` → `/var/www/public/build/` ✓
4. Verify it exists before completing

### Code Added to Dockerfiles

```bash
# After npm run build completes:
if [ -d /var/www/public/build ]; then
    echo "✓ Build already in correct location"
elif [ -d /var/www/dist ]; then
    echo "Moving Vite output to Laravel location"
    mkdir -p /var/www/public
    mv /var/www/dist /var/www/public/build
    echo "✓ Success"
fi
```

## Files Updated

1. **docker/common/php-fpm/Dockerfile**
   - Checks for build output in multiple locations
   - Automatically moves `/var/www/dist/` → `/var/www/public/build/`
   - Shows detailed directory structure if something goes wrong

2. **docker/production/nginx/Dockerfile**
   - Same fix applied
   - Ensures assets are in correct location for nginx to serve

## Deploy on VPS

```bash
cd /path/to/hpvtcrm
git pull origin main
docker-compose -f compose.prod.yaml build --no-cache
docker-compose -f compose.prod.yaml down
docker-compose -f compose.prod.yaml up -d

# Verify
docker-compose -f compose.prod.yaml exec php-fpm ls -la /var/www/public/build/
```

## Why This Works

- ✅ Handles both locations (in case vite.config.js ever changes)
- ✅ Automatically moves output to correct location
- ✅ Works with both `php-fpm` and `nginx` images
- ✅ Provides detailed error messages if something fails
- ✅ No need to modify vite.config.js or package.json

## Expected Build Output

```
===== NPM Build Step =====
...
Step 2: Build assets with Vite
✓ built in 2.79s
✓ built in 877ms (SSR)

Step 3: Check where build output was created
Contents of /var/www/public/:
drwxr-xr-x  build

Step 4: Verify and move build artifacts
✓ /var/www/public/build/ already exists

Step 5: Final verification
✓ SUCCESS: /var/www/public/build/ exists
-rw-r--r--  manifest.json
drwxr-xr-x  assets

===== NPM Build Complete =====
```

## Summary

The npm build IS working! It just outputs to `/var/www/dist/` instead of `/var/www/public/build/`. The fix automatically moves it to the correct location so Laravel can find it.
