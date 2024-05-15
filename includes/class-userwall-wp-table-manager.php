<?php
/**
 * UserWall_WP_Table_Manager class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Table_Manager class
 */
class UserWall_WP_Table_Manager {
	/**
	 *  Method to get the table names.
	 */
	public static function get_table_names() {
		global $wpdb;

		// Define the table names with the "userwall_" prefix.
		$table_names = array(
			'posts'     => $wpdb->prefix . 'userwall_posts',
			'comments'  => $wpdb->prefix . 'userwall_comments',
			'likes'     => $wpdb->prefix . 'userwall_likes',
			'bookmarks' => $wpdb->prefix . 'userwall_bookmarks',
			'groups'    => $wpdb->prefix . 'userwall_groups',
			'polls'     => $wpdb->prefix . 'userwall_polls',
			// Add more table names here as needed.
		);

		return $table_names;
	}
}
