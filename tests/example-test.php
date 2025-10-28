<?php
/**
 * Example test case for WordPress VIP.
 *
 * @package VIP_Skeleton
 */

/**
 * Example test case.
 */
class Example_Test extends WP_UnitTestCase {

	/**
	 * Test that WordPress is loaded.
	 */
	public function test_wordpress_loaded() {
		$this->assertTrue( function_exists( 'do_action' ) );
	}

	/**
	 * Test that WP database is working.
	 */
	public function test_database_connection() {
		global $wpdb;
		$this->assertInstanceOf( 'wpdb', $wpdb );
		$this->assertNotEmpty( $wpdb->prefix );
	}

	/**
	 * Example test for WordPress version.
	 */
	public function test_wp_version() {
		global $wp_version;
		$this->assertNotEmpty( $wp_version );
	}

	/**
	 * Test creating a post.
	 */
	public function test_create_post() {
		$post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
			'post_content' => 'Test content',
		) );

		$this->assertGreaterThan( 0, $post_id );
		$post = get_post( $post_id );
		$this->assertEquals( 'Test Post', $post->post_title );
	}
}
