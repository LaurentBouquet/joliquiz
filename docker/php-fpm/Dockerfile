FROM php:7.4-fpm-alpine
RUN apk --update --no-cache add git postgresql-dev
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; bin/console doctrine:migrations:migrate -n ;  bin/console doctrine:fixtures:load -n ; php-fpm
EXPOSE 9000
