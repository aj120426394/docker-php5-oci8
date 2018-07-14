FROM php:5.6-apache

ENV LD_LIBRARY_PATH /usr/local/instantclient_12_1/

RUN apt-get update && apt-get install -y apt-utils unzip libaio1 build-essential && apt-get clean -y && rm -r /var/lib/apt/lists/*

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
RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8-1.4.10 && docker-php-ext-enable oci8
# RUN echo "extension=oci8.so" > /usr/local/etc/php/conf.d/oci8.ini
RUN echo "extension=oci8.so" > /usr/local/etc/php/php.ini


# RUN echo "<?php echo phpinfo(); ?>" > /app/index.php
