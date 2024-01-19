<?php
class UserWallWP_Addon_Polls extends Threads_WP_Base_Addon {
    public function get_id() {
        return 'polls';
    }

    public function get_name() {
        return __( 'Polls', 'userwall-wp' );
    }

    public function get_description() {
        return __( 'Polls', 'userwall-wp' );
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
        $table_polls = $wpdb->prefix . 'threads_polls';
        $table_poll_votes = $wpdb->prefix . 'threads_poll_votes';
        $table_poll_options = $wpdb->prefix . 'threads_poll_options';

        // SQL query to create the 'threads_polls' table
        $sql_query_polls = "CREATE TABLE IF NOT EXISTS $table_polls (
            poll_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            question_text TEXT NOT NULL,
            creation_date DATETIME NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            post_id INT UNSIGNED NOT NULL,
            INDEX user_id_index (user_id),
            INDEX post_id_index (post_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
        )";

        // SQL query to create the 'threads_poll_votes' table
        $sql_query_poll_votes = "CREATE TABLE IF NOT EXISTS $table_poll_votes (
            vote_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            poll_id INT UNSIGNED NOT NULL,
            selected_option INT NOT NULL,
            INDEX user_id_index (user_id),
            INDEX poll_id_index (poll_id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
            FOREIGN KEY (poll_id) REFERENCES $table_polls(poll_id)
        )";

        // SQL query to create the 'wp_threads_poll_options' table
        $sql_query_poll_options = "CREATE TABLE IF NOT EXISTS $table_poll_options (
            option_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            poll_id INT UNSIGNED NOT NULL,
            option_text TEXT NOT NULL,
            INDEX poll_id_index (poll_id),
            FOREIGN KEY (poll_id) REFERENCES wp_threads_polls(poll_id)
        )";

         // Array of SQL queries for the first 5 tables
        $sql_queries = array(
            $sql_query_polls,  
            $sql_query_poll_votes,
            $sql_query_poll_options
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

        $table_polls = $wpdb->prefix . 'threads_polls';
        $table_poll_votes = $wpdb->prefix . 'threads_poll_votes';
        $table_poll_options = $wpdb->prefix . 'threads_poll_options';

        // SQL queries to drop the tables
        $sql_queries = array(
            "DROP TABLE IF EXISTS $table_poll_votes",
            "DROP TABLE IF EXISTS $table_poll_options",
            "DROP TABLE IF EXISTS $table_polls",
        );

        // Delete the tables
        foreach ($sql_queries as $sql_query) {
            $wpdb->query($sql_query);
        }
    }

    public function hooks() {
        add_filter( 'thread_wp_post_tabs', array( $this, 'add_poll_tab' ) );
    }

    public function add_poll_tab( $tabs = array() ) {
        $tabs['poll'] = __( 'Poll', 'userwall-wp' );
        return $tabs;
    }
}