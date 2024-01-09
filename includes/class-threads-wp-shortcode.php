<?php
class Threads_WP_Shortcode {
    public function __construct() {
        add_shortcode('threads_wp_post_form', array( $this, 'threads_wp_post_form_shortcode') );
        add_action( 'wp_footer', array( $this, 'add_tmpls' ) );
    }

    public function add_tmpls() {
        include( THREADS_WP_PLUGIN_DIR . 'templates/tmpls.php');
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