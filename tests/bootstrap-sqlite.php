<?php
/**
 * PHPUnit bootstrap file for WordPress VIP with SQLite.
 *
 * @package VIP_Skeleton
 */

// Composer autoloader must be loaded first
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// The WordPress tests suite is installed in the tmp directory
$_tests_dir = dirname( __DIR__ ) . '/tmp/wordpress-tests-lib';

// Fallback to WP_PHPUNIT__DIR environment variable if tmp doesn't exist
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	$_tests_dir = getenv( 'WP_PHPUNIT__DIR' );
}

if ( ! $_tests_dir || ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	die( "WordPress test suite not found.\nRun: composer install-wp-tests\n" );
}

// Configure SQLite database BEFORE wp-config-test.php is loaded
$sqlite_db_dir = dirname( __DIR__ ) . '/tmp/sqlite';
$sqlite_db_file = 'test.sqlite';

// Create the SQLite database directory if it doesn't exist
if ( ! file_exists( $sqlite_db_dir ) ) {
	mkdir( $sqlite_db_dir, 0755, true );
}

// Copy the SQLite drop-in to BOTH the WordPress core AND test suite wp-content directories
$sqlite_dropin_source = dirname( __DIR__ ) . '/wp-content/wp-sqlite-db/src/db.php';

if ( ! file_exists( $sqlite_dropin_source ) ) {
	die( "SQLite drop-in not found at: $sqlite_dropin_source\nRun: composer install\n" );
}

// Copy to WordPress core wp-content (where WordPress actually loads from)
$wp_core_dir = dirname( __DIR__ ) . '/tmp/wordpress';
$wp_core_content_dir = $wp_core_dir . '/wp-content';
$wp_core_dropin_dest = $wp_core_content_dir . '/db.php';

if ( file_exists( $wp_core_content_dir ) ) {
	if ( ! file_exists( $wp_core_dropin_dest ) || md5_file( $sqlite_dropin_source ) !== md5_file( $wp_core_dropin_dest ) ) {
		copy( $sqlite_dropin_source, $wp_core_dropin_dest );
		echo "SQLite drop-in copied to WordPress core.\n";
	}
}

// Also copy to test suite wp-content (for good measure)
$wp_content_dir = $_tests_dir . '/wp-content';
$sqlite_dropin_dest = $wp_content_dir . '/db.php';

if ( ! file_exists( $wp_content_dir ) ) {
	mkdir( $wp_content_dir, 0755, true );
}

if ( ! file_exists( $sqlite_dropin_dest ) || md5_file( $sqlite_dropin_source ) !== md5_file( $sqlite_dropin_dest ) ) {
	copy( $sqlite_dropin_source, $sqlite_dropin_dest );
	echo "SQLite drop-in copied to test suite.\n";
}

// Inject SQLite configuration into wp-tests-config.php if not already present
$wp_tests_config = $_tests_dir . '/wp-tests-config.php';
if ( file_exists( $wp_tests_config ) ) {
	$config_content = file_get_contents( $wp_tests_config );
	if ( strpos( $config_content, "define( 'DB_DIR'" ) === false ) {
		// Add SQLite configuration right after the opening PHP tag
		$sqlite_config = "\n// SQLite configuration (added by bootstrap-sqlite.php)\ndefine( 'DB_DIR', '$sqlite_db_dir' );\ndefine( 'DB_FILE', '$sqlite_db_file' );\n";
		$config_content = preg_replace( '/(<\?php)/', "$1$sqlite_config", $config_content, 1 );
		file_put_contents( $wp_tests_config, $config_content );
		echo "SQLite configuration added to wp-tests-config.php.\n";
	}
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the environment being tested.
 */
function _manually_load_environment() {
	// Load VIP config
	if ( file_exists( dirname( __DIR__ ) . '/vip-config/vip-config.php' ) ) {
		require dirname( __DIR__ ) . '/vip-config/vip-config.php';
	}

	// Load client MU plugins
	if ( file_exists( dirname( __DIR__ ) . '/client-mu-plugins/plugin-loader.php' ) ) {
		require dirname( __DIR__ ) . '/client-mu-plugins/plugin-loader.php';
	}
}

tests_add_filter( 'muplugins_loaded', '_manually_load_environment' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
