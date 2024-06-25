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
