# Fix: Website Serving Old Build Files (Cache Issues)

## Problem
After running `npm run build`, the website continues to serve old files from the previous build instead of the new ones. Old CSS/JS don't match new build.

## Root Causes Found

### 1. **Nginx Caching Header Issues** ❌
**Before:**
```nginx
location /build/ {
    expires 1y;                           # Cache for 1 YEAR!
    add_header Cache-Control "public, immutable";
}
```

This tells browsers to cache **EVERYTHING** for 1 year, including:
- `manifest.json` (indexes all other files)
- Static CSS/JS files with old names

Even if you rebuild, browser still has old manifest!

**After:**
```nginx
# manifest.json must always be fresh
location ~* /build/manifest\.json$ {
    expires -1;                           # Never cache
    add_header Cache-Control "no-cache, no-store, must-revalidate";
}

# Hashed files (with build hash) safe to cache forever
location ~* /build/.*\.(js|css)$ {
    expires 1y;                           # Safe: filename has hash
    add_header Cache-Control "public, immutable";
}
```

### 2. **Multiple Asset Builders** ❌
**Before:**
- **nginx container** builds assets → `/var/www/public/build`
- **php-fpm container** also builds assets → `/var/www/public/build`

Result: Two separate build processes, potentially out of sync!

**After:**
- **Only nginx** builds assets (it serves them)
- **php-fpm** removed asset building (not its job)

### 3. **Old Build Files Not Cleaned** ❌
**Before:**
```dockerfile
RUN npm ci && \
    npm run build && \
    if [ -d /var/www/dist ]; then \
        cp -r /var/www/dist/* /var/www/public/build/;  # ← Old files still there!
```

If Vite build added new files but couldn't overwrite old ones, both versions exist!

**After:**
```dockerfile
RUN rm -rf /var/www/dist /var/www/public/build && \  # Clean first!
    npm ci && \
    npm run build && \
    cp -r /var/www/dist /var/www/public/build
```

---

## How It Works Now

### Build Process Flow
```
Docker Build:
┌─────────────────────────────────────┐
│ nginx Builder Stage                 │
│                                     │
│ 1. rm -rf dist/ build/  (CLEAN!)    │
│ 2. npm ci                           │
│ 3. npm run build (new hashes)       │
│ 4. cp dist/ → public/build/         │
│                                     │
│ Result: Fresh build, old files gone │
└─────────────────────────────────────┘
         ↓
         ✓ manifest.json has NEW file hashes
         ✓ app.123abc.js (new hash)
         ✓ app.456def.css (new hash)
```

### Cache Strategy (Smart Hash-Based)
```
Browser Request Flow:
┌──────────────────────────────────────────────────┐
│ 1. Browser requests / (index page)               │
│    → manifest.json (NO cache - always fresh)     │
│    → Reads: app -> app.123abc.js (new hash)      │
│                                                  │
│ 2. Browser checks cache for app.123abc.js        │
│    → NOT in cache (different hash than before!)  │
│    → Requests it from server                     │
│    → Gets new file (Cache: 1 year)               │
│                                                  │
│ 3. Old app.456def.js still in browser cache      │
│    → But manifest no longer references it        │
│    → Never requested again ✓                     │
└──────────────────────────────────────────────────┘
```

---

## Why This Matters

### Before (Problems)
```
Build 1: app.js, style.css
         ↓
Browser caches for 1 year

Build 2: app.js, style.css (same names)
         ↓
Browser already has cached version → NEVER REQUESTS NEW FILES!

Result: Old build files served forever (until cache expires in 1 year) ❌
```

### After (Fixed)
```
Build 1: app.123abc.js, style.456def.css
         manifest.json (no cache)
         ↓
Browser caches versioned files

Build 2: app.789ghi.js, style.012jkl.css  (different names!)
         manifest.json (fresh - no cache) ← Key difference!
         ↓
Browser checks manifest → sees new hashes
         → Requests app.789ghi.js (not cached)
         → Gets new version ✓

Result: New builds served immediately ✓
```

