FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    nodejs \
    npm

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN touch database/database.sqlite

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000