# Stage 1: Build modern CSS/JS assets using Node 20
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production PHP + Nginx image
FROM richarvey/nginx-php-fpm:latest
WORKDIR /var/www/html

# Copy all application files
COPY . .

# Copy compiled assets from Stage 1
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Image configuration
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel production settings
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Start the container
CMD ["/start.sh"]