---

## Deployment

### Step 1: Update Code
```bash
cd /path/to/hpvtcrm
git pull origin main
```

### Step 2: Rebuild All Docker Images (Important!)
```bash
# Rebuild both nginx and php-fpm
docker-compose -f compose.prod.yaml build --no-cache
```

### Step 3: Restart Services
```bash
docker-compose -f compose.prod.yaml down
docker-compose -f compose.prod.yaml up -d
```

### Step 4: Verify
```bash
# Check manifest is fresh (no cache headers)
docker-compose -f compose.prod.yaml exec nginx \
    cat /var/www/public/build/manifest.json

# Should show new file hashes if you rebuild
```

---

## Testing Before/After

### Browser DevTools Test
1. Open app in browser
2. Press `F12` (DevTools)
3. Go to **Network** tab
4. Refresh page
5. Look for `manifest.json` request:
   - **Before Fix:** Shows `Cache-Control: public, immutable` (wrong!)
   - **After Fix:** Shows `Cache-Control: no-cache, no-store, must-revalidate` ✓

### Check File Hashes
```bash
# After rebuild, check if files have version hashes
docker-compose -f compose.prod.yaml exec nginx ls /var/www/public/build/

# Should show files like:
# app.6a7b8c9d.js    ← Hash here
# app.e1f2g3h4.css   ← Hash here
# manifest.json      ← No hash (content-addressed instead)
```

---

## Files Changed

| File | Change |
|------|--------|
| [docker/production/nginx/nginx.conf](docker/production/nginx/nginx.conf) | Smart cache headers: manifest.json never cached, hashed files cached forever |
| [docker/production/nginx/Dockerfile](docker/production/nginx/Dockerfile) | Clean old builds before building, verify manifest exists |
| [docker/common/php-fpm/Dockerfile](docker/common/php-fpm/Dockerfile) | Removed asset building (only nginx builds now) |

---

## Troubleshooting

### Still seeing old files?

**Check 1: Browser cache**
```
Hard refresh: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)
Or: Open DevTools → Network → Disable cache → Refresh
```

**Check 2: Nginx config reloaded?**
```bash
docker-compose -f compose.prod.yaml exec nginx nginx -t
# Should output: syntax is ok, test is successful
```

**Check 3: Images rebuilt?**
```bash
# If you only ran 'up' without rebuilding, old images still running
docker-compose -f compose.prod.yaml build --no-cache
docker-compose -f compose.prod.yaml up -d
```

**Check 4: manifest.json was updated?**
```bash
# After rebuild, check timestamp and content
stat /var/www/public/build/manifest.json

# Hash in manifest should be DIFFERENT from before
cat /var/www/public/build/manifest.json | grep -o '"app[^"]*"'
```

### manifest.json missing?
```bash
# Check if build actually ran
docker-compose -f compose.prod.yaml build --no-cache 2>&1 | grep -A5 "=== Building Frontend"

# If no output, build might have been skipped (cached layers)
# Force rebuild:
docker-compose -f compose.prod.yaml build --no-cache nginx
```

---

## Cache Headers Explained

| Location | Headers | Reason |
|----------|---------|--------|
| `manifest.json` | `no-cache, no-store, must-revalidate` | Always request fresh to get new file hashes |
| Hashed JS/CSS<br/>(`app.123abc.js`) | `public, immutable`<br/>`expires 1y` | Filename is unique, safe to cache forever |
| Regular assets<br/>(images, fonts) | `public`<br/>`expires 30d` | Balance freshness with performance |
| HTML pages (`/`) | `no-cache`<br/>`must-revalidate` | Check for changes but can revalidate with server |

---

## Related Issues Fixed
- ❌ Nginx misconfiguration (php-fpm reference)
- ❌ Multiple asset builders (now only nginx)
- ❌ Old build files accumulation
- ❌ manifest.json being cached (now always fresh)
- ✅ Browser caching old files
- ✅ New builds served immediately
