#!/usr/bin/env bash

set -u
: "${BROWSER}"

echo "Browser: ${BROWSER}"
OUTPUT=$(echo "${BROWSER}.Dockerfile")
cat template.before.Dockerfile ${BROWSER}.partial.Dockerfile template.after.Dockerfile > ./"${OUTPUT}"
echo "Created: ${OUTPUT}"
