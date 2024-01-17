<?php
class Threads_Template {
    // Get a template from the plugin's templates folder
    public static function get_template($template_name) {
        // Check if the template file exists in the theme folder
        $theme_template = locate_template('threads-wp/' . $template_name);

        // If the template file exists in the theme folder, use it
        if ($theme_template) {
            return $theme_template;
        }

        // If not found in the theme, use the plugin's default template
        return THREADS_WP_PLUGIN_DIR . 'templates/' . $template_name;
    }

    public function enqueue_assets() {
        // Enqueue JavaScript
        wp_enqueue_script('threads-wp-js', THREADS_WP_PLUGIN_URL . '/assets/js/threads-wp.js' , array('jquery', 'wp-util', 'wp-hooks', 'jquery-ui-core'), '1.0', true);
        
        $ajax_nonce = wp_create_nonce('threads_wp_nonce');

        // Create an array to pass data to JavaScript
        $threadsWP_data = array(
            'ajax_url' => admin_url('admin-ajax.php'), // WordPress AJAX URL
            'nonce'    => $ajax_nonce,
            'user_id'  => get_current_user_id(),
            'reply_placeholder' => __( 'What are you\'re thoughs?', 'threads-wp' ),
        );

        // Localize the script with the data
        wp_localize_script('threads-wp-js', 'threadsWPObject', apply_filters( 'thread_wp_localize_script', $threadsWP_data ) );


        // Enqueue CSS
        wp_enqueue_style('threads-wp-css', THREADS_WP_PLUGIN_URL . '/assets/css/threads-wp.css', array(), '1.0', 'all');
    }
}