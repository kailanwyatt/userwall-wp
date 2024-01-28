<?php
class UserWall_WP_Blocks {
	public function __construct() {
		// Register Gutenberg block
		add_action( 'init', array( $this, 'register_gutenberg_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'wp_userwall_enqueue_block_editor_assets' ) );
	}

	public function register_gutenberg_block() {
		register_block_type(
			'wp-userwall/thread-post',
			array(
				'editor_script' => 'wp-userwall-block-script',
				'editor_style'  => 'wp-userwall-block-editor-style',
				'style'         => 'wp-userwall-block-style',
			)
		);
	}

	public function wp_userwall_enqueue_block_editor_assets() {
		wp_enqueue_script(
			'wp-userwall-block-script',
			USERWALL_WP_PLUGIN_URL . '/assets/js/wp-userwall-block.js',
			array( 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element' )
		);

		wp_enqueue_style(
			'wp-userwall-block-editor-style',
			USERWALL_WP_PLUGIN_URL . '/assets/css/wp-userwall-block-editor.css',
			array( 'wp-edit-blocks' )
		);

		wp_enqueue_style(
			'wp-userwall-block-style',
			USERWALL_WP_PLUGIN_URL . '/assets/css/wp-userwall-block.css'
		);
	}
}

new UserWall_WP_Blocks();
