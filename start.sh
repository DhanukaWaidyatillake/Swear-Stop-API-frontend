#!/bin/sh

# Substitute environment variables in the Nginx config
envsubst '$FPM_HOST' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

# Start PHP-FPM
php-fpm &

# Start Nginx
nginx -g 'daemon off;' &

# Start cron
crond -f
