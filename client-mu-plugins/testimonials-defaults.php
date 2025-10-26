<?php
/**
 * Create default testimonials on site initialization.
 *
 * This file creates sample testimonials when the site is first set up.
 * It only runs once by checking for an option flag.
 *
 * @package VIP_Skeleton
 */

/**
 * Create default testimonial entries.
 *
 * This function creates sample testimonials for the site.
 * It only runs once by setting a flag in the options table.
 */
function vip_create_default_testimonials() {
	// Check if we've already created the defaults
	if ( get_option( 'vip_testimonials_defaults_created' ) ) {
		return;
	}

	// Array of default testimonials
	$default_testimonials = array(
		array(
			'title'   => 'Great Service!',
			'content' => 'I had an amazing experience with this company. The team was professional, responsive, and delivered exceptional results. Highly recommended!',
			'author'  => 'John Smith',
			'company' => 'Tech Innovations Inc.',
			'rating'  => 5,
		),
		array(
			'title'   => 'Exceeded Expectations',
			'content' => 'The quality of work was outstanding. They went above and beyond to ensure our project was a success. Will definitely work with them again.',
			'author'  => 'Sarah Johnson',
			'company' => 'Digital Solutions Ltd.',
			'rating'  => 3,
		),
		array(
			'title'   => 'Highly Professional',
			'content' => 'From start to finish, the experience was seamless. Great communication, timely delivery, and excellent attention to detail.',
			'author'  => 'Michael Chen',
			'company' => 'Global Marketing Group',
			'rating'  => 4,
		),
		array(
			'title'   => 'Outstanding Results',
			'content' => 'The team delivered exactly what we needed, on time and on budget. Their expertise and dedication were evident throughout the project.',
			'author'  => 'Emily Rodriguez',
			'company' => 'Creative Studios',
			'rating'  => 5,
		),
		array(
			'title'   => 'Reliable Partner',
			'content' => 'Working with them has been a pleasure. They understand our needs and consistently deliver high-quality solutions.',
			'author'  => 'David Thompson',
			'company' => 'Enterprise Solutions Corp',
			'rating'  => 3,
		),
	);

	// Create each testimonial
	foreach ( $default_testimonials as $testimonial ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'testimonials',
				'post_title'   => $testimonial['title'],
				'post_status'  => 'publish',
				'post_author'  => 1, // Admin user
			)
		);

		// Add custom meta fields if your testimonials use them
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_testimonial-author', $testimonial['author'] );
			update_post_meta( $post_id, '_testimonial-content', $testimonial['content'] );
			update_post_meta( $post_id, '_testimonial-company', $testimonial['company'] );
			update_post_meta( $post_id, '_testimonial-star-rating', $testimonial['rating'] );
		}
	}

	// Set the flag so we don't create these again
	update_option( 'vip_testimonials_defaults_created', true );
}

// Hook into WordPress init
// Priority 20 ensures custom post types are registered first
add_action( 'init', 'vip_create_default_testimonials', 20 );
