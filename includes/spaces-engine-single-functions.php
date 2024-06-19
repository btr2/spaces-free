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
