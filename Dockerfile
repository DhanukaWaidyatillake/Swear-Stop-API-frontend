# Use the official PHP image as base
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apk update && apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    inotify-tools \
    npm \
    dcron \
    nginx \
    autoconf \
    gcc \
    g++ \
    make \
    musl-dev \
    libtool \
    gettext

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && php -v

# Install phpredis using pecl
RUN pecl install redis && docker-php-ext-enable redis

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html/

# Install PHP dependencies
RUN composer install --no-scripts

# Copy existing application code
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/public

# Remove the default Nginx configuration
RUN rm -f /etc/nginx/http.d/default.conf

# Copy custom Nginx configuration template
COPY nginx.conf.template /etc/nginx/http.d/default.conf.template

# Copy the start script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Add the cron job
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" >> /etc/cron.d/laravel-cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/laravel-cron

# Apply cron job
RUN crontab /etc/cron.d/laravel-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Start the application
CMD ["/usr/local/bin/start.sh"]
