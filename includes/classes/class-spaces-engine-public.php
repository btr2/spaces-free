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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'template_include', array( $this, 'filter_template' ) );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		}
	}

	/**
	 * Enqueues the stylesheets needed for Spaces Engine.
	 *
	 * This function adds the main CSS stylesheet for the Spaces Engine plugin
	 * and dynamically loads inline styles for the color palette and border radius.
	 *
	 * @return void
	 */
	public function enqueue_styles(  ) {
		wp_enqueue_style( 'spaces-engine-main', WPE_WPS_PLUGIN_URL . 'assets/css/main.css', array(), WPE_WPS_PLUGIN_VERSION );

		// Loads dynamic inline color style.
		$color_css = $this->load_color_palette();
		wp_add_inline_style( 'spaces-engine-main', $color_css );

		// Loads dynamic border radius inline style.
		$radius_css = $this->load_border_radius();
		wp_add_inline_style( 'spaces-engine-main', $radius_css );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'spaces-engine-main', WPE_WPS_PLUGIN_URL . 'assets/js/main.js', array( 'jquery' ), WPE_WPS_PLUGIN_VERSION, true );

		wp_localize_script(
			'spaces-engine-main',
			'spaces_engine_main',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'creation_steps' => get_creation_steps(),
				'next' => esc_html__( 'Next step', 'wpe-wps' ),
				'previous' => esc_html__( 'Previous step', 'wpe-wps' ),
				'create' => sprintf(
					// translators: Placeholder %s is the singular label of the space post type.
					esc_html__( 'Create %s', 'wpe-wps' ),
					esc_html( strtolower( get_singular_label() ) )
				),
				'visit' => sprintf(
					// translators: Placeholder %s is the singular label of the space post type.
					esc_html__( 'Visit %s', 'wpe-wps' ),
					esc_html( strtolower( get_singular_label() ) )
				),
				'create_space_link' => esc_url( get_create_space_link() )
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

		/** Add query vars for each menu item and creation step */
		$vars[] = 'active-space-tab';

		return $vars;
	}

	/**
	 * Custom rewrite rules.
	 */
	public function rewrites() {
		$create_space_string = get_create_space_string();
		$slug                = get_slug();

		add_rewrite_rule( "^{$slug}/{$create_space_string}", 'index.php?post_type=wpe_wpspace&' . $create_space_string . '=true', 'top' );
		$navs = get_primary_nav();

		foreach ( $navs as $nav => $value ) {
			add_rewrite_rule( "^{$slug}/([^/]*)/{$nav}", 'index.php?post_type=wpe_wpspace&name=$matches[1]&active-space-tab=' . $nav, 'top' );
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
		buddypress()->current_component = 'activity';
		$create_space_string = get_create_space_string();

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
				/* We need to force our single template to be part of the activity component, to receive the needed scripts */
				// buddypress()->current_component = 'activity';
				// add_filter( 'bp_current_component', function () {return 'activity';} );
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

	/**
	 * Loads the color palette for the plugin.
	 *
	 * @return string The CSS root variables for the color palette.
	 */
	public function load_color_palette() {

		$colors = array(
			'primary_color'    => '--global-primary-color',
			'secondary_color'  => '--global-secondary-color',
			'border_color'     => '--global-border-color',
			'background_color' => '--content-background-color',
		);

		// Customizer colors.todo
		$color_settings = array();

		$primary_color    = ( isset( $color_settings['primary_color'] ) ) ? $color_settings['primary_color'] : '#1b74e5';
		$secondary_color  = ( isset( $color_settings['secondary_color'] ) ) ? $color_settings['secondary_color'] : '#0c60cc';
		$border_color     = ( isset( $color_settings['border_color'] ) ) ? $color_settings['border_color'] : '#ced0d4';
		$background_color = ( isset( $color_settings['background_color'] ) ) ? $color_settings['background_color'] : '#ffffff';

		$admin_colors = array(
			'--global-primary-color'     => $primary_color,
			'--global-secondary-color'   => $secondary_color,
			'--global-border-color'      => $border_color,
			'--content-background-color' => $background_color,
		);

		$fallback_colors = array(
			'primary_color'    => '#1b74e5',
			'secondary_color'  => '#0c60cc',
			'border_color'     => '#ced0d4',
			'background_color' => '#ffffff',
		);

		$color_string = '';
		foreach ( $colors as $key => $property ) {
			$fallback_color = isset( $fallback_colors[ $key ] ) ? $fallback_colors[ $key ] : '';
			$color          = get_option( $key, $fallback_color );

			if ( isset( $admin_colors[ $property ] ) ) {
				$color = $admin_colors[ $property ];
			}

			if ( $color ) {
				$color_string .= $property . ':' . $color . ';';
			}
		}

		return ':root{' . $color_string . '}';
	}

	/**
	 * Load the border radius styles.
	 *
	 * This method generates the CSS for the border radius based on the settings and options.
	 *
	 * @return string The CSS for the border radius styles.
	 */
	public function load_border_radius() {

		// todo
		$color_settings = array();

		$global_radius = array(
			'border_radius' => '--global-border-radius',
		);

		// Global Border Radius.
		$global_border_radius_option = ( isset( $color_settings['border_radius'] ) ) ? $color_settings['border_radius'] . 'px' : '8px';

		$admin_radius = array(
			'--global-border-radius' => $global_border_radius_option,
		);

		$fallback_border_radius = array(
			'global_border_radius_option' => '8px',
		);

		$radius_string = '';
		foreach ( $global_radius as $key => $property ) {
			$fallback_radius = isset( $fallback_border_radius[ $key ] ) ? $fallback_border_radius[ $key ] : '8px';
			$border_radius   = get_option( $key, $fallback_radius );

			if ( isset( $admin_radius[ $property ] ) ) {
				$border_radius = $admin_radius[ $property ];
			}

			if ( $border_radius ) {
				$radius_string .= $property . ':' . $border_radius . ';';
			}
		}

		return ':root{' . $radius_string . '}';
	}
}
