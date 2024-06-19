<?php
/**
 * @link              https://spacesengine.com
 * @since             1.0.0
 *
 * @wordpress-plugin
 *
 * Plugin Name: Spaces Engine
 * Plugin URI:  https://spacesengine.com/
 * Description: Easily create business profiles for BuddyPress and BuddyBoss.
 * Author:      Bouncingsprout Studio
 * Author URI:  https://www.bouncingsprout.com/
 * Version:     1.0.0
 * Domain Path: /languages/
 * License:     GPLv2 or later (license.txt)
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'WPE_WPS_PLUGIN_VERSION', '1.0.0' );
define( 'WPE_WPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPE_WPS_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'WPE_WPS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Start the engines, Captain...
 */
function wpe_wps_run() {
	require_once WPE_WPS_PLUGIN_PATH . 'includes/classes/class-spaces-engine.php';
	$plugin = new \SpacesEngine\Spaces_Engine();
}
add_action( 'bp_include', 'wpe_wps_run' );

/**
 *  Checks if BuddyPress is activated.
 */
function wpe_wps_requires_buddypress() {
	if ( ! class_exists( 'Buddypress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'wpe_wps_required_plugin_admin_notice' );
	}
}
add_action( 'admin_init', 'wpe_wps_requires_buddypress' );


/**
 * Creates an error admin notice explaining why Spaces Engine was deactivated.
 */
function wpe_wps_required_plugin_admin_notice() {

	$a = ' Spaces Engine';
	$b = 'BuddyPress';
	echo '<div class="error"><p>';
	/* translators: %s: */
	echo sprintf(
		/* Translators: %1$s is our name. %2$s is BuddyPress */
		esc_html__( '%1$s requires %2$s to be installed and active.', 'wpe-wps' ),
		'<strong>' . esc_html( $a ) . '</strong>',
		'<strong>' . esc_html( $b ) . '</strong>'
	);
	echo '</p></div>';
}
