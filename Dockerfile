FROM composer AS composer
WORKDIR /app
ADD . /app
RUN composer install

FROM php:8-cli-alpine
WORKDIR /app
COPY --from=composer /app /app
RUN ./vendor/bin/phpcs --ignore=_build . \
        && ./vendor/bin/phpunit

FROM nginx
WORKDIR /usr/share/nginx/html
COPY --from=sphinx_build /app/_build/html /usr/share/nginx/html
