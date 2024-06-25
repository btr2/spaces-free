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

	$title       = $_POST['title'];
	$description = $_POST['description'];

	if ( ! $title || ! $description ) {
		$error = new \WP_Error(
			'Missing title or description',
			esc_html__( 'Please fill in all of the required fields', 'wpe-wps' )
		);

		wp_send_json_error( $error );
	}

	$categories = array();
	if ( ! empty( $_POST['category'] ) ) {
		if ( is_array( $_POST['category'] ) ) {
			foreach ( $_POST['category'] as $category ) {
				if ( is_numeric( $category ) ) {
					$categories[] = intval( $category );
				}
			}
		} else {
			$categories = intval( $_POST['category'] );
		}
	}

	$args = array(
		'post_title'   => sanitize_text_field( $title ),
		'post_type'    => 'wpe_wpspace',
		'post_status'  => 'publish',
		'post_content' => wp_kses_post( $description ),
	);

	$id = wp_insert_post( $args );

	if ( $description ) {
		// Legacy. The OG Spaces Engine put this into meta.
		add_post_meta( $id, 'wpe_wps_short_description', sanitize_text_field( $description ) );
	}

	wp_set_object_terms( $id, $categories, 'wp_space_category' );

	$response = array(
		'url'     => esc_url( get_permalink( $id ) ),
		'message' => sprintf(
		// translators: Placeholder %s is the singular label of the space post type.
			esc_html__( '%s created successfully', 'wpe-wps' ),
			esc_html( get_singular_label() )
		),
	);

	wp_send_json_success( $response );

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
		'post_type'      => 'wpe_wpspace',
		'posts_per_page' => 12,
		'paged'          => $page,
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
		<ul id="space-list" class="item-list space-list bp-list grid">

		<?php
		while ( $the_query->have_posts() ) :
			$the_query->the_post();

			include WPE_WPS_PLUGIN_DIR . '/templates/card.php';
			?>

		<?php endwhile; ?>

		</ul>

		<?php paginate( $the_query->max_num_pages, $page ); ?>

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

/**
 * Paginates a list of items.
 *
 * This method generates a pagination HTML output for a list of items based on the maximum number of pages and the current page.
 * It uses the `paginate_links()` function to generate the paginated links.
 * The `$max_num_pages` parameter is the total number of pages available.
 * The `$paged` parameter is the current page number.
 * It also supports localization for the "Prev" and "Next" texts, based on the existence of the BuddyBoss plugin.
 * The generated pagination HTML is outputted directly to the page.
 *
 * @param int $max_num_pages The total number of pages available.
 * @param int $paged The current page number.
 *
 * @return void
 */
function paginate( $max_num_pages, $paged ) {
	$big          = 999999999;
	$search_for   = array( $big, '#038;' );
	$replace_with = array( '%#%', '' );
	$position     = 'bottom';
	$pag_count    = false;

	if ( isset( buddypress()->buddyboss ) ) {
		$prev = esc_html__( 'Prev', 'wpe-wps' );
		$next = esc_html__( 'Next', 'wpe-wps' );
	} else {
		$prev = '←';
		$next = '→';
	}

	$paginate = paginate_links(
		array(
			'base'      => str_replace( $search_for, $replace_with, esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?page=%#%',
			'type'      => 'array',
			'current'   => max( 1, $paged ),
			'total'     => $max_num_pages,
			'prev_next' => true,
			'prev_text' => $prev,
			'next_text' => $next,
		)
	);

	if ( $max_num_pages > 1 ) :
		?>
		<div class="<?php echo esc_attr( 'bp-pagination ' . sanitize_html_class( $position ) ); ?>">

			<?php if ( $pag_count ) : ?>
				<div class="<?php echo esc_attr( 'pag-count ' . sanitize_html_class( $position ) ); ?>">

					<p class="pag-data">
						<?php echo esc_html( $pag_count ); ?>
					</p>

				</div>
			<?php endif; ?>

			<div class="bp-pagination-links bottom pagination">
				<?php foreach ( $paginate as $page ) : ?>
					<?php echo wp_kses_post( $page ); ?>
				<?php endforeach; ?>
			</div>

		</div>

		<?php
	endif;
}
