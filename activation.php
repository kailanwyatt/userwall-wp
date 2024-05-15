<?php
/**
 * User Wall WP Activation
 *
 * @file  activation.php
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Activation functions for the User Wall plugin.
 */
function userwall_wp_activate() {
	global $wpdb;

	// Define the table names with the "userwall_" prefix
	// Core tables.
	$table_posts    = $wpdb->prefix . 'userwall_posts';
	$table_comments = $wpdb->prefix . 'userwall_comments';
	$table_likes    = $wpdb->prefix . 'userwall_likes';

	$table_reports            = $wpdb->prefix . 'userwall_reports';
	$table_user_reputation    = $wpdb->prefix . 'userwall_user_reputation';
	$table_badges             = $wpdb->prefix . 'userwall_badges';
	$table_hashtags           = $wpdb->prefix . 'userwall_hashtags';
	$table_user_settings      = $wpdb->prefix . 'userwall_user_settings';
	$table_notifications      = $wpdb->prefix . 'userwall_notifications';
	$table_search_history     = $wpdb->prefix . 'userwall_search_history';
	$table_user_notifications = $wpdb->prefix . 'userwall_user_notifications';
	$table_blocklist          = $wpdb->prefix . 'userwall_blocklist';

	// SQL query to create the 'userwall_plugin_posts' table.
	$sql_query_posts = "CREATE TABLE IF NOT EXISTS $table_posts (
        post_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_title VARCHAR(255) NOT NULL,
        post_content TEXT,
        post_type VARCHAR(20) NOT NULL,
        content_type VARCHAR(20) NOT NULL,
        post_status VARCHAR(20) NOT NULL,
        post_action VARCHAR(20) NOT NULL,
        creation_date DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// SQL query to create the 'userwall_plugin_comments' table.
	$sql_query_comments = "CREATE TABLE IF NOT EXISTS $table_comments (
        comment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        parent_id BIGINT NOT NULL,
        comment_content TEXT,
        comment_date DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";

	// SQL query to create the 'userwall_plugin_likes' table.
	$sql_query_likes = "CREATE TABLE IF NOT EXISTS $table_likes (
        like_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id BIGINT UNSIGNED NOT NULL,
        comment_id BIGINT UNSIGNED NULL,
        reaction_type VARCHAR(20) NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        INDEX comment_id_index (comment_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";

	// SQL query to create the 'userwall_user_reputation' table.
	$sql_query_user_reputation = "CREATE TABLE IF NOT EXISTS $table_user_reputation (
        reputation_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        reputation_score BIGINT NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// SQL query to create the 'wp_userwall_hashtags' table.
	$sql_query_hashtags = "CREATE TABLE IF NOT EXISTS $table_hashtags (
        hashtag_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        hashtag_text VARCHAR(255) NOT NULL,
        INDEX hashtag_text_index (hashtag_text)
    )";

	// SQL query to create the 'wp_userwall_user_settings' table.
	$sql_query_user_settings = "CREATE TABLE IF NOT EXISTS $table_user_settings (
        setting_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_preferences TEXT,
        privacy_settings TEXT,
        display_options TEXT,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// SQL query to create the 'wp_userwall_notifications' table.
	$sql_query_notifications = "CREATE TABLE IF NOT EXISTS $table_notifications (
        notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        notification_type VARCHAR(255) NOT NULL,
        sender_user_id BIGINT UNSIGNED NOT NULL,
        receiver_user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT,
        timestamp DATETIME NOT NULL,
        INDEX sender_user_id_index (sender_user_id),
        INDEX receiver_user_id_index (receiver_user_id),
        FOREIGN KEY (sender_user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (receiver_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// SQL query to create the 'userwall_reports' table.
	$sql_query_reports = "CREATE TABLE IF NOT EXISTS $table_reports (
        report_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        reporter_user_id BIGINT UNSIGNED NOT NULL,
        reported_content_id BIGINT UNSIGNED NOT NULL,
        report_reason TEXT NOT NULL,
        report_date DATETIME NOT NULL,
        INDEX reporter_user_id_index (reporter_user_id),
        INDEX reported_content_id_index (reported_content_id),
        FOREIGN KEY (reporter_user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (reported_content_id) REFERENCES $table_posts(post_id)
    )";

	// SQL query to create the 'wp_userwall_search_history' table.
	$sql_query_search_history = "CREATE TABLE IF NOT EXISTS $table_search_history (
        search_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        search_query VARCHAR(255) NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// SQL query to create the 'wp_userwall_user_notifications' table.
	$sql_query_user_notifications = "CREATE TABLE IF NOT EXISTS $table_user_notifications (
        notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	$sql_query_badges = "CREATE TABLE IF NOT EXISTS $table_badges (
        badge_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        badge_name VARCHAR(255) NOT NULL,
        badge_description TEXT,
        badge_image_url VARCHAR(255) NOT NULL,
        INDEX badge_name_index (badge_name)
    )";

	$sql_query_blocklist = "CREATE TABLE IF NOT EXISTS $table_blocklist (
        blocklist_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        blocked_user_id BIGINT UNSIGNED NOT NULL,
        block_date DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        INDEX blocked_user_id_index (blocked_user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (blocked_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

	// Array of SQL queries for the first 5 tables.
	$sql_queries = array(
		$sql_query_posts,
		$sql_query_comments,
		$sql_query_user_reputation,
		$sql_query_badges,
		$sql_query_hashtags,
		$sql_query_user_settings,
		$sql_query_notifications,
		$sql_query_search_history,
		$sql_query_reports,
		$sql_query_user_notifications,
		$sql_query_blocklist,
		$sql_query_likes,
	);

	// Include the WordPress database upgrade file.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Execute the SQL queries to create the tables.
	foreach ( $sql_queries as $sql_query ) {
		dbDelta( $sql_query );
	}
}
