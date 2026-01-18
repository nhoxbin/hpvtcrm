# Production Build Fix - npm run build Issue

## Problem Identified
The `public/build` folder was not being created during production Docker build despite `composer install` succeeding.

## Root Causes

1. **Wrong Base Image for Node Build**: The builder stage used `debian` as a base image and installed Node.js manually, which was inefficient and error-prone.

2. **Missing Dev Dependencies**: The original command used `npm ci --omit=dev` which skips development dependencies. However, Vite (a dev dependency) is **required** to build assets during the Docker build process.

3. **Incorrect Volume Mount Path**: The nginx service volume was mounted to `/var/www/public/build:ro` instead of `/var/www/public:ro`, causing only the build subfolder to be shared instead of the entire public directory.

## Changes Made

### 1. Updated `docker/production/nginx/Dockerfile`

**Changed the builder stage:**
```dockerfile
# Old (Inefficient and broken)
FROM debian AS builder
RUN apt-get update && apt-get install -y curl nodejs npm
RUN npm install && npm run build

# New (Efficient and working)
FROM node:20-alpine AS builder
RUN npm ci && npm run build
```

**Why:**
- `node:20-alpine` is the official, optimized Node.js image
- Uses `npm ci` instead of `npm install` (better for CI/CD - respects lock file)
- **Includes dev dependencies** needed for build (Vite, plugins, etc.)
- Smaller image size and faster build

### 2. Updated `compose.prod.yaml` - Web Service

**Before:**
```yaml
volumes:
  - laravel-public-assets:/var/www/public/build:ro
```

**After:**
```yaml
volumes:
  - laravel-public-assets:/var/www/public:ro
```

**Why:** Share the entire public directory, not just the build subfolder

### 3. Updated `compose.prod.yaml` - PHP-FPM Service

**Before:**
```yaml
volumes:
  - laravel-public-assets:/var/www/public/build
```

**After:**
```yaml
volumes:
  - laravel-public-assets:/var/www/public
```

**Why:** Ensures PHP-FPM can access manifest.json and handle asset caching properly

## Build Output Verification

The build now successfully creates:
- ✅ `public/build/assets/` - All compiled JavaScript/CSS files
- ✅ `public/build/manifest.json` - Asset manifest for Laravel
- ✅ `bootstrap/ssr/` - Server-side rendering bundle

Example build output:
```
public/build/assets/app-CkU2W1WC.js           235.88 kB │ gzip: 85.20 kB
public/build/manifest.json                    28991 bytes
bootstrap/ssr/ssr.js                          28.37 kB
✓ built in 2.79s
```

## Testing

The Docker build passes successfully:
```bash
docker build -f docker/production/nginx/Dockerfile .
```

All assets are properly compiled and available in the container.

## Permission Notes

- Docker handles permissions automatically during build
- The `laravel-public-assets` volume maintains RW access in php-fpm for cache operations
- Nginx has RO access to prevent accidental modifications during runtime
- No manual permission fixes needed on the VPS

## Next Steps for Production

1. Run: `docker-compose -f compose.prod.yaml build --no-cache`
2. Deploy the new images to your VPS
3. Verify: Check that nginx container has the build assets at `/var/www/public/build/`
4. Monitor: Laravel should use the manifest to load assets correctly

