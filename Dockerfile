FROM php:8.2-alpine3.16 AS app
RUN apk add bash acl yq make git jq && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql opcache
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH $PATH:/root/.composer/vendor/bin
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
CMD ["symfony", "server:start", "--no-tls", "--port", "80"]

# Payment gateway service
FROM php:8.2-alpine3.16 AS payment-gateway
RUN apk add bash acl yq make git jq && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql opcache
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH $PATH:/root/.composer/vendor/bin
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
CMD ["symfony", "server:start", "--no-tls", "--port", "80"]

# Exchange service
FROM php:8.2-alpine3.16 AS exchange
RUN apk add bash acl yq make git jq && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql opcache
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH $PATH:/root/.composer/vendor/bin
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
CMD ["symfony", "server:start", "--no-tls", "--port", "80"]

# Transport company service
FROM php:8.2-alpine3.16 AS transport-company
RUN apk add bash acl yq make git jq && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql opcache
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH $PATH:/root/.composer/vendor/bin
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
CMD ["symfony", "server:start", "--no-tls", "--port", "80"]
