/**
 * Gulpfile.
 * Project Configuration for gulp tasks.
 */

var slug = 'assets-integration';

var repositoryFolder = '/Users/tarasdashkevych/Documents/Repositories/assets-integration';
var repositoryFiles = ['./**', '!_dist', '!_dist/**', '!node_module/', '!node_modules/**', '!*.log', '!*.DS_Store', '!*.gitignore', '!*.git'];

var readyForReleaseFolder = './_dist';
var readyForReleaseFiles = ['./**', '!scss', '!scss/**', '!src', '!src/**', '!_dist', '!_dist/**', '!node_module/', '!node_modules/**', '!*.dist', '!*.log', '!*.json', '!*.lock', '!*.DS_Store', '!*.gitignore', '!*.git', '!*.md', '!gulpfile.js', '!phpcs.xml.dist'];

/**
 * Load Plugins.
 */
var gulp = require('gulp');
var sass = require('gulp-sass');
var postcss = require('gulp-postcss');
var rename = require('gulp-rename');
var autoprefixer = require('autoprefixer');
var alphabeticalCSS = require('postcss-sort-alphabetically');
var wpPot = require('gulp-wp-pot');
var cssnano = require('cssnano');
var mqpacker = require('css-mqpacker');
var del = require('del');

var browserify = require('browserify');
var babelify = require('babelify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

// Create a translation .pot file for the theme.
gulp.task('create:pot', function () {
    return gulp.src([ './**/*.php', '!_dist', '!_dist/**', '!vendor', '!vendor/**', '!node_module/', '!node_modules/**' ])
        .pipe(wpPot({
            domain: slug
        }))
        .pipe(gulp.dest( './languages/' + slug + '.pot'));
});

// Create an admin main JavaScript file.
gulp.task('create:admin:js', function () {
    return browserify({
        entries: ['./src/admin/js/app.js']
    })
    .transform( babelify, { presets: ['@babel/env'] } )
    .bundle()
    .pipe( source( 'app.js' ) )
    .pipe( rename({ 
        basename: 'app',
        extname: '.min.js' 
    }) )
    .pipe( buffer() )
    .pipe( sourcemaps.init({ loadMaps: true }) )
    .pipe( uglify() )
    .pipe( sourcemaps.write( './' ) )
    .pipe( gulp.dest( './assets/admin/js/' ) )
});

// Create a distribution folder.
gulp.task( 'create:distribution:folder', function() {
    return gulp.src( readyForReleaseFiles )
        .pipe( gulp.dest( readyForReleaseFolder + '/' + slug ) );
});

// Remove files in a distribution folder.
gulp.task( 'clean:distribution:folder', function(done) {
    del.sync(
        [readyForReleaseFolder + '/*'],
        {force: true}
    );

    done();
});

// These mulptiple tasks clean a distribution folder first and then add files.
gulp.task( 'build:distribution:folder', gulp.series( 'clean:distribution:folder', 'create:distribution:folder' ) );

// Create a repository folder.
gulp.task( 'create:repository:folder', function() {
    return gulp.src( repositoryFiles )
        .pipe( gulp.dest( repositoryFolder ) );
});

// Remove files from a repository folder and leaves only a git file.
gulp.task( 'clean:repository:folder', function(done) {
    del.sync(
        [repositoryFolder + '/*', '!'+ repositoryFolder + '/.git'],
        {force: true}
    );

    done();
});

// These mulptiple tasks clean a distribution folder first and then add files.
gulp.task( 'build:repository:folder', gulp.series( 'clean:repository:folder', 'create:repository:folder' ) );

gulp.task( 'default', gulp.series( 
    'create:pot',
    'create:admin:js',
    'build:distribution:folder',
    'build:repository:folder'
) );

// Watch JS and CSS changes.
gulp.task( 'watch', function() {
    gulp.watch( './src/admin/js/**/*.js', gulp.series( 'create:admin:js' ) );
});
