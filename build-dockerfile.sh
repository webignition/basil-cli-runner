#!/usr/bin/env bash

set -u
: "${BROWSER}"

echo "Browser: ${BROWSER}"
cat ./docker/template.before.Dockerfile ./docker/${BROWSER}.partial.Dockerfile ./docker/template.after.Dockerfile > ./Dockerfile
