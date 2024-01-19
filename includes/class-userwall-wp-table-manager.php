<?php
class Threads_WP_Table_Manager {
    // Method to get the table names
    public static function get_table_names() {
        global $wpdb;

        // Define the table names with the "threads_" prefix
        $table_names = array(
            'posts' => $wpdb->prefix . 'threads_posts',
            'comments' => $wpdb->prefix . 'threads_comments',
            'likes' => $wpdb->prefix . 'threads_likes',
            'bookmarks' => $wpdb->prefix . 'threads_bookmarks',
            'groups' => $wpdb->prefix . 'threads_groups',
            'polls' => $wpdb->prefix . 'threads_polls',
            // Add more table names here as needed
        );

        return $table_names;
    }
}
