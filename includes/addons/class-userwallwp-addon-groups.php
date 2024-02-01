<?php
class UserWallWP_Addon_Groups extends UserWall_WP_Base_Addon {
	public function get_id() {
		return 'groups';
	}

	public function get_name() {
		return __( 'Groups', 'userwall-wp' );
	}

	public function get_description() {
		return __( 'Groups', 'userwall-wp' );
	}

	public function get_author() {
		return __( 'UserWallWP', 'userwall-wp' );
	}

	public function get_version() {
		return '1.0';
	}

	public function is_ready() {
		return false;
	}

	public function activate_addon() {
		global $wpdb;

		$table_posts  = $wpdb->prefix . 'userwall_posts';
		$table_groups = $wpdb->prefix . 'userwall_groups';

		// SQL query to create the 'userwall_plugin_groups' table
		$sql_query_groups = "CREATE TABLE IF NOT EXISTS $table_groups (
            group_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            parent_group_id BIGINT UNSIGNED DEFAULT NULL,
            group_name VARCHAR(255) NOT NULL,
            group_slug VARCHAR(255) NOT NULL,
            group_description TEXT,
            group_avatar VARCHAR(255),
            creation_date DATETIME NOT NULL,
            creator_user_id BIGINT UNSIGNED NOT NULL,
            INDEX creator_user_id_index (creator_user_id),
            INDEX parent_group_id_index (parent_group_id),
            FOREIGN KEY (creator_user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (parent_group_id) REFERENCES $table_groups(group_id) ON DELETE SET NULL
        )";

		// Array of SQL queries for the first 5 tables
		$sql_queries = array(
			$sql_query_groups,
		);

		// Include the WordPress database upgrade file
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Execute the SQL queries to create the tables
		foreach ( $sql_queries as $sql_query ) {
			dbDelta( $sql_query );
		}
	}

	public function deactivate_addon() {
		global $wpdb;

		$table_groups = $wpdb->prefix . 'userwall_groups';

		// Check if the option for deletion is set to true
		$delete_on_deactivation = get_option( 'userwall_wp_delete_deactivation', false );

		// If the option is set to true, delete the tables
		if ( $delete_on_deactivation ) {
			return;
		}

		// SQL queries to drop the tables
		$sql_queries = array(
			"DROP TABLE IF EXISTS $table_groups",
		);

		// Delete the tables
		foreach ( $sql_queries as $sql_query ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$wpdb->query( $sql_query );
		}
	}

	public function hooks() {
		require_once USERWALL_WP_PLUGIN_DIR . 'includes/addons/includes/class-userwallwp-addon-groups-core.php';
		$groups = new UserWallWP_Addon_Groups_Core();
	}
}
