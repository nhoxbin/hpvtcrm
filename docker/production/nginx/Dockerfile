#==============================================================================
# Stage 1: Build Frontend Assets
#==============================================================================
# Use Node.js 25 on Debian bookworm (slim) for building assets
FROM node:25-bookworm-slim AS builder

# Set working directory for the build
WORKDIR /var/www

# Copy package files for dependency installation
COPY package*.json ./

# Install Node.js dependencies
# - Use clean install for production builds
# - Ensures reproducible builds with package-lock.json
RUN npm ci --only=production=false

# Copy application source code
COPY . .

# Build frontend assets
# - Compiles Vue components and CSS
# - Generates optimized production bundles
# - Creates SSR build for server-side rendering
RUN npm run build

#==============================================================================
# Stage 2: Nginx Production Server
#==============================================================================
# Use Alpine-based Nginx for a lightweight production image
FROM nginx:alpine

# Copy custom Nginx configuration
# - Optimized for serving Laravel applications
# - Configures proper routing for SPA and API endpoints
COPY ./docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy built assets from builder stage
# - Only includes compiled frontend assets (JS, CSS, images)
# - Keeps final image size minimal
COPY --from=builder /var/www/public /var/www/public

# Set working directory
WORKDIR /var/www/public

# Expose HTTP port
EXPOSE 80

# Start Nginx in foreground mode
CMD ["nginx", "-g", "daemon off;"]
