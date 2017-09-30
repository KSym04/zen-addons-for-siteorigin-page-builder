var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');

var browserSync = require('browser-sync').create();
var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');

gulp.task('browserSync', function() {
	browserSync.init({
		proxy: 'local-uri.com'
	});
});

gulp.task('scss', function() {
	var errPrint = function(err) {
		console.log(err);
		this.emit('end');
	};

	return gulp
		.src('./assets/scss/style.scss')
		.pipe(sourcemaps.init())
		.pipe(
			sass({
				outputStyle: 'expanded'
			}).on('error', errPrint)
		)
		.on('error', errPrint)
		.pipe(postcss([autoprefixer({ browsers: ['last 2 version'] })]))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(browserSync.stream());
});

gulp.task('build', ['scss'], function() {
	return gulp
		.src('./assets/css/frontend.css')
		.pipe(postcss([cssnano()]))
		.pipe(rename('style.min.css'))
		.pipe(gulp.dest('./assets/css/'));
});

gulp.task('watch', ['browserSync'], function() {
	gulp.watch('./assets/scss/**/*.scss', ['scss']);
});

gulp.task('default', ['watch']);
