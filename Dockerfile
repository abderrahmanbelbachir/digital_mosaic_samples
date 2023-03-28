FROM composer:1.9.0 as build
WORKDIR /app
COPY . /app
RUN composer global require hirak/prestissimo && composer install
FROM php:7.3-apache-stretch
RUN apt-get update -y && apt-get install -y libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev libxpm-dev
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-install gd
EXPOSE 8080
COPY --from=build /app /var/www/

COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .env.example /var/www/.env
RUN chmod 777 -R /var/www/storage/ && \
    echo "Listen 8080" >> /etc/apache2/ports.conf && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite