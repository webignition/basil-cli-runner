#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

if ! (docker run -it ${BASE_IMAGE_NAME} ./bin/runner --version); then
  echo "Base image --version failed"
  exit 1
fi

echo "Base image --version successful"

if ! (docker run -it ${BASE_IMAGE_NAME} ./bin/runner --path=bin); then
  echo "Base image run failed"
  exit 1
fi

echo "Base image run successful"

exit 0
