#!/bin/bash
set -e

if [ $TESTNAME = "unit-tests" ]; then
  echo "Running unit-tests"
  export DISPLAY=:99.0
  sh -e /etc/init.d/xvfb start
  sleep 1
  npm run test
  npm run codecov
elif [ $TESTNAME = "browser-tests" ]; then
  echo "Running browser-tests"
  npm run test:ci
fi
