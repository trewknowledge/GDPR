var gulp = require('gulp');
var	uglify = require('gulp-uglify-es').default;
var	$ = require('gulp-load-plugins')();

var paths = {
	src: {
		php: ['./**/*.php', '!./vendor/**/*.php'],
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

function pot() {
	return gulp.src( paths.src.php )
		.pipe( $.wpPot( {
			domain: 'gdpr'
		} ) )
		.pipe( gulp.dest( paths.dest.pot + 'gdpr.pot' ) );
}

function admincss() {
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
}

function publiccss() {
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
}

function adminjs() {
	return gulp.src( paths.src.admin.js )
		.pipe( $.concat( 'gdpr-admin.js' ) )
		.pipe( uglify() )
		.on('error', errorLog)
		.pipe( gulp.dest( paths.dest.js ) )
		.pipe($.livereload())
		.pipe( $.notify( {
			message: 'Admin JS script task complete'
		} ) );
}

function publicjs() {
	return gulp.src( paths.src.public.js )
		.pipe( $.concat( 'gdpr-public.js' ) )
		.pipe( uglify() )
		.on('error', errorLog)
		.pipe( gulp.dest( paths.dest.js ) )
		.pipe($.livereload())
		.pipe( $.notify( {
			message: 'Public JS script task complete'
		} ) );
}

function watch() {
	$.livereload.listen();
	gulp.watch( paths.src.php, $.livereload.reload);
	gulp.watch( paths.src.admin.css, admincss);
	gulp.watch( paths.src.public.css, publiccss);
	gulp.watch( paths.src.admin.js, adminjs);
	gulp.watch( paths.src.public.js, publicjs);
}

exports.pot = pot;
exports.admincss = admincss;
exports.publiccss = publiccss;
exports.adminjs = adminjs;
exports.publicjs = publicjs;
exports.watch = watch;

var build = gulp.series(admincss, publiccss, adminjs, publicjs, pot, watch);

exports.default = build;
