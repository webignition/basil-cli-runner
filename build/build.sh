!/usr/bin/env bash

docker build -f chrome.Dockerfile -t "smartassert/chrome-runner:${TAG_NAME:-master}" .
docker build -f firefox.Dockerfile -t "smartassert/firefox-runner:${TAG_NAME:-master}" .
