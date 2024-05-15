<?php
/**
 * UserWallWP_Addon_Polls class
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class UserWallWP_Addon_Polls
 */
class UserWallWP_Addon_Polls extends UserWall_WP_Base_Addon {
	/**
	 * Get the ID.
	 *
	 * @return string The ID.
	 */
	public function get_id() {
		return 'polls';
	}

	/**
	 * Get the name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return __( 'Polls', 'userwall-wp' );
	}

	/**
	 * Get the description.
	 *
	 * @return string The description.
	 */
	public function get_description() {
		return __( 'A way to add Polls to posts with expiration', 'userwall-wp' );
	}

	/**
	 * Get the author.
	 *
	 * @return string The author.
	 */
	public function get_author() {
		return __( 'UserWallWP', 'userwall-wp' );
	}

	/**
	 * Get the version.
	 *
	 * @return string The version.
	 */
	public function get_version() {
		return '1.0';
	}

	/**
	 * Check if the addon is ready.
	 *
	 * @return bool True if ready, false otherwise.
	 */
	public function is_ready() {
		return false;
	}

	/**
	 * Activate the addon.
	 */
	public function activate_addon() {
		global $wpdb;

		$table_posts        = $wpdb->prefix . 'userwall_posts';
		$table_polls        = $wpdb->prefix . 'userwall_polls';
		$table_poll_votes   = $wpdb->prefix . 'userwall_poll_votes';
		$table_poll_options = $wpdb->prefix . 'userwall_poll_options';

		/*
		 * SQL query to create the 'userwall_polls' table.
		 */
		$sql_query_polls = "CREATE TABLE IF NOT EXISTS $table_polls (
            poll_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_text TEXT NOT NULL,
            creation_date DATETIME NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            post_id BIGINT UNSIGNED NOT NULL,
            INDEX user_id_index (user_id),
            INDEX post_id_index (post_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
        )";

		/*
		 * SQL query to create the 'userwall_poll_votes' table.
		 */
		$sql_query_poll_votes = "CREATE TABLE IF NOT EXISTS $table_poll_votes (
            vote_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            poll_id BIGINT UNSIGNED NOT NULL,
            selected_option BIGINT NOT NULL,
            INDEX user_id_index (user_id),
            INDEX poll_id_index (poll_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (poll_id) REFERENCES $table_polls(poll_id)
        )";

		/*
		 * SQL query to create the 'wp_userwall_poll_options' table.
		 */
		$sql_query_poll_options = "CREATE TABLE IF NOT EXISTS $table_poll_options (
            option_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            poll_id BIGINT UNSIGNED NOT NULL,
            option_text TEXT NOT NULL,
            INDEX poll_id_index (poll_id),
            FOREIGN KEY (poll_id) REFERENCES wp_userwall_polls(poll_id)
        )";

		// Array of SQL queries for the first 5 tables.
		$sql_queries = array(
			$sql_query_polls,
			$sql_query_poll_votes,
			$sql_query_poll_options,
		);

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Execute the SQL queries to create the tables.
		foreach ( $sql_queries as $sql_query ) {
			dbDelta( $sql_query );
		}
	}

	/**
	 * Deactivate the addon.
	 */
	public function deactivate_addon() {
		global $wpdb;

		$table_polls        = $wpdb->prefix . 'userwall_polls';
		$table_poll_votes   = $wpdb->prefix . 'userwall_poll_votes';
		$table_poll_options = $wpdb->prefix . 'userwall_poll_options';

		// SQL queries to drop the tables.
		$sql_queries = array(
			$table_poll_votes,
			$table_poll_options,
			$table_polls,
		);

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Delete the tables.
		foreach ( $sql_queries as $table ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) );
		}
	}

	/**
	 * Add hooks.
	 */
	public function hooks() {
		add_filter( 'userwall_wp_post_tabs', array( $this, 'add_poll_tab' ) );
	}

	/**
	 * Add poll tab.
	 *
	 * @param array $tabs The tabs.
	 * @return array The modified tabs.
	 */
	public function add_poll_tab( $tabs = array() ) {
		$tabs['poll'] = __( 'Poll', 'userwall-wp' );
		return $tabs;
	}
}
