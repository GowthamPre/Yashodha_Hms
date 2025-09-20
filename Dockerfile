# Use official PHP 8.2 with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer (latest stable)
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (to cache dependencies)
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy rest of application
COPY . .

# Expose port 8000
EXPOSE 8000

# Run Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
