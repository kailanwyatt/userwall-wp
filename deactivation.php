<?php

function threads_wp_deactivate() {
    // Check if the option for deletion is set to true
    $delete_on_deactivation = get_option('threads_wp_delete_deactivation', false);

    // If the option is set to true, delete the tables
    if ($delete_on_deactivation) {
        global $wpdb;

        // Define the table names with the "threads_" prefix
        $table_posts = $wpdb->prefix . 'threads_posts';
        $table_comments = $wpdb->prefix . 'threads_comments';
        $table_likes = $wpdb->prefix . 'threads_likes';
        $table_bookmarks = $wpdb->prefix . 'threads_bookmarks';
        $table_polls = $wpdb->prefix . 'threads_polls';
        $table_poll_votes = $wpdb->prefix . 'threads_poll_votes';
        $table_media = $wpdb->prefix . 'threads_media';
        $table_albums = $wpdb->prefix . 'threads_albums';
        $table_reports = $wpdb->prefix . 'threads_reports';
        $table_user_reputation = $wpdb->prefix . 'threads_user_reputation';
        $table_badges = $wpdb->prefix . 'threads_badges';
        $table_hashtags = $wpdb->prefix . 'threads_hashtags';
        $table_user_settings = $wpdb->prefix . 'threads_user_settings';
        $table_notifications = $wpdb->prefix . 'threads_notifications';
        $table_search_history = $wpdb->prefix . 'threads_search_history';
        $table_user_followers = $wpdb->prefix . 'threads_user_followers';
        $table_user_following = $wpdb->prefix . 'threads_user_following';
        $table_reports = $wpdb->prefix . 'threads_reports';
        $table_user_notifications = $wpdb->prefix . 'threads_user_notifications';
        $table_poll_options = $wpdb->prefix . 'threads_poll_options';
        $table_blocklist = $wpdb->prefix . 'threads_blocklist';

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
        foreach ($sql_queries as $sql_query) {
            $wpdb->query($sql_query);
        }
    }
}
