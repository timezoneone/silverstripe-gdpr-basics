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
        './client/javascript/cookie-permission.js'
    ];

gulp.task('js', function() {
    return gulp.src(jsFiles)
        .pipe(rename('cookie-permission.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('client/dist'));
});

gulp.task('js:w', function() {
    return gulp.watch(jsFiles, { ignoreInitial: false }, gulp.parallel('js'));
});

/**
 * Compile SCSS/SASS files
 */
gulp.task('scss', function() {
    return gulp.src(['client/scss/cookie-permission.scss'])
        .pipe(sass.sync({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(rename('Style.ss'))
        .pipe(autoprefixer({browsers: ['last 2 versions']}))
        .pipe(gulp.dest('./templates'));
});

gulp.task('scss:w', function() {
    return gulp.watch('./scss/**/*.scss', gulp.parallel('js'));
});

gulp.task('watch', gulp.parallel('js:w', 'scss:w'));

gulp.task('default', gulp.parallel('js', 'scss'));

