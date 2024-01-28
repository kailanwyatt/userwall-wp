<?php
class UserWallWP_Addon_Followers extends UserWall_WP_Base_Addon {

    public function __construct() {
        parent::__construct();
    }

    public function get_id() {
        return 'followers';
    }

    public function get_name() {
        return __( 'Followers', 'userwall-wp' );
    }

    public function get_description() {
        return __( 'This addon adds ability to have users follow each other.', 'userwall-wp' );
    }

    public function get_author() {
        return 'UserWallWP'; // Author of the Files addon
    }

    public function get_version() {
        return '1.0';
    }

    public function is_ready() {
        return false;
    }

    // Implement addon-specific activation logic
    public function activate_addon() {
        global $wpdb;
        $table_user_followers = $wpdb->prefix . 'userwall_user_followers';
        $table_posts = $wpdb->prefix . 'userwall_posts';

        // SQL query to create the 'userwall_plugin_bookmarks' table
        $sql_query_user_followers = "CREATE TABLE IF NOT EXISTS $table_user_followers (
            follower_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            follower_user_id BIGINT UNSIGNED NOT NULL,
            INDEX user_id_index (user_id),
            INDEX follower_user_id_index (follower_user_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (follower_user_id) REFERENCES {$wpdb->prefix}users(ID)
        )";

        // Include the WordPress database upgrade file
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_query_user_followers);
    }

    // Implement addon-specific deactivation logic
    public function deactivate_addon() {
        global $wpdb;
        // Check if the option for deletion is set to true
        $delete_on_deactivation = get_option('userwall_wp_delete_deactivation', false);

        // If the option is set to true, delete the tables
        if ($delete_on_deactivation) {
            $table_media = $wpdb->prefix . 'userwall_media';

            // SQL queries to drop the tables
            $sql_queries = array(
                "DROP TABLE IF EXISTS $table_media",
            );

            // Delete the tables
            foreach ($sql_queries as $sql_query) {
                $wpdb->query($sql_query);
            }

        }
    }

    // Implement addon-specific hooks and actions
    public function hooks() {
        // Add hooks and actions specific to the Files addon here
    }
}
