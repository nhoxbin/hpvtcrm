#!/bin/bash
# Extract Docker build logs for debugging

echo "Building Docker image and capturing logs..."
echo ""

# Build and capture the build output
docker-compose -f compose.prod.yaml build --no-cache 2>&1 | tee docker-build-output.log

echo ""
echo "=========================================="
echo "Build complete. Output saved to:"
echo "  - docker-build-output.log (full build output)"
echo ""
echo "If build succeeded, try to extract logs from container:"
echo ""
echo "  1. First, identify the php-fpm image:"
echo "     docker images | grep php-fpm"
echo ""
echo "  2. Run a temporary container to extract logs:"
echo "     docker run --rm <image-id> cat /tmp/npm-build.log > npm-build.log"
echo ""
echo "  3. Or for nginx:"
echo "     docker run --rm <image-id> cat /tmp/nginx-build.log > nginx-build.log"
echo ""
echo "  4. Share these log files:"
echo "     - docker-build-output.log"
echo "     - npm-build.log (if available)"
echo "     - nginx-build.log (if available)"
echo ""
echo "=========================================="
