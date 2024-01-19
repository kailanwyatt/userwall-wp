<?php
class UserWallWP_Addon_Groups extends Threads_WP_Base_Addon {
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
        return __( 'ThreadWP', 'userwall-wp' );
    }

    public function get_version() {
        return '1.0';
    }

    public function activate_addon() {
        global $wpdb;
        
        $table_posts = $wpdb->prefix . 'threads_posts';
        $table_groups = $wpdb->prefix . 'threads_groups';

        // SQL query to create the 'threads_plugin_groups' table
        $sql_query_groups = "CREATE TABLE IF NOT EXISTS $table_groups (
            group_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            group_name VARCHAR(255) NOT NULL,
            group_description TEXT,
            creation_date DATETIME NOT NULL,
            creator_user_id BIGINT UNSIGNED NOT NULL,
            INDEX creator_user_id_index (creator_user_id),
            FOREIGN KEY (creator_user_id) REFERENCES {$wpdb->prefix}users(ID)
        )";
        
         // Array of SQL queries for the first 5 tables
        $sql_queries = array(
            $sql_query_groups,  
        );

        // Include the WordPress database upgrade file
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Execute the SQL queries to create the tables
        foreach ($sql_queries as $sql_query) {
            dbDelta($sql_query);
        }
    }

    public function deactivate_addon() {
        global $wpdb;

        $table_groups = $wpdb->prefix . 'threads_groups';

        // SQL queries to drop the tables
        $sql_queries = array(
            "DROP TABLE IF EXISTS $table_groups",
        );

        // Delete the tables
        foreach ($sql_queries as $sql_query) {
            $wpdb->query($sql_query);
        }
    }
}
