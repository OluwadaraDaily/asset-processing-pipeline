FROM richarvey/nginx-php-fpm:3.1.6

# Install supervisor
RUN apk add --no-cache supervisor

COPY . .

# Make scripts executable
RUN chmod +x deploy/start.sh scripts/00-laravel-deploy.sh

# Copy supervisor config
COPY deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Expose ports
EXPOSE 80 8080

CMD ["./deploy/start.sh"]