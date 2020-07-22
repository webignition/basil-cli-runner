#!/usr/bin/env bash

IMAGE_REPOSITORY="smartassert/runner"

IMAGE_DEFAULT_TAG="${TRAVIS_BRANCH:-master}"
IMAGE_TAG="${1:-${IMAGE_DEFAULT_TAG}}"

IMAGE_NAME=${IMAGE_REPOSITORY}:${IMAGE_TAG}

echo "Image name: "${IMAGE_NAME}
