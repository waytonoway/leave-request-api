# Use an official PHP image as a base image
FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    nano \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory to the Symfony project directory
WORKDIR /var/www/html

# Copy the Symfony project into the container
COPY . /var/www/html/

# Copy the custom Apache config
COPY leave_request_app.conf /etc/apache2/sites-available/000-default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN a2ensite 000-default.conf

RUN git config --global --add safe.directory /var/www/html

# Set permissions for the Symfony directories
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Expose port 80
EXPOSE 80

# Install Symfony dependencies
RUN composer install --no-scripts --no-autoloader --no-progress

# Run the Apache server
CMD ["apache2-foreground"]
