#==============================================================================
# Stage 1: Build PHP Dependencies
#==============================================================================
# Installs PHP extensions and Composer dependencies
FROM php:8.2-fpm AS builder

# Install system dependencies and PHP extensions
# - Build tools and libraries for compiling PHP extensions
# - Database drivers (MySQL, PostgreSQL)
# - Common Laravel requirements (GD, Intl, Zip, etc.)
# - Node.js and npm for building frontend assets
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
    ca-certificates \
    gnupg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
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

# Install Node.js LTS from NodeSource repository (better than Debian's outdated version)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /var/www

# Copy application code
# Note: Full codebase is needed before composer install because Laravel's
# post-autoload-dump scripts may execute Artisan commands that require
# the complete application structure
COPY . /var/www

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# Install PHP dependencies
RUN composer install \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --prefer-dist

# Asset building is now handled by nginx container only
# This keeps PHP container focused on application logic

# Install Node.js dependencies and build frontend assets
RUN npm ci && \
    npm run build && \
    if [ -f /var/www/public/build/manifest.json ]; then \
        echo "✓ manifest.json found in public/build"; \
        echo "=== Build Manifest (first 20 lines) ===" && \
        head -20 /var/www/public/build/manifest.json && \
        echo ""; \
        echo "=== Build artifacts ===" && \
        find /var/www/public/build -type f | wc -l && echo "files total"; \
    else \
        if [ -d /var/www/dist ]; then \
            mkdir -p /var/www/public && \
            mv /var/www/dist /var/www/public/build; \
        else \
            echo "✗ ERROR: Build failed! manifest.json not found"; \
            echo "=== Checking for build output ==="; \
            ls -laR /var/www/public/ 2>/dev/null || echo "public/ not found"; \
            echo "=== Checking for dist ==="; \
            ls -laR /var/www/dist/ 2>/dev/null || echo "dist/ not found"; \
            exit 1; \
        fi \
    fi

#==============================================================================
# Stage 2: Production Runtime
#==============================================================================
FROM php:8.2-fpm AS production

# Install runtime libraries only (no build tools)
# - libfcgi-bin & procps: Required for php-fpm-healthcheck
# - curl, git, unzip: Runtime utilities
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfcgi-bin \
    procps \
    curl \
    git \
    unzip \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP-FPM health check script
RUN curl -o /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck

# Copy initialization scripts
COPY ./docker/production/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY ./docker/production/php-fpm/check-db.php /usr/local/bin/check-db.php
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy initial storage structure for first-time initialization
COPY ./storage /var/www/storage-init

# Copy compiled PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

# Use production PHP configuration
# Optimized settings for performance and security
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Enable PHP-FPM status page for health checks
RUN sed -i '/\[www\]/a pm.status_path = /status' /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy application code and Composer dependencies from builder
# Note: Assets are built and served by the nginx container, not here
COPY --from=builder /var/www /var/www

# Set working directory
WORKDIR /var/www

# Set correct permissions
RUN chown -R www-data:www-data /var/www

# Entrypoint runs as root for initialization, then switches to www-data
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose PHP-FPM port
EXPOSE 9000
CMD ["php-fpm"]

#==============================================================================
# Stage 3: Development Environment
#==============================================================================
# Extends production with Xdebug and development tools
FROM production AS development

# Xdebug configuration arguments
ARG XDEBUG_ENABLED=true
ARG XDEBUG_MODE=develop,coverage,debug,profile
ARG XDEBUG_HOST=host.docker.internal
ARG XDEBUG_IDE_KEY=DOCKER
ARG XDEBUG_LOG=/dev/stdout
ARG XDEBUG_LOG_LEVEL=0

USER root

# Install and configure Xdebug for debugging
RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && { \
        echo "xdebug.mode=${XDEBUG_MODE}"; \
        echo "xdebug.idekey=${XDEBUG_IDE_KEY}"; \
        echo "xdebug.log=${XDEBUG_LOG}"; \
        echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}"; \
        echo "xdebug.client_host=${XDEBUG_HOST}"; \
        echo "xdebug.start_with_request=yes"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

# User/Group synchronization for file permissions
ARG UID=1000
ARG GID=1000

# Create user matching host UID/GID to avoid permission issues
RUN if getent group ${GID}; then \
    group_name=$(getent group ${GID} | cut -d: -f1); \
    useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
    groupadd -g ${GID} www \
    && useradd -m -u ${UID} -g www -s /bin/bash www; \
    group_name=www; \
    fi

# Update PHP-FPM to run as the new user
RUN sed -i "s/user = www-data/user = www/g" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/group = www-data/group = $group_name/g" /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www

# Copy development entrypoint
COPY ./docker/development/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Switch to non-privileged user
USER www-data

# Use development entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose PHP-FPM port
EXPOSE 9000
CMD ["php-fpm"]
