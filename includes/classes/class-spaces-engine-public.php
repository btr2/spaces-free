<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The core plugin class.
 */
class Spaces_Engine_Public {

	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Define the public-facing functionality of the plugin.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * We use an init function to decouple our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'rewrites' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		add_filter( 'template_include', array( $this, 'filter_template' ) );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		}
	}

	public function frontend_scripts() {
		wp_enqueue_style( 'spaces-engine-main', WPE_WPS_PLUGIN_URL . 'assets/css/main.css', array(), WPE_WPS_PLUGIN_VERSION );
		wp_enqueue_script( 'spaces-engine-main', WPE_WPS_PLUGIN_URL . 'assets/js/main.js', array( 'jquery' ), WPE_WPS_PLUGIN_VERSION, true );

		wp_localize_script(
			'spaces-engine-main',
			'spaces_engine_main',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		/** Add a query var for our Space Creation Page */
		$vars[] = 'create-' . strtolower( get_singular_label() ) . '-page';

		return $vars;
	}

	/**
	 * Custom rewrite rules.
	 */
	public function rewrites() {
		$create_space_string = 'create-' . strtolower( get_singular_label() ) . '-page';
		$slug                = get_slug();

		add_rewrite_rule( "^{$slug}/{$create_space_string}", 'index.php?post_type=wpe_wpspace&' . $create_space_string . '=true', 'top' );

		if ( ! get_option( 'wpe_wps_permalinks_flushed' ) ) {
			flush_rewrite_rules( false );
			update_option( 'wpe_wps_permalinks_flushed', 1 );
		}
	}

	/**
	 * Our template includes function.
	 *
	 * @param $template
	 *
	 * @return mixed|string
	 */
	public function filter_template( $template ) {
		$create_space_string = 'create-' . strtolower( get_singular_label() ) . '-page';

		if ( 'wpe_wpspace' === get_query_var( 'post_type' ) ) {
			if ( get_query_var( $create_space_string ) ) {
				// Regardless of other privacy settings, you must be logged in to create a Space
				if ( ! is_user_logged_in() ) {
					wp_safe_redirect( home_url( '/' ) );
					exit;
				}

				// checks if the file exists in the theme first, otherwise serve the file from the plugin
				$theme_file = locate_template( 'spacesengine/create.php' );

				if ( $theme_file ) {
					$template = $theme_file;
				} else {
					$template = WPE_WPS_PLUGIN_DIR . '/templates/create.php';
				}

				return $template;
			}

			if ( is_single() ) {
				$theme_file = locate_template( 'spacesengine/single.php' );

				if ( $theme_file ) {
					$template = $theme_file;
				} else {
					$template = WPE_WPS_PLUGIN_DIR . '/templates/single.php';
				}
			} elseif ( is_archive() ) {
				$theme_file = locate_template( 'spacesengine/archive.php' );

				if ( $theme_file ) {
					$template = $theme_file;
				} else {
					$template = WPE_WPS_PLUGIN_DIR . '/templates/archive.php';
				}
			}
		}

		return $template;
	}
}
