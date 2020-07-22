#!/usr/bin/env bash

docker build -f docker/test/Compiler.Dockerfile -t compiler-test .
docker run \
  -v "$(pwd)"/docker/test/basil:/app/basil \
  -v "$(pwd)"/docker/test/generated:/app/generated \
  -it \
  compiler-test ./compiler --source=basil --target=generated

docker rm nginx-test
docker build -f docker/test/Nginx.Dockerfile -t nginx-test .
docker run -d --name nginx-test nginx-test

docker network create test-network
docker network connect test-network nginx-test
