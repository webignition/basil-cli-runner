#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

source ${CURRENT_DIRECTORY}/test_setup.sh

declare -a ALL_IMAGE_NAMES=(
  ${BASE_IMAGE_NAME}
  ${CHROME_IMAGE_NAME}
)

declare -a COMMANDS_FOR_ALL_IMAGES=(
  "./bin/runner --version"
  "./bin/runner --path=bin"
)

for IMAGE_NAME in "${ALL_IMAGE_NAMES[@]}"; do
  for COMMAND in "${COMMANDS_FOR_ALL_IMAGES[@]}"; do
    EXECUTABLE="${IMAGE_NAME} ${COMMAND}"

    if ! (docker run -it ${EXECUTABLE} >> /dev/null); then
      echo "x" ${EXECUTABLE} "failed"
      source ${CURRENT_DIRECTORY}/test_teardown.sh

      exit 1
    fi

    echo "✓" ${EXECUTABLE} "successful"
  done
done

declare -a BROWSER_IMAGE_NAMES=(
  ${CHROME_IMAGE_NAME}
)

declare -a COMMANDS_FOR_BROWSER_IMAGES=(
  "./bin/runner --path=generated"
)

for BROWSER_IMAGE_NAME in "${BROWSER_IMAGE_NAMES[@]}"; do
  for COMMAND in "${COMMANDS_FOR_BROWSER_IMAGES[@]}"; do
    EXECUTABLE="${IMAGE_NAME} ${COMMAND}"

    docker run \
      -v "$(pwd)"/docker/test/generated:/app/generated \
      --network=test-network \
      -it \
      ${EXECUTABLE}

    if [ ${?} != 0 ]; then
      echo "x" ${EXECUTABLE} "failed"
      source ${CURRENT_DIRECTORY}/test_teardown.sh

      exit 1
    fi

    echo "✓" ${EXECUTABLE} "successful"
  done
done

source ${CURRENT_DIRECTORY}/test_teardown.sh

exit 0
