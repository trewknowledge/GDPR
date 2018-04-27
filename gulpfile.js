var gulp = require('gulp');
var	uglify = require('gulp-uglify-es').default;
var	$ = require('gulp-load-plugins')();

var paths = {
	src: {
		php: './**/*.php',
		admin: {
			js: './src/js/admin/*.js',
			css: './src/css/admin/*.scss'
		},
		public: {
			js: './src/js/public/*.js',
			css: './src/css/public/*.scss'
		}
	},
	dest: {
		css: './assets/css/',
		js: './assets/js/',
		pot: './languages/'
	}
};

function errorLog(error) {
    console.log(error.message);
    this.emit('end');
}

gulp.task('pot', function() {
	return gulp.src( paths.src.php )
		.pipe( $.wpPot( {
			domain: 'gdpr'
		} ) )
		.pipe( gulp.dest( paths.dest.pot + 'gdpr.pot' ) );
});

gulp.task('admin-css', function() {
	return gulp.src( paths.src.admin.css )
		.pipe( $.sass( {
			outputStyle: 'compressed'
		} ) )
		.on('error', errorLog)
		.pipe( $.autoprefixer( 'last 4 versions' ) )
		.pipe( $.rename( 'gdpr-admin.css' ) )
		.pipe( gulp.dest( paths.dest.css ) )
		.pipe( $.livereload() )
		.pipe( $.notify( {
			message: 'Admin SASS style task complete'
		} ) );
});

gulp.task('public-css', function() {
	return gulp.src( paths.src.public.css )
		.pipe( $.sass( {
			outputStyle: 'compressed'
		} ) )
		.on('error', errorLog)
		.pipe( $.autoprefixer( 'last 4 versions' ) )
		.pipe( $.rename( 'gdpr-public.css' ) )
		.pipe( gulp.dest( paths.dest.css ) )
		.pipe( $.livereload() )
		.pipe( $.notify( {
			message: 'Admin SASS style task complete'
		} ) );
});

gulp.task('admin-js', function() {
	return gulp.src( paths.src.admin.js )
		.pipe( $.concat( 'gdpr-admin.js' ) )
		.pipe( uglify() )
		.on('error', errorLog)
		.pipe( gulp.dest( paths.dest.js ) )
		.pipe($.livereload())
		.pipe( $.notify( {
			message: 'Admin JS script task complete'
		} ) );
});

gulp.task('public-js', function() {
	return gulp.src( paths.src.public.js )
		.pipe( $.concat( 'gdpr-public.js' ) )
		.pipe( uglify() )
		.on('error', errorLog)
		.pipe( gulp.dest( paths.dest.js ) )
		.pipe($.livereload())
		.pipe( $.notify( {
			message: 'Public JS script task complete'
		} ) );
});

gulp.task('watch', function(){
	$.livereload.listen();
	gulp.watch( paths.src.php, $.livereload.reload);
	gulp.watch( paths.src.admin.css, ['admin-css']);
	gulp.watch( paths.src.public.css, ['public-css']);
	gulp.watch( paths.src.admin.js, ['admin-js']);
	gulp.watch( paths.src.public.js, ['public-js']);
});

gulp.task('default', ['admin-css', 'public-css', 'admin-js', 'public-js', 'pot', 'watch']);
