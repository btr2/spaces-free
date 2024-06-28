<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the plural label for Spaces.
 *
 * @return mixed|null
 */
function get_plural_label() {
	return apply_filters( 'spaces_engine_get_plural_label', 'Spaces' );
}

/**
 * Get the singular label for Spaces.
 *
 * @return mixed|null
 */
function get_singular_label() {
	return apply_filters( 'spaces_engine_get_singular_label', 'Space' );
}

/**
 * Get the Spaces slug.
 *
 * @return mixed|null
 */
function get_slug() {
	return apply_filters( 'spaces_engine_get_slug', 'spaces' );
}

/**
 * Determine if current page ID is a single Space Post type
 *
 * @return bool
 */
function is_space_by_id( $post_id ) {
	if ( 'wpe_wpspace' === get_post_type( $post_id ) ) {
		return true;
	} else {
		return false;

	}
}

/**
 * Checks if a user is a Space admin.
 *
 * @param int|null $user_id Optional. The user ID to check. Defaults to the current user ID.
 * @param int|null $space_id Optional. The space ID to check. Defaults to the current space ID.
 *
 * @return bool               Returns true if the user is a Space admin, false otherwise.
 */
function is_space_admin( $user_id = null, $space_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// Site admins are always Space admins.
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	if ( ! $space_id ) {
		$space_id = get_the_ID();
	}

	$author_id = get_post_field( 'post_author', $space_id );

	// Owners are always admins
	if ( (int) $author_id === (int) $user_id ) {
		return true;
	}
}

/**
 * Gets the primary Space navigation array.
 *
 * @return mixed|void
 */
function get_primary_nav() {
	$items = array(
		'home'  => esc_html__( 'Home', 'wpe-wps' ),
		'about' => esc_html__( 'About', 'wpe-wps' ),
	);

	return apply_filters( 'spaces_engine_get_primary_nav', $items );
}

/**
 * Gets the creation steps array.
 *
 * Filterable to allow 3rd-party tools to add steps in. The order of the creation
 * panels shown will be in the order of the array.
 *
 * @return array An associative array of creation steps.
 */
function get_creation_steps() {
	$steps = array(
		'details'       => esc_html__( 'Details', 'wpe-wps' ),
		'profile-image' => esc_html__( 'Profile Image', 'wpe-wps' ),
		'cover-image'   => esc_html__( 'Cover Image', 'wpe-wps' ),
	);

	return apply_filters( 'spaces_engine_get_creation_steps', $steps );
}

/**
 * Checks if a given step is the first creation step.
 *
 * @param string $step The step to check.
 *
 * @return bool Whether the given step is the first creation step.
 */
function is_first_creation_step( $step ) {
	return array_key_first( get_creation_steps() ) === $step;
}

/**
 * Checks if the given step is the last creation step.
 *
 * @param mixed $step The step to check.
 *
 * @return bool Whether the given step is the last creation step.
 */
function is_last_creation_step( $step ) {
	return array_key_last( get_creation_steps() ) === $step;
}

/**
 * Gets the string for the Space creation page.
 *
 * @return string The formatted string for creating a Space page.
 */
function get_create_space_string() {
	return sprintf(
		esc_html__( 'create-%1$s-page', 'wpe-wps' ),
		esc_html( strtolower( get_singular_label() ) )
	);
}

/**
 * Gets the link for the Space creation page.
 *
 * @return string The link for creating a Space.
 */
function get_create_space_link() {
	return get_post_type_archive_link( 'wpe_wpspace' ) . get_create_space_string();
}

/**
 * Retrieves the creation step tabs.
 *
 * @return void
 */
function get_creation_step_tabs() {
	$counter = 1;
	foreach ( get_creation_steps() as $slug => $step ) { ?>

		<li data-id="<?php echo esc_attr( $slug ); ?>" <?php echo 1 === $counter ? 'class="current"' : ''; ?>>
			<span><?php echo esc_html( $counter ); ?>. <?php echo esc_html( $step ); ?></span>
		</li>
		<?php
		$counter++;
	}
}

/**
 * Gets the creation step buttons HTML.
 *
 * @return string The HTML for the creation step buttons.
 */
function get_creation_step_buttons() {
	ob_start();
	?>

	<div class="prev-next">
		<button type="submit" class="primary start" data-action="back" style="display: none">
			<?php esc_html_e( 'Previous step', 'wpe-wps' ); ?>
		</button>
		<button type="submit" class="primary end" data-action="forward">
			<?php esc_html_e( 'Next step', 'wpe-wps' ); ?>
		</button>
	</div>

	<?php
	return ob_get_flush();
}

/**
 * Gets the default cover image for a Space.
 *
 * @param bool $echo Optional. Whether to echo the image or just return the image URL. Defaults to false.
 *
 * @return string|void The URL of the default cover image or nothing if $echo is set to true.
 */
function default_cover_image( $echo = false ) {
	$cover_image = WPE_WPS_PLUGIN_URL . 'assets/images/cover.jpg';

	if ( ! $echo ) {
		?>

		<img src="<?php echo esc_url( $cover_image ); ?>" alt="Space Cover Image">

		<?php
	} else {
		return $cover_image;
	}
}
