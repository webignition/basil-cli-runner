#!/usr/bin/env bash

BASE_IMAGE_REPOSITORY="local/runner"

DEFAULT_TAG="${TRAVIS_BRANCH:-master}"
TAG="${1:-${DEFAULT_TAG}}"

BASE_IMAGE_NAME=${BASE_IMAGE_REPOSITORY}:${TAG}
echo "Image name: "${BASE_IMAGE_NAME}
