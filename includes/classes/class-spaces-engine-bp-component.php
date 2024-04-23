<?php

namespace SpacesEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implementation of Spaces_Engine_BP_Component.
 */
class Spaces_Engine_BP_Component extends \BP_Component {

	/**
	 * The current Space.
	 *
	 * @var Post
	 */
	public $current_space;

	/**
	 * Constructor method
	 */
	public function __construct() {
		global $bp;

		parent::start(
			'wpe_wpspace',
			__( 'Spaces', 'wpe-wps' ),
			dirname(__FILE__)
		);

		$this->includes();

		$bp->active_components[ $this->id ] = '1';
	}

	/**
	 * Includes.
	 */
	public function includes( $includes = array() ) {

//		$includes = array(
//			'wpe-wps-bp-filters.php',
//			'wpe-wps-bp-template.php',
//			'wpe-wps-bp-functions.php',
//		);
//
//		parent::includes( $includes );
	}

	/**
	 * Set up globals.
	 *
	 * @global obj $bp BuddyPress's global object
	 */
	public function setup_globals( $args = array() ) {
		global $bp;

		if ( ! defined( 'WPE_WPS_BP_SLUG' ) ) {
			define( 'WPE_WPS_BP_SLUG', 'spaces' );
		}

		$globals = array(
			'slug'          => WPE_WPS_BP_SLUG,
			'root_slug'     => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : WPE_WPS_BP_SLUG,
			'has_directory' => false,
		);

		parent::setup_globals( $globals );
	}

	/**
	 * Set up navigation.
	 *
	 * @global obj $bp
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		$main_nav = array(
			'name'                => 'Spaces',
			'slug'                => WPE_WPS_BP_SLUG,
			'position'            => 80,
			'screen_function'     => 'wpe_wps_bp_main_screen',
			'default_subnav_slug' => 'my-spaces',
		);

		if ( bp_loggedin_user_id() === bp_displayed_user_id() ) {
			$spaces_link = trailingslashit( bp_loggedin_user_domain() . WPE_WPS_BP_SLUG );

			$sub_nav[] = array(
				'name'            => 'Spaces',
				'slug'            => 'my-spaces',
				'parent_url'      => $spaces_link,
				'parent_slug'     => WPE_WPS_BP_SLUG,
				'screen_function' => 'wpe_wps_bp_main_screen',
				'position'        => 10,
			);

//			if ( 'dedicated' === wpe_wps_messaging_type() ) {
//				$sub_nav[] = array(
//					'name'            => __( 'Messages', 'wpe-wps' ),
//					'slug'            => 'space-messages',
//					'parent_url'      => $spaces_link,
//					'parent_slug'     => WPE_WPS_BP_SLUG,
//					'screen_function' => 'wpe_wps_bp_my_messages_screen',
//					'position'        => 30,
//				);
//			}
		}

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the component entries in the WordPress Admin Bar.
	 *
	 * @param array $wp_admin_nav See BP_Component::setup_admin_bar() for a description.
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {
		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Set up the logged in user variables.
			$spaces_link = trailingslashit( bp_loggedin_user_domain() . WPE_WPS_BP_SLUG );
			$title       = 'spaces';

			// Add the "My Account" sub menus.
			$wp_admin_nav[] = array(
				'parent' => buddypress()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => $title,
				'href'   => $spaces_link,
			);

			// My Spaces.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-memberships',
				'title'    => 'spaces',
				'href'     => $spaces_link,
				'position' => 10,
			);

			// Messages.
//			if ( 'dedicated' === wpe_wps_messaging_type() ) {
//				$wp_admin_nav[] = array(
//					'parent'   => 'my-account-' . $this->id,
//					'id'       => 'my-account-' . $this->id . '-messages',
//					'title'    => 'space',
//					'href'     => trailingslashit( $spaces_link . 'space-messages' ),
//					'position' => 30,
//				);
//			}

//			$wp_admin_nav[] = array(
//				'parent'   => 'my-account-' . $this->id,
//				'id'       => 'my-account-' . $this->id . '-create',
//				'title'    => 'space',
//				'href'     => trailingslashit( wpe_wps_get_create_page_url() ),
//				'position' => 90,
//			);
		}

		// parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up your component's actions.
	 *
	 * @global obj $bp
	 */
	public function setup_actions() {
		parent::setup_actions();

	}

}

