/**
 layer构建
 */

const {src, dest, series} = require('gulp');
var pkg = require('./package.json');
var uglify = require('gulp-uglify');
var minify = require('gulp-clean-css');
var rename = require('gulp-rename');
var header = require('gulp-header');
var del = require('del');

var task = {
    layer: function () {
        src('./src/**/*.css')
            .pipe(minify({
                compatibility: 'ie7'
            }))
            .pipe(dest('./dist'));

        return src('./src/layer.js').pipe(uglify())
            .pipe(header('/*! <%= pkg.realname %>-v<%= pkg.version %> <%= pkg.description %> <%= pkg.license %> License  <%= pkg.homepage %>  By <%= pkg.author %> */\n ;', {pkg: pkg}))
            .pipe(dest('./dist'));

    }
    , mobile: function () {
        return src('./src/mobile/layer.js').pipe(uglify())
            .pipe(header('/*! <%= pkg.realname %> mobile-v<%= pkg.mobile %> <%= pkg.description %> <%= pkg.license %> License  <%= pkg.homepage %>mobile  By <%= pkg.author %> */\n ;', {pkg: pkg}))
            .pipe(dest('./dist/mobile'));
    }
};

exports.layer = task.layer;
exports.mobile = task.mobile;
exports.default = series(task.layer, task.mobile);
