# Step-by-Step Build Log Collection

## For Detailed Debugging

The Dockerfiles now write comprehensive build logs that will help us identify exactly where the build is failing.

### On Your VPS

Run the following commands to build and extract the logs:

#### Step 1: Build Docker Image
```bash
cd /path/to/hpvtcrm
docker-compose -f compose.prod.yaml build --no-cache 2>&1 | tee docker-build.log
```

This will:
- Run the build
- Save all output to `docker-build.log`
- Show output on screen

#### Step 2: Check if Build Succeeded
After the build completes, you should see the Docker images listed:
```bash
docker images | grep hpvtcrm
```

#### Step 3: Extract Internal Build Logs

If the build succeeded, the container has detailed logs. Extract them:

**For php-fpm image:**
```bash
# Get the image ID
IMAGE_ID=$(docker images | grep hpvtcrm_php-fpm | awk '{print $3}' | head -1)

# Extract the detailed log
docker run --rm $IMAGE_ID cat /tmp/npm-build.log > npm-build.log

echo "Log extracted to: npm-build.log"
```

**For nginx image:**
```bash
# Get the image ID
IMAGE_ID=$(docker images | grep hpvtcrm_web | awk '{print $3}' | head -1)

# Extract the detailed log
docker run --rm $IMAGE_ID cat /tmp/nginx-build.log > nginx-build.log

echo "Log extracted to: nginx-build.log"
```

#### Step 4: Share the Logs

Send me these files:
1. **docker-build.log** - Full Docker Compose build output
2. **npm-build.log** - Detailed npm build steps (from php-fpm image)
3. **nginx-build.log** - Nginx build steps (optional, from nginx image)

### What the Logs Will Show

The logs include:

```
✓ Node and npm versions
✓ Pre-build directory structure
✓ npm ci output (all dependencies installed)
✓ npm run build full output
✓ Post-build directory structure
✓ Search results for:
  - manifest.json location
  - All .js files created
✓ Specific directory contents:
  - /var/www/dist
  - /var/www/public
  - /var/www/public/build
  - /var/www/bootstrap/ssr
✓ Move operations (if build output relocated)
✓ Final verification
```

### Common Issues These Logs Will Reveal

1. **npm ci fails** - Missing or locked dependencies
2. **npm run build fails** - Vite compilation error (missing imports, syntax errors)
3. **Build succeeds but output missing** - Vite creates output in different location
4. **Build succeeds, output exists but not moved** - The move operation needs debugging
5. **Permissions issue** - Can't read/write certain directories

### Quick Troubleshooting Commands

```bash
# Check if volumes are the problem
docker-compose -f compose.prod.yaml exec php-fpm mount | grep "/var/www"

# Check disk space
docker-compose -f compose.prod.yaml exec php-fpm df -h

# Check npm cache issues
docker-compose -f compose.prod.yaml exec php-fpm npm cache clean --force

# Rebuild (this might fix cache issues)
docker-compose -f compose.prod.yaml build --no-cache
```

### Automated Log Extraction Script

Or use this one-liner to extract all logs:

```bash
#!/bin/bash
echo "Extracting build logs..."

# Get image IDs
PHP_IMG=$(docker images | grep "php-fpm.*latest" | head -1 | awk '{print $3}')
NGINX_IMG=$(docker images | grep "nginx.*latest" | head -1 | awk '{print $3}')

# Extract logs
if [ ! -z "$PHP_IMG" ]; then
  echo "Extracting npm-build.log..."
  docker run --rm $PHP_IMG cat /tmp/npm-build.log > npm-build.log 2>/dev/null
  echo "✓ Done"
fi

if [ ! -z "$NGINX_IMG" ]; then
  echo "Extracting nginx-build.log..."
  docker run --rm $NGINX_IMG cat /tmp/nginx-build.log > nginx-build.log 2>/dev/null
  echo "✓ Done"
fi

echo ""
echo "Logs extracted. Share:"
ls -lh docker-build.log npm-build.log nginx-build.log 2>/dev/null
```

## What I Need From You

Send me:
1. The build logs (docker-build.log, npm-build.log, etc.)
2. Output of: `docker-compose -f compose.prod.yaml exec php-fpm npm --version`
3. Output of: `docker-compose -f compose.prod.yaml exec php-fpm node --version`
4. Output of: `ls -la resources/` (to verify resources exist)

With these logs, I can see exactly where the build is failing and provide a targeted fix.
