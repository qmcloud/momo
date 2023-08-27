'use strict';

var fs = require('fs');

module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg: grunt.file.readJSON('package.json'),
        banner: '/*\n' +
                '* bootstrap-table - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' +
                'https://github.com/wenzhixin/bootstrap-table\n' +
                '* Copyright (c) 2017 zhixin wen\n' +
                '* Licensed MIT License\n' +
                '*/\n',
        // Task configuration.
        clean: ['dist', 'docs/dist'],
        concat: {
            //basic_target: {
            //    src: ['src/bootstrap-table.js', 'src/extensions/**/*.js'],
            //    dest: 'dist/bootstrap-table-all.js'
            //},
            locale_target: {
                src: ['src/locale/**/*.js'],
                dest: 'dist/bootstrap-table-locale-all.js'
            }
        },
        uglify: {
            options: {
                banner: '<%= banner %>'
            },
            basic_target: {
                files: {
                    'dist/bootstrap-table.min.js': ['src/bootstrap-table.js'],
                    //'dist/bootstrap-table-all.min.js': ['dist/bootstrap-table-all.js'],
                    'dist/bootstrap-table-locale-all.min.js': ['dist/bootstrap-table-locale-all.js']
                }
            },
            locale_target: {
                files: [{
                    expand: true,
                    cwd: 'src/locale',
                    src: '**/*.js',
                    dest: 'dist/locale',
                    ext: '.min.js' // replace .js to .min.js
                }]
            },
            extensions_target: {
                files: [{
                    expand: true,
                    cwd: 'src/extensions',
                    src: '**/*.js',
                    dest: 'dist/extensions',
                    ext: '.min.js' // replace .js to .min.js
                }]
            }
        },
        cssmin: {
            add_banner: {
                options: {
                    banner: '<%= banner %>'
                },
                files: {
                    'dist/bootstrap-table.min.css': ['src/bootstrap-table.css']
                }
            }
        },
        copy: {
            source: {
                cwd: 'src',                     // set working folder / root to copy
                src: ['**/*.js', '**/*.css'],   // copy all files and subfolders
                dest: 'dist',                   // destination folder
                expand: true                    // required when using cwd
            },
            files: {
                cwd: 'dist',            // set working folder / root to copy
                src: '**/*',            // copy all files and subfolders
                dest: 'docs/dist',      // destination folder
                expand: true            // required when using cwd
            }
        }
    });

    var bumpVersion = function (path, version, startWith) {
        var lines = fs.readFileSync(path, 'utf8').split('\n');
        lines.forEach(function (line, i) {
            if (line.indexOf(startWith) === 0) {
                lines[i] = startWith + version;
            }
        });
        fs.writeFileSync(path, lines.join('\n'), 'utf8');

        grunt.log.ok('bumped version of ' + path + ' to ' + version);
    };

    grunt.registerTask('docs', 'build the docs', function () {
        var version = require('./package.json').version;
        bumpVersion('./_config.yml', version, 'current_version: ');
        bumpVersion('./src/bootstrap-table.js', version, ' * version: ');
        bumpVersion('./src/bootstrap-table.css', version, ' * version: ');

        var changeLog = fs.readFileSync('./CHANGELOG.md', 'utf8');
        var latestLogs = changeLog.split('### ')[1];
        var date = new Date();

        var lines = [
            '### Latest release (' +
            [date.getFullYear(), date.getMonth() + 1, date.getDate()].join('-') + ')',
            '',
            '#### v' + latestLogs
        ];
        fs.writeFileSync('./docs/_includes/latest-release.md', lines.join('\n'), 'utf8');

        grunt.log.ok('updated the latest-release.md to ' + version);
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', ['clean', 'concat', 'uglify', 'cssmin', 'copy']);
};
