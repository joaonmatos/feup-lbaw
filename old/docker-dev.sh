#!/bin/bash
docker run -d --name 2032-dev -p 8000:80 \
    --mount type=bind,source="$(pwd)"/src,target=/var/www/html \
    php:7.4-apache
    