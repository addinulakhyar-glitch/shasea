# ============================================================
#  SHASEA — Dockerfile for Railway.app
#  PHP 8.2 + Apache
# ============================================================
FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Fix MPM: hapus SEMUA mpm symlink, enable prefork saja
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -s /etc/apache2/mods-available/mpm_prefork.load \
             /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s /etc/apache2/mods-available/mpm_prefork.conf \
             /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite headers deflate expires

# Copy konfigurasi
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/shasea.ini

# Set working directory
WORKDIR /var/www/html

# Copy semua file project
COPY . /var/www/html/

# Permission
RUN mkdir -p /var/www/html/assets/images/products \
             /var/www/html/assets/images/categories \
             /var/www/html/assets/images/banners \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/assets/images

# Entrypoint untuk Railway dynamic PORT
RUN printf '#!/bin/bash\nPORT=${PORT:-80}\nsed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf\nsed -i "s/*:80/*:$PORT/g" /etc/apache2/sites-available/000-default.conf\nexec apache2-foreground\n' > /entrypoint.sh \
    && chmod +x /entrypoint.sh

EXPOSE 80
CMD ["/bin/bash", "/entrypoint.sh"]
