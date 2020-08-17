#!/usr/bin/env bash

declare -a STEPS=(
  $(pwd)"/build/build.sh"
  $(pwd)"/build/test/images_exist.sh"
  $(pwd)"/build/test/test.sh"
)

for STEP in "${STEPS[@]}"; do
  ${STEP}

  if [ $? -ne 0 ]
  then
    echo ${STEP}" failed"

    exit $?
  fi
done

exit 0
