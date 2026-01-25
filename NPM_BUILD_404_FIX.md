# NPM Build 404 Errors - Complete Fix

## Problem
- `npm run build` creates files in `public/build` folder
- Production returns 404 errors when accessing built assets
- Static files (CSS, JS) not being served correctly

## Root Causes Fixed

### 1. **Nginx Configuration Issue**
The old nginx config tried to use php-fpm, which doesn't exist in the nginx container.

✅ **Fixed**: Updated `/docker/production/nginx/nginx.conf` to:
- Properly serve static assets with `try_files $uri 404`
- Add caching headers for build artifacts (1 year cache)
- Enable gzip compression
- Return 404 for PHP requests (instead of trying to forward to php-fpm)

### 2. **Build Artifact Copy Issue**
Using `mv` could fail or incomplete.

✅ **Fixed**: Updated `/docker/production/nginx/Dockerfile` to:
- Use `cp -r` to copy dist files safely
- Create build directory explicitly
- Verify manifest.json exists before completing build
- Show clear error if build fails

### 3. **Asset Serving**
Assets weren't being served with proper caching and content-type headers.

✅ **Fixed**: Added specific location blocks in nginx for:
- CSS/JS/Image files with long cache headers
- Build directory with immutable cache headers

## Files Changed

### 1. `/docker/production/nginx/nginx.conf`
- Added gzip compression
- Added static asset caching (1 year expiry)
- Fixed PHP request handling
- Optimized try_files directives

### 2. `/docker/production/nginx/Dockerfile`
- Enhanced build verification
- Better error handling
- Explicit manifest.json validation

## Deployment Steps

### Step 1: Update Your Code
```bash
cd /path/to/hpvtcrm
git pull origin main
```

### Step 2: Rebuild Docker Images
```bash
# Rebuild with no cache to ensure fresh build
docker-compose -f compose.prod.yaml build --no-cache nginx
```

### Step 3: Restart Services
```bash
# Stop and remove old containers
docker-compose -f compose.prod.yaml down

# Start fresh
docker-compose -f compose.prod.yaml up -d
```

### Step 4: Verify Build Artifacts
```bash
# Check if manifest.json exists
docker-compose -f compose.prod.yaml exec nginx ls -la /var/www/public/build/

# Should output:
# manifest.json
# .vite/
# (and other build files)
```

### Step 5: Test Asset Loading
1. Visit your production app
2. Open browser DevTools (F12)
3. Go to Network tab
4. Refresh the page
5. Check that CSS and JS files load with **200 status code** (not 404)

## How It Works

```
Docker Build Process:
┌─────────────────────────┐
│  Node.js Alpine Builder │
│                         │
│  npm ci                 │  Install deps
│  npm run build          │  Build to dist/
│  cp dist → public/build │  Copy to public
│                         │
│  Verify manifest.json   │
└─────────────────────────┘
         ↓
┌─────────────────────────┐
│  Nginx Alpine Image     │
│                         │
│  COPY /var/www/public   │  Get built assets
│  from builder stage     │
│                         │
│  Start Nginx            │  Serve on port 80
└─────────────────────────┘

Production Serving:
┌──────────┐
│ Browser  │
└────┬─────┘
     │
     ├─→ /index.php         → try_files → index.php (404 now, no PHP)
     ├─→ /app.js            → /var/www/public/build/app.js (200)
     ├─→ /app.css           → /var/www/public/build/app.css (200)
     └─→ /build/manifest.json → /var/www/public/build/manifest.json (200)
```

## Troubleshooting

### Assets Still returning 404?
```bash
# 1. Check if files exist in container
docker-compose -f compose.prod.yaml exec nginx find /var/www/public -name "*.js" -o -name "*.css"

# 2. Check nginx logs
docker-compose -f compose.prod.yaml logs nginx

# 3. Rebuild with no cache
docker-compose -f compose.prod.yaml build --no-cache nginx
docker-compose -f compose.prod.yaml restart nginx
```

### Build process failing in Docker?
```bash
# 1. Check build logs
docker-compose -f compose.prod.yaml build --no-cache nginx 2>&1 | tail -100

# 2. Look for npm errors
# Common issues: Node version, missing dependencies, disk space

# 3. Try local build first
npm ci
npm run build
ls -la public/build/manifest.json  # Should exist
```

### manifest.json missing?
This happens when Vite build fails silently:
1. Check that `resources/css/app.css` exists
2. Check that `resources/js/app.js` exists
3. Run locally: `npm run build` and check for errors
4. Check disk space in container during build

## Verification Checklist

- [ ] Docker image builds successfully
- [ ] `manifest.json` exists in build output
- [ ] CSS files load (200 status)
- [ ] JS files load (200 status)
- [ ] Images load (200 status)
- [ ] Cache headers present on assets
- [ ] Gzip compression working
- [ ] No 404 errors in browser console

## Related Documentation
- See `VITE_MANIFEST_FINAL_FIX.md` for volume issues
- See `PRODUCTION_BUILD_FIX_FINAL.md` for Node version issues
