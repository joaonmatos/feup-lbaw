#!/bin/bash

# Stop execution if a step fails
set -e

DOCKER_USERNAME=lbaw2032 # Replace by your docker hub username
IMAGE_NAME=lbaw2032-piu

docker build -t $DOCKER_USERNAME/$IMAGE_NAME .
docker push $DOCKER_USERNAME/$IMAGE_NAME
