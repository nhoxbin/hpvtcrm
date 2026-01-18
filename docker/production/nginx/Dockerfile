#==============================================================================
# Stage 1: Build PHP Dependencies
#==============================================================================
# Use PHP for running Composer first
FROM php:8.2-fpm AS php-builder

# Install system dependencies and PHP extensions required by Laravel packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        opcache \
        intl \
        zip \
        bcmath \
        soap \
        gd \
    && pecl install redis \
    && docker-php-ext-enable redis gd \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.* ./

# Copy full application
COPY . .

# Install PHP dependencies (composer install)
RUN composer install \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist

#==============================================================================
# Stage 2: Build Frontend Assets
#==============================================================================
# Use Node.js 25 on Debian bookworm (slim) for building assets
FROM node:25-bookworm-slim AS builder

# Set working directory for the build
WORKDIR /var/www

# Copy vendor from PHP builder (needed for Ziggy Vue)
COPY --from=php-builder /var/www/vendor ./vendor

# Copy package files for dependency installation
COPY package*.json ./

# Install Node.js dependencies
# - Use clean install for production builds
# - Ensures reproducible builds with package-lock.json
RUN npm ci --only=production=false

# Copy application source code
COPY . .

# Show what we're working with
RUN echo "=== Before npm run build ===" && pwd && ls -la /var/www/ | head -20

# Build frontend assets with verbose output
RUN echo "=== Starting npm run build ===" && npm run build 2>&1 | tee /tmp/build.log && echo "=== Build completed ==="

# Show the output log
RUN echo "=== Build Log ===" && cat /tmp/build.log

# List everything created
RUN echo "=== AFTER BUILD - ALL FILES ===" && find /var/www -type f \( -name "*.js" -o -name "*.css" -o -name "manifest.json" \) -not -path "*/node_modules/*" -not -path "*/vendor/*" 2>/dev/null && \
    echo "=== Check key directories ===" && \
    echo "public folder:" && ls -la /var/www/public/ 2>/dev/null && \
    echo "public/build folder:" && ls -la /var/www/public/build/ 2>/dev/null

#==============================================================================
# Stage 2: Nginx Production Server
#==============================================================================
# Use Alpine-based Nginx for a lightweight production image
FROM nginx:alpine

# Install bash for debugging/troubleshooting
RUN apk add --no-cache bash curl

# Copy custom Nginx configuration
# - Optimized for serving Laravel applications
# - Configures proper routing for SPA and API endpoints
COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy built assets from builder stage
# - Only includes compiled frontend assets (JS, CSS, images)
# - Keeps final image size minimal
COPY --from=builder /var/www/public/build /var/www/public/build

# Ensure proper permissions
RUN chmod -R 755 /var/www/public 2>/dev/null || true && \
    echo "=== Contents of copied public folder ===" && \
    ls -la /var/www/public/ && \
    if [ -d "/var/www/public/build" ]; then echo "=== public/build exists ===" && ls -la /var/www/public/build/; fi

# Set working directory
WORKDIR /var/www/public

# Expose HTTP port
EXPOSE 80

# Start Nginx in foreground mode
CMD ["nginx", "-g", "daemon off;"]
