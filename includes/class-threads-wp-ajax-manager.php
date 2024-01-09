<?php
class Threads_WP_AJAX_Manager {
    public function __construct() {
        // Add action hooks for handling AJAX requests
        add_action('wp_ajax_threads_wp_save_post', array( $this, 'threads_wp_save_post') );
    }

    /**
     * Check if the AJAX nonce is valid.
     *
     * @return bool Whether the nonce is valid.
     */
    private function is_valid_nonce( $nonce_field = 'nonce', $nonce_action = 'threads_wp_nonce') {
        if (isset($_POST[ $nonce_field ]) && wp_verify_nonce($_POST[ $nonce_field ],  $nonce_action)) {
            return true;
        }
        return false;
    }

    public function threads_wp_save_post() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            wp_send_json_error(array('message' => __( 'Invalid nonce.', 'threads-wp' ) ) );
        }

        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(array('message' => __( 'You must be logged in to create a post.', 'threads-wp' ) ) );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = sanitize_text_field( $_POST['content'] );

        // Prepare data to insert into the threads_posts table
        $post_data = array(
            'content' => $post_content,
            'post_type'    => 'posts', // Change to your desired post type
            'creation_date' => current_time( 'mysql' ),
            'user_id'      => $current_user_id,
        );

        $post_manager = new Threads_WP_Post_Manager();
        $insert_result = $post_manager->create_post( $post_data );

        if ( $insert_result === false ) {
            wp_send_json_failure( array( 'message' => __( 'Post creation failed. Please try again.', 'threads-wp' ) ) );
        } else {
            $post = $post_manager->get_post_by_id( $insert_result );
            error_log( print_r( $post, true ) );
            $return_data = array(
                'post_type' => $post->post_type,
                'threads' => array(
                    $post
                )
            );
            wp_send_json_success( $return_data );
        }
    }

    public function handle_ajax_request() {
        // Check for nonce security
        if (!$this->is_valid_nonce()) {
            wp_send_json_error(array('message' => 'Invalid nonce.'));
        }

        // Your AJAX request handling logic goes here
        // You can access POST data using $_POST array
        // Process the request and prepare a response

        $response = array(
            'status' => 'success',
            'message' => 'AJAX request successful!',
            'data' => $_POST // Include any data you want to send back
        );

        // Send JSON response
        wp_send_json($response);
    }
}

new Threads_WP_AJAX_Manager();