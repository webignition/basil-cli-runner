#!/usr/bin/env bash

set -u
: "${BROWSER}"

OUTPUT="${OUTPUT:-./${BROWSER}.Dockerfile}"

cat ./docker/template.before.Dockerfile ./docker/${BROWSER}.partial.Dockerfile ./docker/template.after.Dockerfile > ${OUTPUT}
