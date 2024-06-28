<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function space_header_template_part() {
	/**
	 * Fires before the display of a Spaces's header.
	 */
	do_action( 'bp_before_space_header' );

	// Get the template part for the header
	include WPE_WPS_PLUGIN_DIR . '/templates/single/cover-image-header.php';

	/**
	 * Fires after the display of a Spaces's header.
	 */
	do_action( 'bp_after_space_header' );

	bp_nouveau_template_notices();
}

function filter_single_content(  $template  ) {

	// print_r(get_query_var('post_type'));
	// print_r(get_query_var('active-space-tab'));

	return $template;
}
// add_filter( 'template_include', __NAMESPACE__  . '\filter_single_content' );

function spaces_get_template_part( $slug, $name = '' ) {
	// checks if the file exists in the theme first, otherwise serve the file from the plugin
	if ( $name ) {
		$theme_file = locate_template( "spaces-engine/{$slug}-{$name}.php" );

		if ( $theme_file ) {
			$template = $theme_file;
		} else {
			$template = WPE_WPS_PLUGIN_DIR . "/templates/{$slug}-{$name}.php";
		}
	} else {
		$theme_file = locate_template( "spaces-engine/{$slug}.php" );

		if ( $theme_file ) {
			$template = $theme_file;
		} else {
			$template = WPE_WPS_PLUGIN_DIR . "templates/{$slug}.php";
		}
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'spaces_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, true );
	}
}

/**
 * Retrieves the menu items.
 *
 * This function retrieves the menu items for the custom navigation. The menu items are returned as an associative array,
 * where the keys are the item IDs and the values are the item names.
 *
 * @return array The menu items.
 */
function get_menu_items() {
	$items = array(
		'home'              => esc_html__( 'Home', 'wpe-wps' ),
		'about'             => esc_html__( 'About', 'wpe-wps' ),
	);

	return apply_filters( 'wpe_wps_custom_nav_items', $items );
}

/**
 * Get account endpoint URL.
 */
function single_endpoint_url( $endpoint ) {
	if ( 'home' === $endpoint ) {
		return get_permalink();
	}

	return get_permalink() . $endpoint;
}

/**
 * Get account menu item classes.
 */
function get_menu_item_classes( $endpoint ) {
	global $wp;

	$classes = array(
		'space-tab',
		'space-navigation-link',
		'space-navigation-link--' . $endpoint,
	);

	$current = false;
	if ( $endpoint === get_query_var( 'active-space-tab' ) ) {
		$current = true;
	}

	// Set current item class.
	if ( 'home' === $endpoint && ( ! get_query_var( 'active-space-tab' ) ) ) {
		$current = true; // Dashboard is not an endpoint, so needs a custom check.
	}

	if ( $current ) {
		$classes[] = 'current';
		$classes[] = 'selected';
	}

	$classes = apply_filters( 'spaces_engine_get_space_menu_item_classes', $classes, $endpoint );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}
