FROM php:8.5.2-cli-alpine

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . .

CMD ["php", "-S", "0.0.0.0:8030"]
