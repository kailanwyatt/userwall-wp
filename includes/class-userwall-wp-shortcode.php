<?php
class UserWall_WP_Shortcode {
    public function __construct() {
        add_shortcode('userwall_wp_post_form', array( $this, 'userwall_wp_post_form_shortcode'), 10, 1 );
        add_action( 'wp_footer', array( $this, 'add_tmpls' ) );
    }

    public function add_tmpls() {
        include( USERWALL_WP_PLUGIN_DIR . 'templates/tmpls.php');
    }

    public function userwall_wp_post_form_shortcode( $atts = array() ) {
        // Extract shortcode attributes with defaults
        $atts = shortcode_atts(
            array(
                'type'         => 'posts',
                'per_page'     => '30',
                'page'         => 1,
                'object_id'    => 0,
                'show_userwall' => true,
                'show_form' => true
            ),
            $atts,
            'userwall_wp_post_form'
        );

        $per_page = absint( $atts['per_page'] );
        $type     = sanitize_text_field( $atts['type'] );
        $object_id = absint( $atts['object_id'] );
        $show_userwall = wp_validate_boolean( $atts['show_userwall'] );
        $show_form    = wp_validate_boolean( $atts['show_form'] );
        // Output the post form
        ob_start();
        $post_tabs = array(
            'post' => __( 'Post', 'userwall-wp' ),
        );

        $post_tabs = apply_filters( 'thread_wp_post_tabs', $post_tabs );
        include( USERWALL_WP_PLUGIN_DIR . 'templates/post-form.php'); // Create a post form template
        return ob_get_clean();
    }
}

new UserWall_WP_Shortcode();