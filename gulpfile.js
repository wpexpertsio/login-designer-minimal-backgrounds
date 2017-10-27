/**
 * Gulpfile.
 * Project Configuration for gulp tasks.
 */

var pkg                     	= require('./package.json');
var project                 	= pkg.name;
var slug                    	= pkg.slug;
var version                	= pkg.version;
var license                	= pkg.license;
var copyright              	= pkg.copyright;
var author                 	= pkg.author;
var plugin_uri              	= pkg.plugin_uri;

var imagesSRC			= './assets/images/src/**/*.{png,jpg,gif,svg}'; // Source folder of images which should be optimized.
var imagesDestination	  	= './assets/images/'; // Destination folder of optimized images. Must be different from the imagesSRC folder.

var text_domain             	= '@@textdomain';
var destFile                	= slug+'.pot';
var packageName             	= project;
var bugReport               	= pkg.author_uri;
var lastTranslator          	= pkg.author;
var team                    	= pkg.author;
var translatePath           	= './languages';
var translatableFiles       	= ['./**/*.php'];

var buildFiles      	    = ['./**', '!dist/', '!.gitattributes', '!assets/images/src/**/*.{png,jpg,gif,svg}', '!.csscomb.json', '!node_modules/**', '!'+ slug +'.sublime-project', '!package.json', '!gulpfile.js', '!assets/scss/**', '!*.json', '!*.map', '!*.xml', '!*.sublime-workspace', '!*.sublime-gulp.cache', '!*.log', '!*.DS_Store','!*.gitignore', '!TODO', '!*.git' ];
var buildDestination        = './dist/'+ slug +'/';
var distributionFiles       = './dist/'+ slug +'/**/*';

/**
 * Load Plugins.
 */
var gulp         = require('gulp');
var cleaner      = require('gulp-clean');
var notify       = require('gulp-notify');
var imagemin     = require('gulp-imagemin');
var runSequence  = require('gulp-run-sequence');
var copy         = require('gulp-copy');
var sort         = require('gulp-sort');
var replace      = require('gulp-replace-task');
var cache        = require('gulp-cache');
var wpPot        = require('gulp-wp-pot');
var zip          = require('gulp-zip');

/**
 * Clean gulp cache
 */
gulp.task('clear', function () {
	cache.clearAll();
});

gulp.task( 'images', function() {
	gulp.src( imagesSRC )
	.pipe( imagemin( {
		progressive: true,
		optimizationLevel: 2,
		interlaced: true,
		svgoPlugins: [{removeViewBox: false}]
	} ) )
	.pipe(gulp.dest( imagesDestination ))
});

/**
 * Build Tasks
 */

gulp.task( 'build-translate', function () {

	gulp.src( translatableFiles )

	.pipe( sort() )
	.pipe( wpPot( {
		domain        : text_domain,
		destFile      : destFile,
		package       : project,
		bugReport     : bugReport,
		lastTranslator: lastTranslator,
		team          : team
	} ))
	.pipe( gulp.dest( translatePath ) )

});

gulp.task( 'build-clean', function () {
	return gulp.src( ['./dist/*'] , { read: false } )
	.pipe(cleaner());
});

gulp.task( 'build-copy', function() {
    return gulp.src( buildFiles )
    .pipe( copy( buildDestination ) );
});

gulp.task( 'build-variables', function () {
	return gulp.src( distributionFiles )
	.pipe( replace( {
		patterns: [
		{
			match: 'pkg.version',
			replacement: version
		},
		{
			match: 'textdomain',
			replacement: pkg.textdomain
		},
		{
			match: 'pkg.name',
			replacement: project
		},
		{
			match: 'pkg.license',
			replacement: pkg.license
		},
		{
			match: 'pkg.author',
			replacement: pkg.author
		},
		{
			match: 'pkg.description',
			replacement: pkg.description
		}
		]
	}))
	.pipe( gulp.dest( buildDestination ) );
});

gulp.task( 'build-zip', function() {
    return gulp.src( buildDestination+'/**', {base: 'dist'} )
    .pipe( zip( slug +'.zip' ) )
    .pipe( gulp.dest( './dist/' ) );
});

gulp.task( 'build-clean-after-zip', function () {
	return gulp.src( [ buildDestination, '!/dist/' + slug + '.zip'] , { read: false } )
	.pipe(cleaner());
});

gulp.task( 'build-notification', function () {
	return gulp.src( '' )
	.pipe( notify( { message: 'Your build of ' + packageName + ' is complete.', onLast: true } ) );
});

/**
 * Commands
 */
gulp.task( 'build', function(callback) {
	runSequence( 'clear', 'build-clean', [ 'images', 'build-translate' ], 'build-copy', 'build-variables', 'build-zip', 'build-clean-after-zip', 'build-notification',  callback);
});
