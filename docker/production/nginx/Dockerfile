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
RUN npm ci && \
    npm run build && \
    mkdir -p /var/www/public/build && \
    if [ -d /var/www/dist ]; then \
        cp -r /var/www/dist/* /var/www/public/build/; \
        rm -rf /var/www/dist; \
    fi && \
    echo "✓ Build stage complete. Checking for manifest..." && \
    if [ -f /var/www/public/build/manifest.json ]; then \
        echo "✓ manifest.json found"; \
    else \
        echo "✗ ERROR: manifest.json not found!"; \
        ls -la /var/www/public/build/ || echo "build directory not found"; \
        exit 1; \
    fi

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
