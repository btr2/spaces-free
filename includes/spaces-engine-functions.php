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
 * Gets the primary Space navigation array.
 *
 * @return mixed|void
 */
function get_primary_nav() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();

	$endpoints = array(
		'about' => 'about',
	);

	$items = array(
		'home'  => esc_html__( 'Home', 'wpe-wps' ),
		'about' => esc_html__( 'About', 'wpe-wps' ),
	);

	// Remove missing endpoints.
	foreach ( $endpoints as $endpoint_id => $endpoint ) {
		if ( empty( $endpoint ) ) {
			unset( $items[ $endpoint_id ] );
		}
	}

	return apply_filters( 'spaces_engine_get_primary_nav', $items, $endpoints );
}
