#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

for IMAGE_NAME in "${IMAGE_NAMES[@]}"; do
  TARGET=$(echo ${IMAGE_NAME} | cut -d ':' -f 1 | cut -d '/' -f 2)
  echo "Building \"${IMAGE_NAME}\" with target \"${TARGET}\""

  DOCKER_BUILDKIT=1 docker build \
   -f docker/Dockerfile \
   --build-arg browser=${IMAGE_NAME} \
   --target=${TARGET} \
   -t ${IMAGE_NAME} .
done
