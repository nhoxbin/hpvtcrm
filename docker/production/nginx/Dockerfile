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
COPY . /var/www

# Install PHP dependencies (composer install)
RUN composer install \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist

# Stage 2: Build assets
FROM node:20-alpine AS builder

# Set working directory
WORKDIR /var/www

# Copy vendor from PHP builder (needed for Ziggy Vue)
COPY --from=php-builder /var/www/vendor ./vendor

# Copy package files for dependency installation
COPY package*.json ./

# Copy Laravel application code (need all files for Vite)
COPY . /var/www

# Install Node.js dependencies (including dev deps needed for build) and build assets
RUN set -e && \
    BUILD_LOG="/tmp/nginx-build.log" && \
    { \
        echo "============================================"; \
        echo "NGINX BUILD DIAGNOSTIC LOG"; \
        echo "Date: $(date)"; \
        echo "============================================"; \
        echo ""; \
        echo "=== SYSTEM INFO ==="; \
        echo "Node version: $(node --version)"; \
        echo "NPM version: $(npm --version)"; \
        echo ""; \
        echo "=== Before Build - Directory Structure ==="; \
        find /var/www -maxdepth 2 -type d | sort; \
        echo ""; \
        echo "=== Step 1: npm ci ==="; \
        npm ci 2>&1; \
        echo "npm ci exit code: $?"; \
        echo ""; \
        echo "=== Step 2: npm run build ==="; \
        npm run build 2>&1; \
        BUILD_EXIT=$?; \
        echo "npm run build exit code: $BUILD_EXIT"; \
        echo ""; \
        echo "=== After Build - Directory Structure ==="; \
        find /var/www -maxdepth 3 -type d | sort; \
        echo ""; \
        echo "=== Check specific directories ==="; \
        echo "--- /var/www/dist (if exists) ---"; \
        ls -la /var/www/dist 2>/dev/null || echo "NOT FOUND"; \
        echo ""; \
        echo "--- /var/www/public/build (if exists) ---"; \
        ls -la /var/www/public/build 2>/dev/null || echo "NOT FOUND"; \
        echo ""; \
        echo "=== Move build output if needed ==="; \
        if [ ! -d /var/www/public/build ]; then \
            if [ -d /var/www/dist ]; then \
                echo "Moving /var/www/dist → /var/www/public/build"; \
                mkdir -p /var/www/public; \
                mv /var/www/dist /var/www/public/build; \
            fi; \
        fi && \
        if [ -d /var/www/public/build ]; then \
            echo "✓ Build directory ready"; \
            ls -lR /var/www/public/build; \
        else \
            echo "✗ ERROR: Build directory not found"; \
            exit 1; \
        fi; \
        echo ""; \
        echo "============================================"; \
        echo "BUILD LOG COMPLETE"; \
        echo "============================================"; \
    } | tee "$BUILD_LOG" && \
    echo "Build log saved to: $BUILD_LOG"

# Stage 2: Nginx production image
FROM nginx:alpine

# Copy custom Nginx configuration
# -----------------------------------------------------------
# Replace the default Nginx configuration with our custom one
# that is optimized for serving a Laravel application.
# -----------------------------------------------------------
COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy Laravel's public assets from the builder stage
# -----------------------------------------------------------
# We only need the 'public' directory from our Laravel app.
# -----------------------------------------------------------
COPY --from=builder /var/www/public /var/www/public

# Set the working directory to the public folder
WORKDIR /var/www/public

# Expose port 80 and start Nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
