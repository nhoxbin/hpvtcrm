#!/bin/bash
# Simple one-command build log extraction

set -e

cd /path/to/hpvtcrm

echo "======================================"
echo "Building Docker Image with Logs"
echo "======================================"
echo ""

# Build and save output
echo "Step 1: Building Docker image..."
docker-compose -f compose.prod.yaml build --no-cache 2>&1 | tee docker-build.log

echo ""
echo "Step 2: Extracting diagnostic logs from images..."

# Extract from php-fpm
echo "Extracting php-fpm build log..."
docker run --rm $(docker-compose -f compose.prod.yaml images -q php-fpm) cat /tmp/npm-build.log 2>/dev/null > npm-build.log || echo "npm-build.log not found"

# Extract from nginx (if available)
echo "Extracting nginx build log..."
docker run --rm $(docker-compose -f compose.prod.yaml images -q web) cat /tmp/nginx-build.log 2>/dev/null > nginx-build.log || echo "nginx-build.log not found"

echo ""
echo "======================================"
echo "Build Complete!"
echo "======================================"
echo ""
echo "Log files created:"
ls -lh docker-build.log npm-build.log nginx-build.log 2>/dev/null || true

echo ""
echo "Next steps:"
echo "1. Share these files with me:"
echo "   - docker-build.log"
echo "   - npm-build.log (if created)"
echo "   - nginx-build.log (if created)"
echo ""
echo "2. Or paste the contents of npm-build.log here"
echo ""
