FROM php:apache
RUN a2enmod rewrite

# Installs "mysqli" library.
RUN docker-php-ext-install mysqli

EXPOSE 80/tcp
EXPOSE 443/tcp
