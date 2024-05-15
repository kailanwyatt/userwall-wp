<?php
/**
 * UserWallWP_Addon_Reactions class
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class UserWallWP_Addon_Reactions
 */
class UserWallWP_Addon_Reactions extends UserWall_WP_Base_Addon {
	/**
	 * Get the addon ID.
	 *
	 * @return string The addon ID.
	 */
	public function get_id() {
		return 'reactions';
	}

	/**
	 * Get the addon name.
	 *
	 * @return string The addon name.
	 */
	public function get_name() {
		return __( 'Reactions', 'userwall-wp' );
	}

	/**
	 * Get the addon description.
	 *
	 * @return string The addon description.
	 */
	public function get_description() {
		return __( 'Adds User Reactions to posts and comments', 'userwall-wp' );
	}

	/**
	 * Get the addon author.
	 *
	 * @return string The addon author.
	 */
	public function get_author() {
		return __( 'UserWallWP', 'userwall-wp' );
	}

	/**
	 * Get the addon version.
	 *
	 * @return string The addon version.
	 */
	public function get_version() {
		return '1.0';
	}

	/**
	 * Activate the addon.
	 */
	public function activate_addon() {
		// Add activation logic specific to this addon (e.g., create tables).
	}

	/**
	 * Deactivate the addon.
	 */
	public function deactivate_addon() {
		// Add deactivation logic specific to this addon (e.g., cleanup).
	}

	/**
	 * Check if the addon is ready.
	 *
	 * @return bool True if the addon is ready, false otherwise.
	 */
	public function is_ready() {
		return false;
	}
}
