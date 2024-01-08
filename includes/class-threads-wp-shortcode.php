<?php
class Threads_WP_Shortcode {
    public function __construct() {
        add_shortcode('threads_wp_post_form', array( $this, 'threads_wp_post_form_shortcode') );

        add_action('wp_ajax_threads_wp_save_post', array( $this, 'threads_wp_save_post') );
        add_action( 'wp_footer', array( $this, 'add_tmpls' ) );
    }

    public function add_tmpls() {
        include( THREADS_WP_PLUGIN_DIR . 'templates/tmpls.php');
    }

    function threads_wp_save_post() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'threads_wp_nonce' ) ) {
            die( 'Permission check failed. Please try again.' );
        }

        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            die( 'You must be logged in to create a post.' );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = sanitize_text_field( $_POST['content'] );

        // You may want to add more sanitization and validation here

        global $wpdb;
        $table_posts = $wpdb->prefix . 'threads_posts';

        // Prepare data to insert into the threads_posts table
        $post_data = array(
            'post_content' => $post_content,
            'post_type'    => 'your_post_type', // Change to your desired post type
            'creation_date' => current_time( 'mysql' ),
            'user_id'      => $current_user_id,
        );

        // Insert data into the threads_posts table
        $insert_result = $wpdb->insert( $table_posts, $post_data );

        if ( $insert_result === false ) {
            wp_send_json_failure( array( 'message' => __( 'Post creation failed. Please try again.', 'threads-wp' ) ) );
        } else {
            $post_id = $wpdb->insert_id;
            // Success! You can redirect or perform other actions here.
            wp_send_json_success(
                array(

                )
            );
        }
    }

    public function threads_wp_post_form_shortcode($atts) {
        // Process any shortcode attributes if needed
    
        // Output the post form
        ob_start();
        $post_tabs = array(
            'post' => __( 'Post', 'threads-wp' ),
            'image' => __( 'Image', 'threads-wp' ),
        );

        $post_tabs = apply_filters( 'thread_wp_post_tabs', $post_tabs );
        include( THREADS_WP_PLUGIN_DIR . 'templates/post-form.php'); // Create a post form template
        return ob_get_clean();
    }
}

new Threads_WP_Shortcode();