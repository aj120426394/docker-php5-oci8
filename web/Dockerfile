FROM php:5.6-apache

ENV LD_LIBRARY_PATH /usr/local/instantclient_12_1/
ENV APACHE_DOCUMENT_ROOT /app/web/

RUN apt-get update && apt-get install -y apt-utils unzip libaio1 build-essential libpng-dev && apt-get clean -y && rm -r /var/lib/apt/lists/*
RUN docker-php-ext-install gd
# Oracle instantclient
ADD instantclient-basic-linux.x64-12.1.0.2.0.zip /tmp/
ADD instantclient-sdk-linux.x64-12.1.0.2.0.zip /tmp/
ADD instantclient-sqlplus-linux.x64-12.1.0.2.0.zip /tmp/


RUN unzip /tmp/instantclient-basic-linux.x64-12.1.0.2.0.zip -d /usr/local/
RUN unzip /tmp/instantclient-sdk-linux.x64-12.1.0.2.0.zip -d /usr/local/
RUN unzip /tmp/instantclient-sqlplus-linux.x64-12.1.0.2.0.zip -d /usr/local/
RUN ln -s /usr/local/instantclient_12_1 /usr/local/instantclient
RUN ln -s /usr/local/instantclient/libclntsh.so.12.1 /usr/local/instantclient/libclntsh.so
RUN ln -s /usr/local/instantclient/libocci.so.12.1 /usr/local/instantclient/libocci.so
RUN ln -s /usr/local/instantclient/sqlplus /usr/bin/sqlplus
#RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8-1.4.10
RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient_12_1 && docker-php-ext-install oci8
COPY templates/php.ini /usr/local/etc/php/

# Apache Configuration
RUN mkdir /app && mkdir /app/web && mkdir /app/source && mkdir /app/security\ key
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
COPY templates/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ssl_dev_certificate/localhost.crt /etc/ssl/localcerts/apache.crt
COPY ssl_dev_certificate/localhost.key /etc/ssl/localcerts/apache.key
COPY templates/vhost-ssl.conf /etc/apache2/sites-available/default-ssl.conf
COPY templates/login.php /app/web/
COPY templates/logout.php /app/web/
COPY ["templates/pingsinglesignon.php","/app/security key/.pingsinglesignon.php"]
COPY start.example.sh /app/start.sh


EXPOSE 80
EXPOSE 443

WORKDIR /tmp

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
# RUN php -r "unlink('composer-setup.php')"


RUN a2enmod rewrite \
    && a2enmod ssl

WORKDIR /etc/apache2/sites-available
RUN a2ensite default-ssl.conf

WORKDIR /app/source


