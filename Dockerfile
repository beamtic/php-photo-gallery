FROM ubuntu:22.04

# About
LABEL maintainer="seat@beamtic.com"
LABEL version="0.1"
LABEL description="PHP Photo Gallery on Ubuntu, Apache, and PHP 8"

ENV PROJECT_ROOT=/var/www/phpphotogallery

# Disable prompting of user when installing packages
ARG DEBIAN_FRONTEND=noninteractive

# Set the default shell
SHELL ["/bin/bash", "-c"]

RUN apt -y update && apt -y upgrade && apt -y install \
sudo wget jq curl nano php apache2 php-curl php-dom php-gd php-json \
php-mbstring php-zip php-fpm php-intl php-opcache supervisor git

# Create the root folder
RUN mkdir /var/www/phpphotogallery 

# Add correct permissions
RUN chown www-data:www-data -R /var/www/phpphotogallery && \
chmod 775 -R /var/www/phpphotogallery

# Make sure the "/var/run/php" directory exists so that PHP-FPM can create the pid/sock file
RUN mkdir -p /var/run/php

# Copy configuration files to container
COPY ./config/etc /etc

RUN a2enmod headers rewrite proxy proxy_fcgi

RUN a2ensite phpphotogallery

WORKDIR $PROJECT_ROOT

EXPOSE 80 443

# Add console start script that shows info on login
ADD ./container/console-start.sh /console-start.sh
RUN chmod +x /console-start.sh

# Add the entrypoint script to make certain modifications
ADD ./container/entrypoint.sh /container-start.sh
RUN bash /container-start.sh
# ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]
# Run supervisored
CMD ["/usr/bin/supervisord"]