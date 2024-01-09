<?php
class ThreadsWP_Addon_Gallery extends Threads_WP_Base_Addon {
    public function get_id() {
        return 'gallery';
    }

    public function get_name() {
        return __( 'Gallery', 'thread-wp' );
    }

    public function get_description() {
        return __( 'Gallery', 'thread-wp' );
    }

    public function get_author() {
        return __( 'ThreadWP', 'thread-wp' );
    }

    public function get_version() {
        return '1.0';
    }

    public function activate_addon() {
        global $wpdb;
        
        $table_media = $wpdb->prefix . 'threads_media';
        $table_albums = $wpdb->prefix . 'threads_albums';

       // SQL query to create the 'threads_media' table
        $sql_query_media = "CREATE TABLE IF NOT EXISTS $table_media (
            media_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            file_path VARCHAR(255) NOT NULL,
            description TEXT,
            post_id INT UNSIGNED NOT NULL,
            INDEX post_id_index (post_id),
            FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
        )";

         // Array of SQL queries for the first 5 tables
        $sql_queries = array(
            $sql_query_media,
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

        $table_media = $wpdb->prefix . 'threads_media';

        // SQL queries to drop the tables
        $sql_queries = array(
            "DROP TABLE IF EXISTS $table_media",
        );

        // Delete the tables
        foreach ($sql_queries as $sql_query) {
            $wpdb->query($sql_query);
        }
    }

    public function hooks() {
        add_filter( 'thread_wp_post_tabs', array( $this, 'add_tab' ) );
        add_action( 'threads_wp_tab_content', array( $this, 'tab_content' ) );
        add_action( 'thread_wp_create_post', array( $this, 'upload_media_files' ) );
    }

    public function add_tab( $tabs = array() ) {
        $tabs['image'] = __( 'Image', 'threads-wp' );
        return $tabs;
    }

    public function tab_content() {
        ?>
        <div class="threads-tab-content" data-tab="image">
            <!-- Content textarea -->
            <textarea placeholder="Write your content here"></textarea>
            <!-- Image upload area -->
            <div class="image-upload-area">
                <label for="image-upload" class="upload-label">Click to Upload Images</label>
                <input type="file" accept="image/*" id="image-upload" multiple>
            </div>
        </div>
        <?php
    }

    public function upload_media_files() {

    }
}