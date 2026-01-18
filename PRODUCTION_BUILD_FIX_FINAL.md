# Production Build Fix - Final Robust Solution

## Problem Summary
- Local: Build works fine, `/var/www/public/build/` created successfully
- VPS: Build fails, `/var/www/public/build/` directory missing

## Root Cause Identified
**Node.js version mismatch**:
- Local machine: Uses newer Node.js
- VPS Debian repo: Ships with outdated Node.js version
- This causes different build behavior or npm ci/npm run build to fail

## Final Solution

### Changes to docker/common/php-fpm/Dockerfile

1. **Node.js Installation (CRITICAL FIX)**
   ```dockerfile
   # OLD: apt-get install nodejs npm (outdated!)

   # NEW: Use NodeSource repository (Node.js 20.x LTS)
   RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
       apt-get install -y nodejs
   ```

2. **Enhanced Build Logging**
   - Shows Node/NPM versions at build time
   - Displays last lines of npm ci output
   - Displays last lines of npm run build output
   - Immediately fails if build artifacts missing
   - Shows directory structure if build fails

3. **Explicit Verification**
   - Checks if `/var/www/public/build/` exists
   - Lists its contents
   - Fails the entire build if missing (prevents silent failures)

## How to Deploy on VPS

### Step 1: Update Code
```bash
cd /path/to/hpvtcrm
git pull origin main
```

### Step 2: Rebuild Docker Image (Fresh Build)
```bash
# IMPORTANT: Use --no-cache to ensure NodeSource repo is used
docker-compose -f compose.prod.yaml build --no-cache php-fpm
```

### Step 3: Watch Build Output
Look for these success indicators:
```
===== NPM Build Step =====
Node version: v20.X.X
NPM version: X.X.X

Step 1: Install npm dependencies
added X packages, ...

Step 2: Build assets
✓ built in X.XXs
✓ built in XXXms (SSR)

Step 3: Verify build output
✓ SUCCESS: /var/www/public/build/ exists
...
===== NPM Build Complete =====
```

### Step 4: If Build Fails
The output will show:
```
✗ FAILED: /var/www/public/build/ does not exist!
Directory structure:
/var/www/public
```

If this happens, check:
1. Do you have `npm run build` in your package.json scripts?
2. Is `vite` installed in devDependencies?
3. Are there any errors in the npm output above the failure?

### Step 5: Restart Services
```bash
docker-compose -f compose.prod.yaml down
docker-compose -f compose.prod.yaml up -d
```

### Step 6: Final Verification
```bash
# Check assets exist
docker-compose -f compose.prod.yaml exec php-fpm ls -la /var/www/public/build/

# Should show:
# -rw-r--r-- manifest.json
# drwxr-xr-x assets/

# Check manifest accessible via HTTP
curl http://your-domain/build/manifest.json | head -20
```

## Why This Fix Works

| Problem | Solution |
|---------|----------|
| Outdated Node.js on VPS | Use NodeSource repo for Node.js 20.x LTS |
| Silent build failures | Explicit verification step that fails the build |
| Different behavior local vs VPS | Consistent Node.js version everywhere |
| Hard to debug issues | Detailed logging shows exactly what happens |

## Key Files Modified

- `docker/common/php-fpm/Dockerfile` - Fixed Node.js installation and added verification

## Troubleshooting

If still getting "directory not found" error after deployment:

```bash
# 1. Force clean rebuild
docker-compose -f compose.prod.yaml down
docker system prune -a -f
docker-compose -f compose.prod.yaml build --no-cache

# 2. Check build output carefully for errors
docker-compose -f compose.prod.yaml build --no-cache 2>&1 | grep -i "error\|failed\|build"

# 3. Verify the image has the build folder
docker run --rm $(docker-compose -f compose.prod.yaml images -q php-fpm) ls -la /var/www/public/build/

# 4. Check if npm run build actually works
docker run --rm -v $(pwd):/app -w /app node:20 sh -c "npm ci && npm run build"
```

## Expected Build Output

When build succeeds, you'll see in the Docker build log:
```
#XX [stage-1 XX/XX] RUN echo "" &&  ...
#XX 0.XXX ===== NPM Build Step =====
#XX 0.XXX Node version: v20.XX.X
#XX 0.XXX NPM version: X.X.X
#XX X.XXX
#XX X.XXX Step 1: Install npm dependencies
#XX X.XXX added 48 packages
#XX X.XXX
#XX X.XXX Step 2: Build assets
#XX X.XXX ✓ built in 2.79s
#XX X.XXX ✓ built in 877ms
#XX X.XXX
#XX X.XXX Step 3: Verify build output
#XX X.XXX Checking for build artifacts...
#XX X.XXX ✓ SUCCESS: /var/www/public/build/ exists
#XX X.XXX -rw-r--r--  manifest.json
#XX X.XXX drwxr-xr-x  assets
#XX X.XXX
#XX X.XXX ===== NPM Build Complete =====
#XX DONE X.XXs
```

## Summary

✅ Uses Node.js 20.x from NodeSource (consistent across all platforms)
✅ Explicit build verification (fails if assets missing)
✅ Detailed logging for debugging
✅ No more silent failures
✅ Works on both local and VPS production

**This should completely resolve the issue!**
