<?php

function testimonials_custom_post_type() {
	register_post_type('testimonials',
		array(
			'supports' => ['title'],
			'labels'      => array(
				'name'          => __('Testimonials', 'textdomain'),
				'singular_name' => __('Testimonial', 'textdomain'),
			),
			'public'      => true,
			'has_archive' => false,
		)
	);
}
add_action('init', 'testimonials_custom_post_type');

function testimonials_add_custom_box() {
	$screens = [ 'testimonials' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'testimonials_meta_data',
			'Testimonial content',
			'testimonials_meta_data_html',  // Content callback, must be of type callable
			$screen                            // Post type
		);
	}
}

add_action( 'add_meta_boxes', 'testimonials_add_custom_box' );

function testimonials_meta_data_html( $post ) {
	$author = get_post_meta( $post->ID, '_testimonial-author', true );
	$company = get_post_meta( $post->ID, '_testimonial-company', true );
	$testimonial = get_post_meta( $post->ID, '_testimonial-content', true );
	$star_rating = get_post_meta( $post->ID, '_testimonial-star-rating', true );
	?>
	<fieldset>
		<table class="form-table editcomment" role="presentation">
			<tbody>
			<tr>
				<td class="first"><label for="testimonial-author">Author</label></td>
				<td><input type="text" name="testimonial-author" value="<?php echo $author; ?>" /></td>
			</tr>
			<tr>
				<td class="first"><label for="testimonial-company">Company</label></td>
				<td><input type="text" name="testimonial-company" value="<?php echo $company; ?>" /></td>
			</tr>
			<tr>
				<td class="first"><label for="testimonial">Testimonial</label></td>
				<td><textarea name="testimonial"><?php echo $testimonial; ?></textarea></td>
			</tr>
			<tr>
				<td class="first"><label for="testimonial-star-rating">Star rating</label></td>
				<td>
					<select name="testimonial-star-rating" class="postbox">
						<option value="1" <?php selected( $star_rating, '1' ); ?>>1</option>
						<option value="2" <?php selected( $star_rating, '2' ); ?>>2</option>
						<option value="3" <?php selected( $star_rating, '3' ); ?>>3</option>
						<option value="4" <?php selected( $star_rating, '4' ); ?>>4</option>
						<option value="5" <?php selected( $star_rating, '5' ); ?>>5</option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
	</fieldset>
	<?php
}

function testimonials_save_postdata( $post_id ) {
	if ( array_key_exists( 'testimonial-author', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_testimonial-author',
			$_POST['testimonial-author']
		);
	}
	if ( array_key_exists( 'testimonial-company', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_testimonial-company',
			$_POST['testimonial-company']
		);
	}
	if ( array_key_exists( 'testimonial', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_testimonial-content',
			$_POST['testimonial']
		);
	}
	if ( array_key_exists( 'testimonial-star-rating', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_testimonial-star-rating',
			$_POST['testimonial-star-rating']
		);
	}
}
add_action( 'save_post', 'testimonials_save_postdata' );
