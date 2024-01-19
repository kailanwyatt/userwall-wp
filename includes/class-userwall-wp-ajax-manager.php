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

        add_action( 'wp_ajax_threads_wp_update_post', array( $this, 'threads_wp_update_post' ) );
        add_action( 'wp_ajax_threads_wp_update_comment', array( $this, 'threads_wp_update_comment' ) );

        add_action( 'wp_ajax_threads_wp_post_comment', array( $this, 'post_comment' ) );

        add_action('wp_ajax_thread_wp_comment_reply', array($this, 'comment_reply_callback'));
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
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            wp_send_json_error(array('message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
        }

        // Get the action type from the AJAX request
        $action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';

        // Get the post ID associated with the action (if needed)
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

        $current_user_id = get_current_user_id();
        $post_manager = new Threads_WP_Post_Manager();

        // Perform actions based on the action type
        switch ( $action_type ) {
            case 'Delete':
                if ( $post_manager->can_moderate( $post_id, $current_user_id ) ) {
                    // Handle Delete action
                   
                    $posts = $post_manager->delete_post( $post_id );
                } else {
                    error_log( 'Can not delete ' );
                }
                break;
            case 'Block':
                $blocked_user_id = $post_manager->get_user_id_by_post_id();
                // Handle Block action
                $post_manager->block_user_for_post( $current_user_id, $blocked_user_id, $post_id );
                // You can perform your block logic here
                break;
            case 'Report':
                // Handle Report action
                // You can perform your report logic here
                break;
            case 'Embed-post':
                // Handle Embed Post action
                // You can perform your embed logic here
                break;
            case 'Save':
                // Handle Save action
                $post_manager->save_post_to_bookmarks( $current_user_id, $post_id );
                break;
            case 'Follow':
                // Handle Follow action
                //$post->follow_author_of_post()
                break;
            default:
                // Handle unknown action type
                wp_send_json_error( array( 'message' => 'Unknown action type.' ) );
        }

        // Send a success response (if needed)
        wp_send_json_success( array( 'message' => 'Action successful.' ) );
    }

    public function threads_wp_update_comment() {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(array('message' => __( 'You must be logged in to update comment.', 'userwall-wp' ) ) );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = ! empty( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';

        $post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        $comment_id = ! empty( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;

        $post_manager = new Threads_WP_Post_Manager();
        $insert_result = $post_manager->update_comment( $comment_id, $post_content );

        if ( $insert_result === false ) {
            wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
        } else {
            $comments = $post_manager->get_comment_by_id( $comment_id );
            $return_data = array(
                'post_id'  => $post_id,
                'comments' => array(
                    $comments
                )
            );
            wp_send_json_success( $return_data );
        }
    }

    public function threads_wp_update_post() {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(array('message' => __( 'You must be logged in to create a post.', 'userwall-wp' ) ) );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = ! empty( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';

        $post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        // Prepare data to insert into the threads_posts table
        $post_data = array(
            'content' => $post_content,
        );

        $post_manager = new Threads_WP_Post_Manager();
        $insert_result = $post_manager->update_post( $post_id, $post_data );

        if ( $insert_result === false ) {
            wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
        } else {
            $post = $post_manager->get_post_by_id( $post_id );
            $return_data = array(
                'post_type' => $post->post_type,
                'threads' => array(
                    $post
                )
            );
            wp_send_json_success( $return_data );
        }
    }

    public function threads_wp_save_post() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            wp_send_json_error(array('message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
        }

        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(array('message' => __( 'You must be logged in to create a post.', 'userwall-wp' ) ) );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = ! empty( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';

        $post_tab = ! empty( $_POST['post_tab'] ) ? sanitize_text_field( $_POST ) : 'post';

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
            wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
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
        $per_page  = ! empty( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 30;
        $page      = ! empty( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
        $object_id = ! empty( $_GET['object_id'] ) ? absint( $_GET['object_id'] ) : 30;
        $post_type = ! empty( $_GET['post_type'] ) ? absint( $_GET['post_type'] ) : 'posts';
        $args = array(
            'per_page'  => $per_page,
            'page'      => $page,
            'object_id' => $object_id,
            'type'      => $post_type,
        );
        $post_manager = new Threads_WP_Post_Manager();
        $posts = $post_manager->get_posts( $args );
        wp_send_json(array( 'threads' => $posts, 'params' => $args ) );
    }

    public function fetch_latest_thread_notice() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            //wp_send_json_error(array('message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
        }

        $post_id = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
        $post_manager = new Threads_WP_Post_Manager();
        $posts = $post_manager->get_posts_latest_count( $post_id );
        wp_send_json_success( array('message' => $posts ) );
    }

    public function fetch_latest_thread() {
        // Check for nonce security
        if ( ! $this->is_valid_nonce() ) {
            //wp_send_json_error(array('message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
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
        $post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
        $post_manager = new Threads_WP_Post_Manager();
        $comments = $post_manager->get_comments_by_post_id( $post_id );
        if ( empty( $comments ) ) {
            $return_data = array(
                'comments' => array()
            );
        } else {
            $return_data = array(
                'comments' => array(
                    $comments
                )
            );
        }
        
        wp_send_json_success( $return_data );
    }

    public function post_comment() {
        // Check if user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(array('message' => __( 'You must be logged in to add a comment.', 'userwall-wp' ) ) );
        }

        // Get current user ID
        $current_user_id = get_current_user_id();

        // Get post content from the form
        $post_content = ! empty( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';

        $post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        // Prepare data to insert into the threads_posts table
        $post_data = array(
            'content' => $post_content,
        );

        $post_manager = new Threads_WP_Post_Manager();
        $insert_result = $post_manager->add_comment( $current_user_id, $post_id, $post_content );

        if ( $insert_result === false ) {
            wp_send_json_error( array( 'message' => __( 'Could not add comment. Please try again.', 'userwall-wp' ) ) );
        } else {
            $comments = $post_manager->get_comment_by_id( $insert_result );
            $return_data = array(
                'comments' => array(
                    $comments
                )
            );
            wp_send_json_success( $return_data );
        }
    }

    public function comment_reply_callback() {
        // Get the comment content from the AJAX request
        $comment_content = sanitize_text_field($_POST['commentContent']);

        // Get current user ID
        $current_user_id = get_current_user_id();

        $post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        $parent_comment = ! empty( $_POST['commentId']) ? intval($_POST['commentId']) : 0;

        $post_manager = new Threads_WP_Post_Manager();
        $insert_result = $post_manager->add_comment( $current_user_id, $post_id, $comment_content, $parent_comment );

        if ( $insert_result === false ) {
            wp_send_json_error( array( 'message' => __( 'Could not add comment. Please try again.', 'userwall-wp' ) ) );
        } else {
            $comments = $post_manager->get_comment_by_id( $insert_result );
            $return_data = array(
                'comments' => array(
                    $comments
                )
            );
            wp_send_json_success( $return_data );
        }
    }
}

new Threads_WP_AJAX_Manager();