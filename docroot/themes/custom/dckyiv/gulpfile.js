/* global require */
var gulp = require('gulp');
var npmDist = require('gulp-npm-dist');
var del = require('del');
var rename = require('gulp-rename');
var eslint = require('gulp-eslint');
var sass = require('gulp-sass');
var sassGlob = require('gulp-sass-glob');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var livereload = require('gulp-livereload');
var neat = require('bourbon-neat').includePaths;
var bourbon = require('bourbon').includePaths;
var sassLint = require('gulp-sass-lint');
var autoprefixer = require('gulp-autoprefixer');
const icomoonBuilder = require('gulp-icomoon-builder');

var paths = {
  scripts: [
    'js/**/*.js',
    '!js/**/*.min.js'
  ],
  sass: {
    main: ['style/scss/styles.scss', 'style/scss/iframes.scss', 'style/scss/print.scss', 'style/scss/wysiwyg.scss'],
    watch: 'style/scss/**/*'
  },
  css: {
    root: 'style/css'
  },
  clean: [
    'style/css/styles.css',
    'style/css/**/*.css.map',
    'js/**/*.min.js'
  ]
};

// Copy dependencies to ./assets
gulp.task('assets', function () {
  gulp.src(npmDist(), {base: './node_modules'})
    .pipe(gulp.dest('./assets'));
});

// Build icomoon font
gulp.task('build-icomoon', function () {
  return gulp.src('./fonts/icomoon/selection.json')
    .pipe(icomoonBuilder({
      templateType: 'map',
    }))
    .on('error', function (error) {
      console.log(error);
    })
    .pipe(gulp.dest('./style/scss/icomoon'))
    .on('error', function (error) {
      console.log(error);
    });
});

gulp.task('clean', function () {
  'use strict';
  return del(paths.clean);
});

gulp.task('js-lint', function () {
  'use strict';
  return gulp.src(paths.scripts)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
});

gulp.task('sass-lint', function () {
  'use strict';
  return gulp.src(paths.sass.watch)
    .pipe(sassLint({
      files: {
        ignore: [
          'bower_components/**/*.scss',
          'style/scss/normalize/**',
          'style/scss/base/_fonts.scss',
          'style/scss/styles.scss'
        ]
      },
      rules: {
        'no-ids': 0,
        'nesting-depth': [1, {'max-depth': 4}],
        'no-qualifying-elements': [1, {'allow-element-with-class': true}],
        'force-element-nesting': 0,
        'force-pseudo-nesting': 0,
        'property-sort-order': 0,
        'no-vendor-prefixes': 0,
        'mixins-before-declarations': [1, {exclude: ['media']}],
        'placeholder-in-extend': 0,
        'single-line-per-selector': 0,
        'no-misspelled-properties': [1, {'extra-properties': ['-webkit-overflow-scrolling']}],
        'class-name-format': 0
      }
    }))
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError());
});

gulp.task('lint', ['js-lint', 'sass-lint']);

gulp.task('compress', function () {
  'use strict';
  return gulp.src(paths.scripts)
    .pipe(rename({suffix: '.min'}))
    .pipe(uglify())
    .pipe(gulp.dest('js/dist'));
});

gulp.task('sass', ['build-icomoon'], function () {
  'use strict';
  return gulp.src(paths.sass.main)
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/support-for/sass'
      ]
        .concat(neat)
        .concat(bourbon)
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions', 'IE >= 9']
    }))
    .pipe(sourcemaps.write('./maps'))
    .pipe(gulp.dest(paths.css.root))
    .pipe(livereload());
});

gulp.task('sourcemaps', function () {
  'use strict';
  return gulp.src(paths.sass.main)
    .pipe(sassGlob())
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.css.root));
});

gulp.task('build', ['build-icomoon', 'assets', 'sass', 'compress']);

gulp.task('watch', ['build'], function () {
  'use strict';
  livereload.listen();
  gulp.watch(paths.sass.watch, ['sass']);
  gulp.watch(paths.scripts, ['compress']);
});

gulp.task('default', ['build']);
