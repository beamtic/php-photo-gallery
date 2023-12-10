#!/usr/bin/env bash

# Build the docker images
printf "\n   Building Docker images...\n\n"
sleep 3
docker-compose up --build

