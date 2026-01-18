# Updated Fix - Simplified Approach

## Problem
`ls: cannot access '/var/www/public/build/': No such file or directory`

## Root Cause
Previous approach had multiple stages which made asset copying complex and error-prone.

## New Solution
**Simplified single-stage build approach:**

The `builder` stage (Stage 1) now includes:
1. PHP + PHP extensions
2. **Node.js and npm** (added)
3. Composer
4. **npm install + npm run build** (added)

This way, everything is built in one place and copied as a whole to the production stage. No complex stage-to-stage copies.

## Changes Made

### docker/common/php-fpm/Dockerfile
- Added `nodejs` and `npm` to builder stage system dependencies
- Moved `npm ci && npm run build` directly into builder stage
- Removed complex assets-builder stage
- Simplified to: `COPY --from=builder /var/www /var/www` (includes assets)

## How It Works
```
Stage 1 (builder):
  - Installs PHP + extensions ✓
  - Installs Node.js + npm ✓  <- NEW
  - Runs: composer install ✓
  - Runs: npm ci ✓             <- NEW
  - Runs: npm run build ✓      <- NEW
  - Creates: /var/www/public/build/ ✓
  
Stage 2 (production):
  - Copy EVERYTHING from builder (includes /var/www/public/build/) ✓
  - Verify assets exist ✓
  - Remove build tools to reduce size
```

## Deployment on VPS

```bash
cd /path/to/hpvtcrm

# Update code
git pull origin main

# Rebuild image (with Node.js included now)
docker-compose -f compose.prod.yaml build --no-cache php-fpm

# Restart
docker-compose -f compose.prod.yaml down
docker-compose -f compose.prod.yaml up -d

# Verify assets
docker-compose -f compose.prod.yaml exec php-fpm ls -la /var/www/public/build/

# Should show:
# -rw-r--r--  manifest.json
# drwxr-xr-x  assets/
```

## Build Output Example
During build, you should see:
```
=== Verifying assets in production stage ===
total 56
drwxr-xr-x    3 root     root  4096 Jan 18 manifest.json
drwxr-xr-x    2 root     root  4096 Jan 18 assets/
✓ Build assets verified
```

## Why This Works
✅ All build tools in one stage
✅ Assets created before production copy
✅ No stage-to-stage copying issues
✅ Verification step ensures assets exist
✅ Simpler to debug if something fails
