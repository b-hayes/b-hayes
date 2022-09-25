#!/usr/bin/env bash

# Lets use the project folder as the name for out docker container.
PROJECT=`basename $(dirname $(pwd))`

# Empty ports by default lets docker assign any port that is free so we avoid collisions.
PORTS="8080:80"

# Run docker
docker run -dit --name $PROJECT -p $PORTS \
  -v `pwd`:/var/www/html \
  -v `pwd`/docker/apache.conf:/etc/apache2/sites-enabled/000-default.conf \
  php:8.1-apache

# Show the user the container info
docker ps | grep $PROJECT
