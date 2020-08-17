#!/usr/bin/env bash

source "$(pwd)"/build/.image_data.sh
source "$(pwd)"/deploy/docker_hub_login.sh

for IMAGE_NAME in "${IMAGE_NAMES[@]}"; do
  docker push ${IMAGE_NAME}
done
