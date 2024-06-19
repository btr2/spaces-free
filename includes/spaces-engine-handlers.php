<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates a new space.
 *
 * This method creates a new space by inserting a post with the given title and post type.
 * It verifies the nonce for security before creating the space.
 * If the nonce is not valid, it sends a JSON error message.
 * After creating the space, it sends a JSON success response with the URL of the space.
 * Finally, it terminates the script execution.
 *
 * @return void
 */
function create_space() {
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'create_space' ) ) {
		$error = new \WP_Error(
			'Failed security check while creating the Space',
			esc_html__( 'We ran into a problem...', 'wpe-wps' )
		);

		wp_send_json_error( $error );
	}

	$title = $_POST['title'];

	$args = array(
		'post_title'  => sanitize_text_field( $title ),
		'post_type'   => 'wpe_wpspace',
		'post_status' => 'publish',
	);

	$id = wp_insert_post( $args );

	$url = get_permalink( $id );

	wp_send_json_success( esc_url( $url ) );

	wp_die();
}
add_action( 'wp_ajax_create_space', __NAMESPACE__ . '\create_space' );

/**
 * Filters spaces based on the given parameters.
 *
 * This method filters spaces based on the parameters received via POST request.
 * It verifies the nonce for security before filtering the spaces.
 * If the nonce is not valid, it sends a JSON error message.
 * It applies filters to the query based on the scope, order, search terms, and category.
 * The filtered spaces are retrieved using WP_Query and displayed in a list format.
 * If no spaces match the criteria, a "Sorry, no posts matched your criteria" message is displayed.
 * The filtered content is captured using output buffering and sent as a JSON success response.
 * The response includes pagination information and the filtered content.
 * Finally, it terminates the script execution.
 *
 * @return void
 */
function filter_spaces() {
	global $max_num_pages;

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['params']['nonce'] ) ), 'spaces-index' ) ) {
		$error = new \WP_Error(
			'Failed security check while processing Spaces index',
			esc_html__( 'We ran into a problem...', 'wpe-wps' )
		);

		wp_send_json_error( $error );
	}

	$page       = intval( $_POST['params']['page'] );
	$qty        = 12;
	$pagination = 'standard';

	$args = array(
		'post_type' => 'wpe_wpspace',
	);

	if ( 'personal' === $_POST['params']['scope'] ) {
		$args['author'] = get_current_user_id();
	}

	if ( 'alphabetical' === $_POST['params']['order'] ) {
		$args['orderby'] = 'title';
		$args['order']   = 'ASC';
	}

	if ( $_POST['params']['search_terms'] ) {
		$args['s'] = sanitize_text_field( $_POST['params']['search_terms'] );
	}

	if ( $_POST['params']['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wp_space_category',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $_POST['params']['category'] ),
			),
		);
	}

	$the_query = new \WP_Query( $args );

	ob_start();

	if ( $the_query->have_posts() ) : ?>
		<?php
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html( the_title() ); ?></a>
			</article><!-- #post-## -->

		<?php endwhile; ?>

	<?php else : ?>
		<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'wpe-wps' ); ?></p>
		<?php
	endif;

		$response = array(
			'pagination' => $pagination,
			'next'       => $page + 1,
		);

		$response['content'] = ob_get_clean();

		wp_send_json_success( $response );

		wp_die();
}
add_action( 'wp_ajax_filter_spaces', __NAMESPACE__ . '\filter_spaces' );
add_action( 'wp_ajax_nopriv_filter_spaces', __NAMESPACE__ . '\filter_spaces' );
