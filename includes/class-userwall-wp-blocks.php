<?php
/**
 * UserWall_WP_Blocks class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Blocks class
 */
class UserWall_WP_Blocks {
	/**
	 * Constructor
	 */
	public function __construct() {
		// Register Gutenberg block.
		add_action( 'init', array( $this, 'register_gutenberg_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'wp_userwall_enqueue_block_editor_assets' ) );
		add_filter( 'block_categories', array( $this, 'register_userwall_wp_category' ), 10, 2 );
	}

	/**
	 * Register the User Wall category.
	 *
	 * @param array $categories The existing block categories.
	 * @return array The modified block categories.
	 */
	public function register_userwall_wp_category( $categories ) {
		$data = array_merge(
			$categories,
			array(
				array(
					'slug'  => 'user-wall',
					'title' => __( 'User Wall', 'userwall-wp' ),
					'icon'  => 'smiley',
				),
			)
		);
		return $data;
	}

	/**
	 * Register the User Wall block.
	 */
	public function register_gutenberg_block() {
		register_block_type(
			'userwall-wp/page-render',
			array(
				'editor_script'   => 'wp-userwall-block-script',
				'render_callback' => array( $this, 'render_userwall_wp_block' ),
				'category'        => 'user-wall',
			)
		);
	}

	/**
	 * Render the User Wall block.
	 *
	 * @param array $attributes The block attributes.
	 * @return string The rendered block content.
	 */
	public function render_userwall_wp_block( $attributes ) {
		$type = 'wall-posts';
		// Server-side rendering of the block.
		if ( ! empty( $attributes['displayType'] ) ) {
			$type = $attributes['displayType'];
		}

		$result = '';
		switch ( $type ) {
			case 'wall-posts':
				$result = do_shortcode( '[userwall_wp_post_form]' );
				break;
			case 'profile':
				$result = do_shortcode( '[userwall_wp_profile]' );
				break;
			case 'single-post':
				$result = do_shortcode( '[userwall_wp_post_single]' );
				break;
		}

		return $result;
	}

	/**
	 * Enqueue block editor assets
	 */
	public function wp_userwall_enqueue_block_editor_assets() {
		wp_enqueue_script(
			'wp-userwall-block-script',
			USERWALL_WP_PLUGIN_URL . '/assets/js/wp-userwall-block.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( USERWALL_WP_PLUGIN_DIR . '/assets/js/wp-userwall-block.js' ),
			array( 'in_footer' => true )
		);
	}
}

new UserWall_WP_Blocks();
