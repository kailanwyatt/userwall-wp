<?php
/**
 * UserWall_WP_Blocks class
 */
class UserWall_WP_Blocks {
	public function __construct() {
		// Register Gutenberg block
		add_action( 'init', array( $this, 'register_gutenberg_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'wp_userwall_enqueue_block_editor_assets' ) );
		add_filter( 'block_categories', array( $this, 'register_user_wall_category' ), 10, 2 );
	}

	public function register_user_wall_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'user-wall',
					'title' => __( 'User Wall', 'user-wall' ),
					'icon'  => 'smiley',
				),
			)
		);
	}

	public function register_gutenberg_block() {
		register_block_type(
			'userwall-wp/page-render',
			array(
				'editor_script'   => 'wp-userwall-block-script',
				'render_callback' => array( $this, 'render_user_wall_block' ),
				'category'        => 'user-wall',
			)
		);
	}

	public function render_user_wall_block( $attributes ) {
		$type = 'wall-posts';
		// Server-side rendering of the block.
		if ( ! empty( $attributes['displayType'] ) ) {
			$type = $attributes['displayType'];
		}

		switch ( $type ) {
			case 'wall-posts':
				return do_shortcode( '[userwall_wp_post_form]' );
				break;
			case 'profile':
				return do_shortcode( '[userwall_wp_profile]' );
				break;
			case 'single-post':
				return do_shortcode( '[userwall_wp_post_single]' );
				break;
		}
	}

	public function wp_userwall_enqueue_block_editor_assets() {
		wp_enqueue_script(
			'wp-userwall-block-script',
			USERWALL_WP_PLUGIN_URL . '/assets/js/wp-userwall-block.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' )
		);
	}
}

new UserWall_WP_Blocks();
