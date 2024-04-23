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
		$this->load_dependencies();
		$this->init();
	}

	/**
	 * Load our dependencies.
	 *
	 * @return void
	 */
	public function load_dependencies() {}

	/**
	 * We use an init function to decouple our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_endpoints' ) );

		add_filter( 'archive_template', array( $this, 'archive_template' ), 10, 3 );
		add_filter( 'single_template', array( $this, 'single_template' ), 99, 3 );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		}
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, EP_PAGES | EP_PERMALINK );
			}
		}
	}

	/**
	 * Get query current active query var.
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;

		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {

		$space_item = get_primary_nav();
		$query_vars = array();
		foreach ( $space_item as $item_key => $value ) {
			$query_vars[ $item_key ] = $item_key;
		}
		return apply_filters( 'spaces_engine_get_query_vars', array_merge( $this->query_vars, $query_vars ) );
	}

	/**
	 * The function will return the archive page template.
	 *
	 * @param string $archive_template Archive page template path.
	 * @since    1.0.0
	 */
	public function archive_template( $archive_template, $type, $templates ) {
		global $wpdb, $wp_query, $post;

		if ( is_archive() && ( 'wpe_wpspace' === get_post_type() || 'wpe_wpspace' === $wp_query->query_vars['post_type'] ) ) {

			$template = '/spaces/archive-' . $wp_query->query_vars['post_type'] . '.php';

			if ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
				$archive_template = get_stylesheet_directory() . '/' . $template;
			} elseif ( file_exists( get_template_directory() . '/' . $template ) ) {
				$archive_template = get_template_directory() . '/' . $template;
			} else {
				$archive_template = WPE_WPS_PLUGIN_DIR . '/templates/archive-wpe_wpspace.php';
			}
		}

		return $archive_template;
	}


	/**
	 * The function will return the single business template.
	 *
	 * @param string $single_template single page template path.
	 * @since    1.0.0
	 */
	public function single_template( $single_template, $type, $templates ) {
		global $wpdb, $wp_query, $post;

		if ( is_single() && ( 'wpe_wpspace' === get_post_type() || 'wpe_wpspace' === $wp_query->query_vars['post_type'] ) ) {

			/* return false when rtmedia plugin is active and media open from single business tab*/
			add_filter( 'rtmedia_return_is_template', '__return_false' );

			$template = '/spaces/single-' . $wp_query->query_vars['post_type'] . '.php';

			if ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
				$single_template = get_stylesheet_directory() . '/' . $template;
			} elseif ( file_exists( get_template_directory() . '/' . $template ) ) {
				$single_template = get_template_directory() . '/' . $template;
			} else {
				$single_template = WPE_WPS_PLUGIN_DIR . '/templates/single-wpe_wpspace.php';
			}
		}
		return $single_template;
	}
}
