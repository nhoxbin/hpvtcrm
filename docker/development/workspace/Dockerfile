#==============================================================================
# Development Workspace Container
#==============================================================================
# Interactive environment for running development commands:
# - PHP CLI with extensions
# - Composer for dependency management
# - Node.js/NPM via NVM for frontend builds
# - Xdebug for debugging CLI scripts
#==============================================================================

FROM php:8.2-cli

#------------------------------------------------------------------------------
# Build Arguments
#------------------------------------------------------------------------------
ARG UID=1000
ARG GID=1000
ARG NODE_VERSION=25.0.0

#------------------------------------------------------------------------------
# Install System Dependencies & PHP Extensions
#------------------------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    sudo \
    git \
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
        gd \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        intl \
        zip \
        bcmath \
        soap \
    && pecl install redis xdebug \
    && docker-php-ext-enable gd redis xdebug \
    && curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#------------------------------------------------------------------------------
# Configure Xdebug
#------------------------------------------------------------------------------
ARG XDEBUG_ENABLED
ARG XDEBUG_MODE
ARG XDEBUG_HOST
ARG XDEBUG_IDE_KEY
ARG XDEBUG_LOG
ARG XDEBUG_LOG_LEVEL

RUN if [ "${XDEBUG_ENABLED}" = "true" ]; then \
    docker-php-ext-enable xdebug \
    && { \
        echo "xdebug.mode=${XDEBUG_MODE}"; \
        echo "xdebug.idekey=${XDEBUG_IDE_KEY}"; \
        echo "xdebug.log=${XDEBUG_LOG}"; \
        echo "xdebug.log_level=${XDEBUG_LOG_LEVEL}"; \
        echo "xdebug.client_host=${XDEBUG_HOST}"; \
        echo "xdebug.start_with_request=yes"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

#------------------------------------------------------------------------------
# Create Development User
#------------------------------------------------------------------------------
# Create user matching host UID/GID to avoid permission issues with volumes
RUN if getent group ${GID}; then \
    useradd -m -u ${UID} -g ${GID} -s /bin/bash www; \
    else \
    groupadd -g ${GID} www \
    && useradd -m -u ${UID} -g www -s /bin/bash www; \
    fi \
    && usermod -aG sudo www \
    && echo 'www ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

#------------------------------------------------------------------------------
# Install Node.js via NVM
#------------------------------------------------------------------------------
# Switch to non-root user for NVM installation
USER www

# Install NVM and Node.js
RUN export NVM_DIR="$HOME/.nvm" \
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash \
    && [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" \
    && nvm install ${NODE_VERSION} \
    && nvm alias default ${NODE_VERSION} \
    && nvm use default

# Configure shell to load NVM automatically
RUN { \
        echo 'export NVM_DIR="$HOME/.nvm"'; \
        echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"'; \
        echo '[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"'; \
    } >> /home/www/.bashrc

#------------------------------------------------------------------------------
# Finalize Container Setup
#------------------------------------------------------------------------------
WORKDIR /var/www

# Override default PHP entrypoint for interactive shell
ENTRYPOINT []

# Keep container running for interactive commands
CMD ["bash"]
