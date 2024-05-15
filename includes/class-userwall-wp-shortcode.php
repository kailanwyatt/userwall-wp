<?php
/**
 * UserWall_WP_Shortcode class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Shortcode class
 */
class UserWall_WP_Shortcode {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'userwall_wp_post_form', array( $this, 'userwall_wp_post_form_shortcode' ), 10, 1 );
		add_shortcode( 'userwall_wp_post_single', array( $this, 'userwall_wp_post_single_shortcode' ), 10, 1 );
		add_shortcode( 'userwall_wp_profile', array( $this, 'userwall_wp_profile_shortcode' ), 10, 1 );
	}

	/**
	 * Userwall Shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function userwall_wp_post_form_shortcode( $atts = array() ) {
		// Extract shortcode attributes with defaults.
		$atts = shortcode_atts(
			array(
				'type'          => 'posts',
				'per_page'      => '30',
				'page'          => 1,
				'object_id'     => 0,
				'show_userwall' => true,
				'show_form'     => true,
			),
			$atts,
			'userwall_wp_post_form'
		);

		$per_page      = absint( $atts['per_page'] );
		$type          = sanitize_text_field( $atts['type'] );
		$object_id     = absint( $atts['object_id'] );
		$show_userwall = wp_validate_boolean( $atts['show_userwall'] );
		$show_form     = wp_validate_boolean( $atts['show_form'] );
		$options       = userwall_wp_get_options();
		$use_editor    = ! empty( $options['enable_rich_editor'] ) ? true : false;
		// Output the post form.
		ob_start();
		$post_tabs = array(
			'post' => __( 'Post', 'userwall-wp' ),
		);

		$post_tabs      = apply_filters( 'userwall_wp_post_tabs', $post_tabs );
		$post_types     = userwall_wp_get_post_types();
		$content_types  = userwall_wp_get_content_types();
		$max_characters = ! empty( $options['character_limit'] ) ? absint( $options['character_limit'] ) : 0;
		$allow_tiltes   = ! empty( $options['allow_tiltes'] ) ? absint( $options['allow_tiltes'] ) : 0;
		include USERWALL_WP_PLUGIN_DIR . 'templates/post-form.php'; // Create a post form template.
		return ob_get_clean();
	}

	/**
	 * User Profile Shortcode callback
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function userwall_wp_profile_shortcode( $atts = array() ) {
		// Extract shortcode attributes with defaults.
		$atts                = shortcode_atts(
			array(
				'type'          => 'posts',
				'per_page'      => '30',
				'page'          => 1,
				'object_id'     => 0,
				'show_userwall' => true,
				'show_form'     => true,
			),
			$atts,
			'userwall_wp_post_form'
		);
		$args['profile_tab'] = get_query_var( 'profile_tab' );
		$args['profile_id']  = get_query_var( 'profile_id' );
		if ( ! $args['profile_tab'] ) {
			$args['profile_tab'] = 'main';
		}

		ob_start();
		userwall_wp_get_template( 'profile.php', $args );
		return ob_get_clean();
	}

	/**
	 * Single Post Shortcode callback.
	 */
	public function userwall_wp_post_single_shortcode() {
		ob_start();
		$args = array(
			'post_id' => get_query_var( 'thread_id' ),
		);
		userwall_wp_get_template( 'post-single.php', $args );
		return ob_get_clean();
	}
}

new UserWall_WP_Shortcode();
