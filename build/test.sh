#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

declare -a COMMANDS=(
  "./bin/runner --version"
)

for COMMAND in "${COMMANDS[@]}"; do
  EXECUTABLE="${IMAGE_NAME} ${COMMAND}"

  if ! (docker run -it ${EXECUTABLE} >> /dev/null); then
    echo "x" ${EXECUTABLE} "failed"

    exit 1
  fi

  echo "✓" ${EXECUTABLE} "successful"
done

exit 0
