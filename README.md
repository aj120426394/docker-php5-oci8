# Docker PHP5.6 + Oci8-1.4.10
The main DOCKERFILE locate at `${this_repo}/web` which is creating a evironment with PHP5+Oci8

## How to start?

### 1. Install
Ensure [Docker](https://www.docker.com/) is installed

### 2. Clone
Clone this repository to your workstation

### 3. Modify template
Modify template file which will replace the original file in the environment
`vhost.conf`: /etc/apache2/sites-available/000-default.conf
`vhost-ssl.conf`" /etc/ache2/sites-available/default-ssl.conf
`php.ini`: /usr/local/etc/php/

### 4. Build the docker image
Run
```
cd ${this_repo}/web
docker build -f DOCKERFILE -t php5-oci8 .
```

# How to use the docker image?

### Mount Project code
If you only need to host static php file, you can bind-mount your project file to `/app/web/${project_name}`. You will able to view your project at `https://localhost:${port_number}/${project_name}`.
If you are working on framework such as Laravel, you should bind-mount your project to `/app/source/${project_name}`. Then symbol link the `public` folder to `/app/web/${project_name}`
```ln -s /app/source/${project_name}/public /app/web/${project_name}```
You will able to view your project at `https://localhost:${port_number}/${project_name}`.

### docker-compose.yml example
docker-compose.yml
```
version: '3.3'

services:
    php-apache:
        image: php5-oci8:latest
        ports:
            - "8080:80"
            - "8081:443"
        volumes:
            - .:/app/source
        command: bash /app/source/.docker/start.sh
```
start.sh
```
#!/bin/bash

ln -s /app/source/public /app/web/vlen


#!/bin/bash
set -e

# Note: we don't just use "apache2ctl" here because it itself is just a shell-script wrapper around apache2 which provides extra functionality like "apache2ctl start" for launching apache2 in the background.
# (also, when run as "apache2ctl <apache args>", it does not use "exec", which leaves an undesirable resident shell process)

: "${APACHE_CONFDIR:=/etc/apache2}"
: "${APACHE_ENVVARS:=$APACHE_CONFDIR/envvars}"
if test -f "$APACHE_ENVVARS"; then
    . "$APACHE_ENVVARS"
fi

# Apache gets grumpy about PID files pre-existing
: "${APACHE_RUN_DIR:=/var/run/apache2}"
: "${APACHE_PID_FILE:=$APACHE_RUN_DIR/apache2.pid}"
rm -f "$APACHE_PID_FILE"

# create missing directories
# (especially APACHE_RUN_DIR, APACHE_LOCK_DIR, and APACHE_LOG_DIR)
for e in "${!APACHE_@}"; do
    if [[ "$e" == *_DIR ]] && [[ "${!e}" == /* ]]; then
        # handle "/var/lock" being a symlink to "/run/lock", but "/run/lock" not existing beforehand, so "/var/lock/something" fails to mkdir
        #   mkdir: cannot create directory '/var/lock': File exists
        dir="${!e}"
        while [ "$dir" != "$(dirname "$dir")" ]; do
            dir="$(dirname "$dir")"
            if [ -d "$dir" ]; then
                break
            fi
            absDir="$(readlink -f "$dir" 2>/dev/null || :)"
            if [ -n "$absDir" ]; then
                mkdir -p "$absDir"
            fi
        done

        mkdir -p "${!e}"
    fi
done

exec apache2 -DFOREGROUND "$@"

```
