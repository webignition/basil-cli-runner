#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

CONTAINER_NAME="test-runner-container"
CONTAINER_PORT="8000"
HOST_PORT="9001"

docker rm -f ${CONTAINER_NAME}
docker create -p ${HOST_PORT}:${CONTAINER_PORT} --name ${CONTAINER_NAME} ${IMAGE_NAME}
docker start ${CONTAINER_NAME}

sleep 0.1

EXECUTABLE="./bin/runner --version"

OUTPUT=$(( echo ${EXECUTABLE}; ) | nc localhost ${HOST_PORT})
if ! [[ "${OUTPUT}" =~ ^"0"."run dev-master" ]]; then
    echo "x" ${EXECUTABLE} "failed"

    exit 1
fi

echo "✓" ${EXECUTABLE} "successful"

exit 0
