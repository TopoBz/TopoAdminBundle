'use strict';

var gulp = require('gulp');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');

var config = {
  bootstrapPath: './node_modules/bootstrap-sass/assets',
  assetsPath: './Resources/assets',
  publicPath: './Resources/public',
};

gulp.task('sass', function () {
  gulp.src(config.assetsPath + '/scss/**/*.scss')
    .pipe(sass({
      includePaths: [
        config.bootstrapPath,
      ],
    }))
    .pipe(autoprefixer())
    .pipe(gulp.dest(config.publicPath + '/css'));
});

gulp.task('sass:watch', function () {
  gulp.watch(config.assetsPath + '/scss/**/*.scss', ['sass']);
});


gulp.task('watch', ['sass:watch']);
