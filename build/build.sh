#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

docker build -f docker/Base.Dockerfile -t ${BASE_IMAGE_NAME} .
docker build -f docker/Chrome.Dockerfile -t ${CHROME_IMAGE_NAME} .
