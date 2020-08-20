#!/usr/bin/env bash

source "$(pwd)"/build/.image_data.sh

for IMAGE_NAME in "${IMAGE_NAMES[@]}"; do
  TARGET=$(echo ${IMAGE_NAME} | cut -d ':' -f 1 | cut -d '/' -f 2)
  echo "Building \"${IMAGE_NAME}\" with target \"${TARGET}\""

  DOCKER_BUILDKIT=1 docker build \
   --build-arg browser=${IMAGE_NAME} \
   --build-arg BUILDKIT_INLINE_CACHE=1 \
   --target=${TARGET} \
   -t ${IMAGE_NAME} .
done
