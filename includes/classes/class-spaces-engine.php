<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class.
 */
class Spaces_Engine {
	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->load_dependencies();

		$this->init();
		$this->boot_admin();
		$this->boot_public();
	}

	/**
	 * Load our dependencies.
	 *
	 * @return void
	 */
	public function load_dependencies() {
		require_once WPE_WPS_PLUGIN_DIR . '/includes/spaces-engine-functions.php';
		require_once WPE_WPS_PLUGIN_DIR . '/admin/includes/classes/class-spaces-engine-admin.php';
		require_once WPE_WPS_PLUGIN_DIR . '/includes/classes/class-spaces-engine-bp-component.php';
		require_once WPE_WPS_PLUGIN_DIR . '/includes/classes/class-spaces-engine-public.php';
	}

	/**
	 * We use an init function to decouple our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'bp_loaded', array( $this, 'register_component' ) );
	}

	/**
	 * Register our BP component.
	 *
	 * @return void
	 */
	public function register_component() {
		global $bp;
		$bp->spaces = new Spaces_Engine_BP_Component();
	}

	/**
	 * Boots our admin functionality.
	 *
	 * @return void
	 */
	public function boot_admin() {
		$spaces_engine_admin = new Spaces_Engine_Admin();
	}

	/**
	 * Boots our public-facing functionality.
	 *
	 * @return void
	 */
	public function boot_public() {
		$spaces_engine_public = new Spaces_Engine_Public();
	}
}
