# cspell:ignore libzip Laravel libjpeg libfreetype freetype
FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libsqlite3-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_sqlite zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

ENV DB_CONNECTION=sqlite \
    DB_DATABASE=/app/database/database.sqlite

# Copy project
COPY . .

# Ensure the SQLite database exists before the startup migration runs
RUN mkdir -p \
        database \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R ug+rwX storage bootstrap/cache database

# Install Laravel dependencies (before npm build so vendor files exist)
RUN composer install --optimize-autoloader --no-dev

# Install Node dependencies
RUN npm install --legacy-peer-deps

# Build frontend assets
RUN npm run build

# Create startup script
RUN echo '#!/bin/bash\n\
mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache\n\
touch database/database.sqlite\n\
chmod -R ug+rwX storage bootstrap/cache database\n\
php artisan migrate --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
php artisan serve --host=0.0.0.0 --port=10000' > /app/startup.sh && chmod +x /app/startup.sh

# Expose port
EXPOSE 10000

# Start server with migrations
CMD ["/app/startup.sh"]
