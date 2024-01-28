<?php

function userwall_wp_deactivate() {
	// Check if the option for deletion is set to true
	$delete_on_deactivation = get_option( 'userwall_wp_delete_deactivation', false );

	// If the option is set to true, delete the tables
	if ( $delete_on_deactivation ) {
		global $wpdb;

		// Define the table names with the "userwall_" prefix
		$table_posts              = $wpdb->prefix . 'userwall_posts';
		$table_comments           = $wpdb->prefix . 'userwall_comments';
		$table_likes              = $wpdb->prefix . 'userwall_likes';
		$table_bookmarks          = $wpdb->prefix . 'userwall_bookmarks';
		$table_polls              = $wpdb->prefix . 'userwall_polls';
		$table_poll_votes         = $wpdb->prefix . 'userwall_poll_votes';
		$table_media              = $wpdb->prefix . 'userwall_media';
		$table_albums             = $wpdb->prefix . 'userwall_albums';
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

		// SQL queries to drop the tables
		$sql_queries = array(
			"DROP TABLE IF EXISTS $table_comments",
			"DROP TABLE IF EXISTS $table_likes",
			"DROP TABLE IF EXISTS $table_bookmarks",
			"DROP TABLE IF EXISTS $table_polls",
			"DROP TABLE IF EXISTS $table_poll_votes",
			"DROP TABLE IF EXISTS $table_media",
			"DROP TABLE IF EXISTS $table_albums",
			"DROP TABLE IF EXISTS $table_reports",
			"DROP TABLE IF EXISTS $table_user_reputation",
			"DROP TABLE IF EXISTS $table_badges",
			"DROP TABLE IF EXISTS $table_hashtags",
			"DROP TABLE IF EXISTS $table_user_settings",
			"DROP TABLE IF EXISTS $table_notifications",
			"DROP TABLE IF EXISTS $table_search_history",
			"DROP TABLE IF EXISTS $table_user_followers",
			"DROP TABLE IF EXISTS $table_user_following",
			"DROP TABLE IF EXISTS $table_reports",
			"DROP TABLE IF EXISTS $table_user_notifications",
			"DROP TABLE IF EXISTS $table_poll_options",
			"DROP TABLE IF EXISTS $table_blocklist",
			"DROP TABLE IF EXISTS $table_posts",

		);

		// Delete the tables
		foreach ( $sql_queries as $sql_query ) {
			$wpdb->query( $sql_query );
		}
	}
}
