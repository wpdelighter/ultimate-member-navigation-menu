<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ultimate_Member_Navigation_Menu
 * @subpackage Ultimate_Member_Navigation_Menu/includes
 * @author     WP Delighter <support@wpdelighter.com>
 */
class UMNM_Admin_Bar {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_ultimate_member_root' ), 99 );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_ultimate_member_user_menu' ), 99 );
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function admin_bar_ultimate_member_user_menu() {

		global $wp_admin_bar;
		global $ultimatemember;

		if ( ! um_get_option( 'profile_menu' ) ) {
			return;
		}

		// get active tabs
		$tabs = $ultimatemember->profile->tabs_active();

		// need enough tabs to continue
		if ( count( $tabs ) <= 1 ) {
			return;
		}

		$active_tab = $ultimatemember->profile->active_tab();

		if ( ! isset( $tabs[ $active_tab ] ) ) {
			$active_tab = 'main';
		}

		// move default tab priority
		$default_tab = um_get_option( 'profile_menu_default_tab' );
		$dtab        = ( isset( $tabs[ $default_tab ] ) ) ? $tabs[ $default_tab ] : 'main';
		if ( isset( $tabs[ $default_tab ] ) ) {
			unset( $tabs[ $default_tab ] );
			$dtabs[ $default_tab ] = $dtab;
			$tabs                  = $dtabs + $tabs;
		}

		$nav_links = array();

		foreach ( $tabs as $id => $tab ) {

			if ( isset( $tab['hidden'] ) ) {
				continue;
			}

			$nav_link = $ultimatemember->permalinks->profile_url();
			$nav_link = remove_query_arg( 'um_action', $nav_link );
			$nav_link = remove_query_arg( 'subnav', $nav_link );
			$nav_link = add_query_arg( 'profiletab', $id, $nav_link );
			$nav_link = apply_filters( "um_profile_menu_link_{$id}", $nav_link );

			$nav_links[] = array(
				'parent' => 'my-account-ultimate-member',
				'id'     => "um_profile_menu_link_{$id}",
				'title'  => $tab['name'],
				'href'   => $nav_link,
			);

			if ( isset( $tab['subnav'] ) ) {
				foreach ( $tab['subnav'] as $subid => $subtab ) {
					$sub_nav_link = add_query_arg( 'subnav', $subid, $nav_link );
					$nav_links[]  = array(
						'parent' => "um_profile_menu_link_{$id}",
						'id'     => "um_profile_menu_link_{$subid}",
						'title'  => $subtab,
						'href'   => $sub_nav_link,
					);
				}
			}

		}

		// add each menu itens into admin_bar
		foreach ( $nav_links as $admin_menu ) {
			$wp_admin_bar->add_menu( $admin_menu );
		}
	}

	/**
	 * Add the secondary UltimateMember area to the my-account menu.
	 *
	 * @since    1.0.0
	 *
	 * @global WP_Admin_Bar $wp_admin_bar
	 */
	function admin_bar_ultimate_member_root() {
		global $wp_admin_bar;

		// Bail if this is an ajax request
		if ( defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Only add menu for logged in user
		if ( is_user_logged_in() ) {

			// Add secondary parent item for all UltimateMember components
			$wp_admin_bar->add_menu( array(
				'parent' => 'my-account',
				'id'     => 'my-account-ultimate-member',
				'title'  => __( 'My Profile', 'ultimate-member-navigation-menu' ),
				'group'  => true,
				'meta'   => array(
					'class' => 'ab-sub-secondary'
				)
			) );

		}
	}

}
