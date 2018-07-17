# Docker PHP5.6 + Oci8-1.4.10
The main DOCKERFILE locate at `${this_repo}/web` which is creating a evironment with PHP5+Oci8

## How to start?

### 1. Install
Ensure [Docker](https://www.docker.com/) is installed

### 2. Clone
Clone this repository to your workstation

### 3. Create file from template
Create 3 files `vhost.conf` `vhost-ssl.conf` `php.ini` from the template file and place them at them same template folder
- `vhost.conf`:This file will replace `/etc/apache2/sites-available/000-default.conf`. Put your VirtualHost for Http configuration here.
- `vhost-ssl.conf`: This file will replace `/etc/ache2/sites-available/default-ssl.conf`. Put your Https VirtualHost configuration here.
- `php.ini`: This file will be placed at`/usr/local/etc/php/`. Put your php configuration at this file.
- *If you have your own certificate .crt/.key. Place it in `ssl_dev_certificate` with the name `localhost.crt` and `localhost.key`.*

### 4. Build the docker image
Run
```
cd ${this_repo}/web
docker build -f DOCKERFILE -t php5-oci8 .
```

# How to use the docker image?

### Mount Project code
* If you only need to host static php file, you can bind-mount your project file to `/app/web/${project_name}`. You will able to view your project at `https://localhost:${port_number}/${project_name}`.
* If you are working on framework such as Laravel, you should bind-mount your project to `/app/source/${project_name}`. Then symbol link the `public` folder to `/app/web/${project_name}`.

```You can do this either ssh into the container or run the bash scrip when the container start```
* You will able to view your project at `https://localhost:${port_number}/${project_name}`.

### Example
The following example is the way I setup my Laravel project with this image. You can use `docker-compose.example.yml` `start.example.sh` as the template to create your own. (*You can get rid of `app-local-db` part if you don't need Oracle database setup*)


`docker-compose.yml`
```yaml
version: '3.3'

services:
    app-local-db:
        container_name: app-local-db
        build: ./db
        ports:
            - "49161:1521"
        restart: always
        volumes:
            - app-local-db-data:/var/lib/mysql
        environment:
            ORACLE_ALLOW_REMOTE: "true"

    app-local-web:
        depends_on:
            - app-local-db
        container_name: app-local-web
        build: ./web
        ports:
            - "8080:80"
            - "8081:443"
        restart: always
        volumes:
          - ../my_project:/app/source
        command: bash /app/source/start.sh
volumes:
    app-local-db-data:
```

`start.sh`: I place this in to source code repo.
```bash
# Place the code you want to run when the container start here.
ln -s /app/source/public /app/web/my_project
...
```