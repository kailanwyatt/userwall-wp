<?php
class Threads_WP_AJAX_Manager {
    public function __construct() {
        // Add action hooks for handling AJAX requests
        add_action('wp_ajax_threads_wp_save_post', array( $this, 'threads_wp_save_post') );
        add_action('wp_ajax_fetch_data_by_thread', array( $this, 'fetch_data_by_thread') );
        add_action('wp_ajax_nopriv_fetch_data_by_thread', array( $this, 'fetch_data_by_thread') );

        add_action('wp_ajax_fetch_latest_thread_notice', array( $this, 'fetch_latest_thread_notice') );
        add_action('wp_ajax_nopriv_fetch_latest_thread_notice', array( $this, 'fetch_latest_thread_notice') );

        add_action('wp_ajax_fetch_latest_thread', array( $this, 'fetch_latest_thread') );
        add_action('wp_ajax_nopriv_fetch_latest_thread', array( $this, 'fetch_latest_thread') );

        // Post action.
        add_action('wp_ajax_threads_wp_posts_action', array( $this, 'handle_post_action') );

        add_action( 'wp_ajax_threads_wp_load_more_posts', array( $this, 'load_more_posts' ) );
        add_action( 'wp_ajax_nopriv_threads_wp_load_more_posts', array( $this, 'load_more_posts' ) );

        add_action( 'wp_ajax_threads_wp_load_comments', array( $this, 'load_comments' ) );
        add_action( 'wp_ajax_nopriv_threads_wp_load_comments', array( $this, 'load_comments' ) );
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

    public function handle_post_action() {
        // Check the nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'your_nonce_action' ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
        }

        // Get the action type from the AJAX request
        $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';

        // Get the post ID associated with the action (if needed)
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

        // Perform actions based on the action type
        switch ( $action_type ) {
            case 'edit':
                // Handle Edit action
                // You can perform your edit logic here
                break;
            case 'delete':
                // Handle Delete action
                // You can perform your delete logic here
                break;
            case 'block':
                // Handle Block action
                // You can perform your block logic here
                break;
            case 'report':
                // Handle Report action
                // You can perform your report logic here
                break;
            case 'embed-post':
                // Handle Embed Post action
                // You can perform your embed logic here
                break;
            case 'save':
                // Handle Save action
                // You can perform your save logic here
                break;
            case 'follow':
                // Handle Follow action
                // You can perform your follow logic here
                break;
            default:
                // Handle unknown action type
                wp_send_json_error( array( 'message' => 'Unknown action type.' ) );
        }

        // Send a success response (if needed)
        wp_send_json_success( array( 'message' => 'Action successful.' ) );
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
        $post_content = wp_kses_post( $_POST['content'] );

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

    public function fetch_data_by_thread() {
        $post_manager = new Threads_WP_Post_Manager();
        $posts = $post_manager->get_posts();
        wp_send_json($posts);
    }

    public function fetch_latest_thread_notice() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            //wp_send_json_error(array('message' => __( 'Invalid nonce.', 'threads-wp' ) ) );
        }

        $post_id = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
        $post_manager = new Threads_WP_Post_Manager();
        $posts = $post_manager->get_posts_latest_count( $post_id );
        wp_send_json_success( array('message' => $posts ) );
    }

    public function fetch_latest_thread() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            //wp_send_json_error(array('message' => __( 'Invalid nonce.', 'threads-wp' ) ) );
        }

        $post_id = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
        $post_manager = new Threads_WP_Post_Manager();
        $posts = $post_manager->get_posts_latest( $post_id );
        wp_send_json_success( array('threads' => $posts ) );
    }

    public function load_more_posts() {
        $posts = array();
        wp_send_json_success( array('threads' => $posts ) );
    }

    public function load_comments() {

    }
}

new Threads_WP_AJAX_Manager();