<?php
/**
 * Class UserWallWP_Addon_Followers
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserWallWP_Addon_Followers
 */
class UserWallWP_Addon_Followers extends UserWall_WP_Base_Addon {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get the addon ID.
	 *
	 * @return string The addon ID.
	 */
	public function get_id() {
		return 'followers';
	}

	/**
	 * Get the addon name.
	 *
	 * @return string The addon name.
	 */
	public function get_name() {
		return __( 'Followers', 'userwall-wp' );
	}

	/**
	 * Get the addon description.
	 *
	 * @return string The addon description.
	 */
	public function get_description() {
		return __( 'This addon adds ability to have users follow each other.', 'userwall-wp' );
	}

	/**
	 * Get the addon author.
	 *
	 * @return string The addon author.
	 */
	public function get_author() {
		return 'UserWallWP'; // Author of the Files addon.
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
	 * Check if the addon is ready.
	 *
	 * @return bool True if the addon is ready, false otherwise.
	 */
	public function is_ready() {
		return false;
	}

	/**
	 * Activate the addon.
	 */
	public function activate_addon() {
		global $wpdb;
		$table_user_followers = $wpdb->prefix . 'userwall_user_followers';
		$table_posts          = $wpdb->prefix . 'userwall_posts';

		/**
		 * SQL query to create the 'userwall_plugin_bookmarks' table.
		 */
		$sql_query_user_followers = "CREATE TABLE IF NOT EXISTS $table_user_followers (
            follower_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            follower_user_id BIGINT UNSIGNED NOT NULL,
            INDEX user_id_index (user_id),
            INDEX follower_user_id_index (follower_user_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (follower_user_id) REFERENCES {$wpdb->prefix}users(ID)
        )";

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_query_user_followers );
	}

	/**
	 * Deactivate the addon.
	 */
	public function deactivate_addon() {
		global $wpdb;
		// Check if the option for deletion is set to true.
		$delete_on_deactivation = get_option( 'userwall_wp_delete_deactivation', false );

		// If the option is set to true, delete the tables.
		if ( $delete_on_deactivation ) {
			$table_media = $wpdb->prefix . 'userwall_media';

			// SQL queries to drop the tables.
			$sql_queries = array(
				$table_media,
			);

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Delete the tables.
			foreach ( $sql_queries as $table ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) );
			}
		}
	}

	/**
	 * Add hooks and actions specific to the addon.
	 */
	public function hooks() {
		// Add hooks and actions specific to the addon here.
	}
}
