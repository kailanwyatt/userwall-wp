<?php
class Threads_WP_Blocks {
    public function __construct() {
        // Register Gutenberg block
        add_action('init', array($this, 'register_gutenberg_block'));
        add_action('enqueue_block_editor_assets', array($this, 'wp_threads_enqueue_block_editor_assets' ) );
    }

    public function register_gutenberg_block() {
        register_block_type('wp-threads/thread-post', array(
            'editor_script' => 'wp-threads-block-script',
            'editor_style'  => 'wp-threads-block-editor-style',
            'style'         => 'wp-threads-block-style',
        ));
    }

    public function wp_threads_enqueue_block_editor_assets() {
        wp_enqueue_script(
            'wp-threads-block-script',
            THREADS_WP_PLUGIN_URL . '/assets/js/wp-threads-block.js',
            array('wp-blocks', 'wp-components', 'wp-editor', 'wp-element')
        );
    
        wp_enqueue_style(
            'wp-threads-block-editor-style',
            THREADS_WP_PLUGIN_URL . '/assets/css/wp-threads-block-editor.css',
            array('wp-edit-blocks')
        );
    
        wp_enqueue_style(
            'wp-threads-block-style',
            THREADS_WP_PLUGIN_URL . '/assets/css/wp-threads-block.css'
        );
    }
}

new Threads_WP_Blocks();