FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip git curl gnupg zip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring \
    && a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY laravel.conf /etc/apache2/sites-available/laravel.conf
RUN a2dissite 000-default.conf \
    && a2ensite laravel.conf

EXPOSE 80

CMD ["apache2-foreground"]
