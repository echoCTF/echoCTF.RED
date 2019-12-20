var gulp = require('gulp');
var path = require('path');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var sourcemaps = require('gulp-sourcemaps');
var open = require('gulp-open');
const minify = require("gulp-minify");
let cleanCSS = require('gulp-clean-css');
//var uglify = require('gulp-uglify');

var Paths = {
  HERE: './',
  DIST: 'dist/',
  CSS: '../../frontend/web/css/',
  SCSS_TOOLKIT_SOURCES: './assets/scss/material-dashboard.scss',
  SCSS: './assets/scss/**/**'
};
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
    .pipe(sourcemaps.write(Paths.HERE))
    .pipe(gulp.dest(Paths.CSS));
});

gulp.task('watch', function() {
    gulp.start('compile').watch(Paths.SCSS, ['compile']);
});

gulp.task('open', function() {
  gulp.src('examples/dashboard.html')
    .pipe(open());
});

gulp.task('open-app', ['open', 'watch']);
