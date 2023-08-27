#!/bin/bash

/usr/local/bin/node r.js -o ./js.js name=simditor baseUrl=../src/js out=../assets/js/simditor.min.js
/usr/local/bin/node r.js -o ./css.js cssIn=../src/css/simditor.css out=../assets/css/simditor.min.css
