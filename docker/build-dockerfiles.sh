#!/usr/bin/env bash

declare -a BROWSER_NAMES=(
  "chrome"
  "firefox"
)

for BROWSER_NAME in "${BROWSER_NAMES[@]}"; do
  OUTPUT="./${BROWSER_NAME}.Dockerfile"
  BROWSER=${BROWSER_NAME} OUTPUT=${OUTPUT} ./docker/build-dockerfile.sh
  echo "Created: ${OUTPUT}"
done
echo ""
