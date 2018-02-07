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
var downloadid              	= pkg.downloadid;

var imagesSRC			= './assets/images/src/**/*.{png,jpg,gif,svg}'; // Source folder of images which should be optimized.
var imagesDestination	  	= './assets/images/'; // Destination folder of optimized images. Must be different from the imagesSRC folder.

var text_domain             	= '@@textdomain';
var destFile                	= slug+'.pot';
var packageName             	= pkg.title;
var bugReport               	= pkg.author_uri;
var lastTranslator          	= pkg.author;
var team                    	= pkg.author_shop;
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
var runSequence  = require('run-sequence');
var copy         = require('gulp-copy');
var sort         = require('gulp-sort');
var replace      = require('gulp-replace-task');
var cache        = require('gulp-cache');
var wpPot        = require('gulp-wp-pot');
var zip          = require('gulp-zip');
var sftp	 = require('gulp-sftp');
var open	 = require('gulp-open');


/**
 * Tasks.
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
			match: 'pkg.slug',
			replacement: slug
		},
		{
			match: 'pkg.downloadid',
			replacement: downloadid
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
			match: 'pkg.plugin_uri',
			replacement: pkg.plugin_uri
		},
		{
			match: 'pkg.author_uri',
			replacement: pkg.author_uri
		},
		{
			match: 'pkg.description',
			replacement: pkg.description
		},
		{
			match: 'pkg.requires',
			replacement: pkg.requires
		},
		{
			match: 'pkg.tested_up_to',
			replacement: pkg.tested_up_to
		},
		{
			match: 'pkg.tags',
			replacement: pkg.tags
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
 * Build Command.
 * Conducts the build process and notification.
 */
gulp.task( 'build-process', function(callback) {
	runSequence( 'clear', 'build-clean', [ 'images', 'build-translate' ], 'build-copy', 'build-variables', 'build-zip', 'build-clean-after-zip',  callback);
});

// Command.
gulp.task( 'build', function(callback) {
	runSequence( 'build-process', 'build-notification', callback);
});


/**
 * Release Command.
 * Conducts the build process, then upload the zip.
 */
gulp.task( 'release-sftp-upload-zip', function () {
	return gulp.src( './dist/' + slug + '.zip' )
	.pipe( sftp( {
		host: 'sftp.pressftp.com',
		auth: 'LoginDesignerSFTP',
		remotePath: '/wp-content/edd-live-downloads/'
	}))

	.pipe( notify( { message: 'The ' + packageName + ' zip has been uploaded.', onLast: true } ) );
});

// Open the download on logindesigner.com, to update the version number.
gulp.task( 'release-edit-download-version-online', function(){
	gulp.src(__filename)
	.pipe( open( { uri: 'https://logindesigner.com/wp-admin/post.php?post=' + downloadid + '&action=edit&version=' + pkg.version + '' } ) );
});

// Notification.
gulp.task( 'release-notification', function () {
	return gulp.src( '' )
	.pipe( notify( { message: 'The v' + pkg.version + ' release of ' + packageName + ' has been uploaded.', onLast: true } ) );
});

// Command.
gulp.task( 'release', function( callback ) {
	runSequence( 'build-process', [ 'release-sftp-upload-zip' ], 'release-edit-download-version-online', 'release-notification', callback);
});
