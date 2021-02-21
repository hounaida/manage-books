docker-symfony
==============

[![Build Status](https://secure.travis-ci.org/eko/docker-symfony.png?branch=master)](http://travis-ci.org/eko/docker-symfony)


This is a complete stack for running Symfony 5.2 into Docker containers using docker-compose tool.

# Installation

1. Clone this repository:

    ```bash
    $ git clone https://github.com/hounaida/manage-books.git
    ```

2. Add `symfony.localhost` in your `/etc/hosts` file.

3. Make sure you adjust `DATABASE_URL` in `symfony/.env` file.

4. Run containers

    ```bash
    $ docker-compose up -d
    ```

**Note :** you can rebuild all Docker images by running:

```bash
$ docker-compose build
```

# How it works?

Here are the `docker-compose` built images:

* `db`: This is the MySQL database container (can be changed to postgresql or whatever in `docker-compose.yml` file),
* `php`: This is the PHP-FPM container including the application volume mounted on,
* `nginx`: This is the Nginx webserver container in which php volumes are mounted too,
* `redis`: This is the Redis container.


This results in the following running containers:

```bash
> $ docker-compose ps
        Name                       Command               State              Ports
--------------------------------------------------------------------------------------------
docker-symfony_db_1      docker-entrypoint.sh     Up      0.0.0.0:3306->3306/tcp
                         --def ...                        , 33060/tcp           
docker-symfony_nginx_1   nginx                    Up      443/tcp,              
                                                          0.0.0.0:80->80/tcp    
docker-symfony_php_1     php-fpm7 -F              Up      0.0.0.0:9000->9001/tcp
docker-                  /docker-entrypoint.sh    Up      0.0.0.0:8080->80/tcp  
symfony_phpmyadmin_1     apac ...                                               
docker-symfony_redis_1   docker-entrypoint.sh     Up      0.0.0.0:6379->6379/tcp
                         redis ...                                              
```
# Read project
```bash cd symfony
$ docker exec -it docker-symfony_php_1 /bin/sh
```
   
# Composer Install
```bash
$ composer install
```
You are done, you can visit your Symfony application on the following URL: `http://symfony.localhost`.

# Execute a migration
```bash
$ bin/console doctrine:migrations:migrate
```

# Load data fixtures to your database
```bash
$ bin/console doctrine:fixtures:load
```

# Tests
```bash
$ bin/phpunit
```

# Redis
login symfony.localhost/login 
* `login: hounaida`
* `password: houanida`
```bash
$ docker exec -it docker-symfony_redis_1 /bin/sh
$ redis-cli
$ keys *
```

# Read logs

You can access Nginx and Symfony application logs in the following directories on your host machine:

* `logs/nginx`
* `logs/symfony`
