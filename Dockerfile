FROM php:8.2-apache

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Copy project ke dalam container
COPY . /var/www/html/

# Enable Apache rewrite module (jika pakai .htaccess)
RUN a2enmod rewrite
