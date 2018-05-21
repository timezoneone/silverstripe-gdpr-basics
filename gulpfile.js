'use strict';
var gulp = require('gulp'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer');

/**
 *   Bundle JS files
 */
var jsFiles = [
        './javascript/cookie-permission.js'
    ];

gulp.task('js', function() {
    gulp.src(jsFiles)
        .pipe(rename('cookie-permission.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('dist'));
});

gulp.task('js:w', function() {
    gulp.watch(jsFiles, ['js']);
});

/**
 * Compile SCSS/SASS files
 */
gulp.task('scss', function() {
    gulp.src(['scss/cookie-permission.scss'])
        .pipe(sass.sync({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(rename('Style.ss'))
        .pipe(autoprefixer({browsers: ['last 2 versions']}))
        .pipe(gulp.dest('./templates'));
});

gulp.task('scss:w', function() {
    gulp.watch('./scss/**/*.scss', ['scss']);
});

gulp.task('watch', ['js:w', 'scss:w']);

gulp.task('default', ['scss', 'js']);
