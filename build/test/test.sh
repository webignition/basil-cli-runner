#!/usr/bin/env bash

source "$(pwd)"/build/.image_data.sh

TARGET_PATH="$(pwd)"/build/test/generated

COMPILER_LOCAL_PORT=9002
RUNNER_LOCAL_PORT=9003

function setup() {
  echo "Test::setup"

  docker rm -f test-compiler-container 2> /dev/null
  docker create \
    -p ${COMPILER_LOCAL_PORT}:8000 \
    -v "$(pwd)"/build/test/basil/chrome:/app/basil/chrome \
    -v "$(pwd)"/build/test/basil/firefox:/app/basil/firefox \
    -v "$(pwd)"/build/test/generated/chrome:/app/generated/chrome \
    -v "$(pwd)"/build/test/generated/firefox:/app/generated/firefox \
    --name test-compiler-container \
    smartassert/basil-compiler:0.17
  docker start test-compiler-container

  docker rm -f test-nginx-container 2> /dev/null
  docker create \
    -v "$(pwd)"/build/test/html:/usr/share/nginx/html \
    -v "$(pwd)"/build/test/basil/firefox:/app/basil/firefox \
    -v "$(pwd)"/build/test/generated/chrome:/app/generated/chrome \
    -v "$(pwd)"/build/test/generated/firefox:/app/generated/firefox \
    --name test-nginx-container \
    nginx:1.19
  docker start test-nginx-container

  docker network create test-network 2> /dev/null
  docker network connect test-network test-nginx-container
}

function main() {
  echo "Test::main"

  for IMAGE_NAME in "${IMAGE_NAMES[@]}"; do
    echo "Testing ${IMAGE_NAME}"

    BROWSER=$(echo ${IMAGE_NAME} | cut -d '/' -f 2 | cut -d '-' -f 1)
    RUNNER_OUTPUT_PATH="$(pwd)"/build/test/runner_output_"${BROWSER}"

    COMPILER_OUTPUT=$( ( echo "./compiler --source=/app/basil/${BROWSER} --target=/app/generated/${BROWSER} "; ) | nc localhost ${COMPILER_LOCAL_PORT})

    if [[ $COMPILER_OUTPUT =~ (Generated.*\.php) ]]; then
      GENERATED_TEST_FILENAME=${BASH_REMATCH}
    else
      echo "x generated filename extraction failed"

      return 2
    fi

    docker rm -f test-${BROWSER}-container 2> /dev/null
    docker create \
      -p ${RUNNER_LOCAL_PORT}:8000 \
      -v "$(pwd)"/build/test/generated/${BROWSER}:/app/generated \
      --name test-${BROWSER}-container \
      ${IMAGE_NAME}
    docker start test-${BROWSER}-container

    docker network connect test-network test-${BROWSER}-container

    sleep 0.1

    ( echo "./bin/runner --path=generated/${GENERATED_TEST_FILENAME}"; ) | nc localhost ${RUNNER_LOCAL_PORT} > ${RUNNER_OUTPUT_PATH}
    docker rm -f test-${BROWSER}-container

    RUNNER_OUTPUT_EXIT_CODE=$(tail $RUNNER_OUTPUT_PATH | tail -1)
    rm $RUNNER_OUTPUT_PATH

    if ! [[ $RUNNER_OUTPUT_EXIT_CODE =~ ^(0) ]]; then
      echo "x ./bin/runner --path=generated/${GENERATED_TEST_FILENAME} failed: ${RUNNER_OUTPUT_EXIT_CODE}"

      return ${RUNNER_OUTPUT_EXIT_CODE}
    fi

    echo "✓ ./bin/runner --path=generated/${GENERATED_TEST_FILENAME} successful"
  done

  return 0
}

function teardown() {
  echo "Test::teardown"

  rm -rf ${TARGET_PATH}/chrome/*.php
  rm -rf ${TARGET_PATH}/firefox/*.php

  docker rm -f test-compiler-container
  docker rm -f test-nginx-container

  docker network rm test-network
}

setup

main
MAIN_RETURN_VALUE=$?

teardown

exit ${MAIN_RETURN_VALUE}
