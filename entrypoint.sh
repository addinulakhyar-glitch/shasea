#!/bin/bash
# Ganti port Apache sesuai PORT yang dikasih Railway
PORT=${PORT:-80}

sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/*:80/*:$PORT/g" /etc/apache2/sites-available/000-default.conf

echo "Apache starting on port $PORT"
exec apache2-foreground
