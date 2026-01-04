FROM php:8.2-fpm

# Install Node
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

WORKDIR /var/www/html

# Copy repo
COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Install JS deps & build
RUN npm install
RUN npm run build

CMD ["php-fpm"]
