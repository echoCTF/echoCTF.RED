var gulp = require('gulp');
var path = require('path');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var open = require('gulp-open');
const minify = require("gulp-minify");
const imagemin = require('gulp-imagemin');
let cleanCSS = require('gulp-clean-css');
//var uglify = require('gulp-uglify');
var imageResize = require('gulp-image-resize');
var rename = require("gulp-rename");

var Paths = {
  HERE: './',
  DIST: 'dist/',
  CSS: '../../frontend/web/css/',
  SCSS_TOOLKIT_SOURCES: './assets/scss/material-dashboard.scss',
  SCSS: './assets/scss/**/**'
};
gulp.task('createMiniIMG', function() {
  return gulp.src(['../images/targets/!(*thumbnail).png'])
        .pipe(imageResize({percentage : 50 }))
        .pipe(rename(function (path) { path.basename += "-thumbnail"; }))
      .pipe(gulp.dest('../images/targets'));
});

gulp.task('robohash', function() {
  return gulp.src(['../../frontend/web/images/robohash/**/*.png'])
        .pipe(imageResize({width : 300, height: 300 }))
      .pipe(gulp.dest('../../frontend/web/images/robohash'));
});

gulp.task('minifyIMG', function() {
  return gulp.src(['../images/targets/*'])
        .pipe(imagemin([
          imagemin.gifsicle({interlaced: true}),
          imagemin.mozjpeg({progressive: true}),
          imagemin.optipng({optimizationLevel: 7}),
          imagemin.svgo({
              plugins: [
                  {removeViewBox: true},
                  {cleanupIDs: false}
              ]
          })
      ],{ verbose: true }))
      .pipe(gulp.dest('../images/targets'));
});

gulp.task('minifyJS', function() {
  return gulp.src('../../frontend/web/js/**/*.js')
    .pipe(minify({ext:{ min:'.min.js' },ignoreFiles: ['*min.js']}))
    .pipe(gulp.dest('../../frontend/web/js'));
});

gulp.task('minifyCSS', function() {
  return gulp.src('../../frontend/web/css/**/*.css')
    .pipe(cleanCSS())
    .pipe(gulp.dest(Paths.CSS));
});

gulp.task('compile', function() {
  return gulp.src(Paths.SCSS_TOOLKIT_SOURCES)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS())
    .pipe(sourcemaps.write(Paths.HERE))
    .pipe(gulp.dest(Paths.CSS));
});
gulp.task('all', function() {
    gulp.start('compile');
    gulp.start('minifyJS');
    gulp.start('createMiniIMG');
    gulp.start('minifyIMG');
});

gulp.task('watch', function() {
    gulp.start('compile').start('minifyJS').start('minifyIMG').watch(Paths.SCSS, ['compile']);
});

gulp.task('open', function() {
  gulp.src('examples/dashboard.html')
    .pipe(open());
});

//gulp.task('open-app', ['open', 'watch']);
