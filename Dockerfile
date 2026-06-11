FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libwebp-dev libzip-dev zip unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mysqli gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# NUCLEAR MPM FIX — hapus semua, tulis langsung (bukan symlink)
RUN find /etc/apache2/mods-enabled -name "mpm_*" -delete \
    && echo "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so" \
       > /etc/apache2/mods-enabled/mpm_prefork.load \
    && printf "<IfModule mpm_prefork_module>\nStartServers 5\nMinSpareServers 5\nMaxSpareServers 10\nMaxRequestWorkers 150\nMaxConnectionsPerChild 0\n</IfModule>" \
       > /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite headers deflate expires

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/shasea.ini

WORKDIR /var/www/html
COPY . /var/www/html/

RUN mkdir -p /var/www/html/assets/images/products \
             /var/www/html/assets/images/categories \
             /var/www/html/assets/images/banners \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/assets/images

# Entrypoint — tulis line by line (hindari semua escape/CRLF issue)
RUN echo '#!/bin/bash' > /entrypoint.sh \
    && echo 'PORT=${PORT:-80}' >> /entrypoint.sh \
    && echo 'sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf' >> /entrypoint.sh \
    && echo 'sed -i "s/*:80/*:${PORT}/g" /etc/apache2/sites-available/000-default.conf' >> /entrypoint.sh \
    && echo 'exec apache2-foreground' >> /entrypoint.sh \
    && chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/bin/bash", "/entrypoint.sh"]
