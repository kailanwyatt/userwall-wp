<?php
/**
 * Deactivation functions for the User Wall plugin.
 *
 * @file  deactivation.php
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Deactivate the User Wall plugin.
 */
function userwall_wp_deactivate() {
	// Check if the option for deletion is set to true.
	$delete_on_deactivation = get_option( 'userwall_wp_delete_deactivation', false );
	$delete_on_deactivation = 1;
	// If the option is set to true, delete the tables.
	if ( $delete_on_deactivation ) {
		global $wpdb;

		// Define the table names with the "userwall_" prefix.
		$table_posts              = $wpdb->prefix . 'userwall_posts';
		$table_comments           = $wpdb->prefix . 'userwall_comments';
		$table_likes              = $wpdb->prefix . 'userwall_likes';
		$table_bookmarks          = $wpdb->prefix . 'userwall_bookmarks';
		$table_polls              = $wpdb->prefix . 'userwall_polls';
		$table_poll_votes         = $wpdb->prefix . 'userwall_poll_votes';
		$table_reports            = $wpdb->prefix . 'userwall_reports';
		$table_user_reputation    = $wpdb->prefix . 'userwall_user_reputation';
		$table_badges             = $wpdb->prefix . 'userwall_badges';
		$table_hashtags           = $wpdb->prefix . 'userwall_hashtags';
		$table_user_settings      = $wpdb->prefix . 'userwall_user_settings';
		$table_notifications      = $wpdb->prefix . 'userwall_notifications';
		$table_search_history     = $wpdb->prefix . 'userwall_search_history';
		$table_user_followers     = $wpdb->prefix . 'userwall_user_followers';
		$table_user_following     = $wpdb->prefix . 'userwall_user_following';
		$table_reports            = $wpdb->prefix . 'userwall_reports';
		$table_user_notifications = $wpdb->prefix . 'userwall_user_notifications';
		$table_poll_options       = $wpdb->prefix . 'userwall_poll_options';
		$table_blocklist          = $wpdb->prefix . 'userwall_blocklist';

		// SQL queries to drop the tables.
		$sql_queries = array(
			$table_comments,
			$table_likes,
			$table_bookmarks,
			$table_polls,
			$table_poll_votes,
			$table_reports,
			$table_user_reputation,
			$table_badges,
			$table_hashtags,
			$table_user_settings,
			$table_notifications,
			$table_search_history,
			$table_user_followers,
			$table_user_following,
			$table_reports,
			$table_user_notifications,
			$table_poll_options,
			$table_blocklist,
		);

		$drop_tables = apply_filters( 'userwall_wp_drop_tables', $sql_queries );

		// Add the posts table to the list of tables to drop.
		$drop_tables[] = $table_posts;

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Execute the SQL queries to create the tables.
		foreach ( $drop_tables as $table ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) );
		}
	}
}
