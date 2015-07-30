var gulp = require('gulp');
var webserver = require('gulp-webserver');

gulp.task('webserver', function() {
  gulp.src('')
  .pipe(webserver({
    livereload: false,
    directoryListing: false,
    open: false
  }));
});

var _public_ = './../../../../public/';

gulp.task('honeybase1', function() {
  var s1 = gulp.src(_public_ + 'assets/lib/honeybase.js');
  return s1.pipe(gulp.dest('lib'));
});
gulp.task('honeybase2', function() {
  var s2 = gulp.src(_public_ + 'config/origins.json');
  return s2.pipe(gulp.dest('lib'));
});
gulp.task('honeybase3', function() {
  var s3 = gulp.src(_public_ + 'config/honeybase_development_config.json');
  return s3.pipe(gulp.dest('lib'));
});
gulp.task('honeybase4', function() {
  var s4 = gulp.src(_public_ + 'config/honeybase_staging_config.json');
  return s4.pipe(gulp.dest('lib'));
});
gulp.task('honeybase5', function() {
  var s5 = gulp.src(_public_ + 'config/honeybase_production_config.json');
  return s5.pipe(gulp.dest('lib'));
});

gulp.task("default",[
  "honeybase1",
  "honeybase2",
  "honeybase3",
  "honeybase4",
  "honeybase5",
  "webserver"
]);
