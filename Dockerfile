FROM php:7.4-cli

RUN apt-get update && apt-get install -y git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer global require phpmetrics/phpmetrics

ENV PATH="/root/.composer/vendor/bin:${PATH}"

WORKDIR /app

ENTRYPOINT ["phpmetrics"]
