FROM webreactor/nginx-php:v0.0.1

RUN apt-get install \
    && apt-get install -y imagemagick php5-imagick
