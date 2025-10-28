<?php
/**
 * PHPUnit bootstrap file for WordPress VIP.
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
