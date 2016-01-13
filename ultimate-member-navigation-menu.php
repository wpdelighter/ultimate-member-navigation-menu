<?php

/**
 * This plugin adds a navigation menu into the admin bar for Ultimate Member components.
 * Also provides option to include Ultimate Member nav items into your theme's menus.
 *
 * @link              https://wpdelighter.com/
 * @since             1.0.0
 * @package           Ultimate_Member_Navigation_Menu
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Member - Navigation Menu
 * Plugin URI:        https://wordpress.org/plugins/ultimate-member-navigation-menu/
 * Description:       Adds a navigation menu into the admin bar for Ultimate Member components. Also provides option to include Ultimate Member nav items into your theme's menus.
 * Version:           1.0.1
 * Author:            WP Delighter
 * Author URI:        https://wpdelighter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-member-navigation-menu
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-umnm-activator.php
 */
function activate_ultimate_member_navigation_menu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-umnm-activator.php';
	UMNM_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_ultimate_member_navigation_menu' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-umnm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ultimate_member_navigation_menu() {

	// if parent plugin is not active
	if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'UM_API' ) ) {

		add_action( 'admin_init', 'deactivate_ultimate_member_navigation_menu' );
		add_action( 'admin_notices', 'ultimate_member_navigation_menu_admin_notice' );

		// Deactivate the Child Plugin
		function deactivate_ultimate_member_navigation_menu() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}

		// Throw an Alert to tell the Admin why it didn't activate
		function ultimate_member_navigation_menu_admin_notice() {

			$child_plugin  = __( 'Ultimate Member - Navigation Menu', 'ultimate-member-navigation-menu' );
			$parent_plugin = __( 'Ultimate Member', 'ultimate-member-navigation-menu' );

			echo '<div class="error"><p>'
			     . sprintf( __( 'The %1$s extension requires the %2$s plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'ultimate-member-navigation-menu' ), '<strong>' . esc_html( $child_plugin ) . '</strong>', '<strong>' . esc_html( $parent_plugin ) . '</strong>' )
			     . '</p></div>';

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

	} else {
		$plugin = new Ultimate_Member_Navigation_Menu();
	}

}

add_action( 'plugins_loaded', 'run_ultimate_member_navigation_menu' );