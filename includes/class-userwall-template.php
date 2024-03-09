<?php
/**
 * UserWall_Template class
 *
 * @package UserWall_WP
 */

/**
 * UserWall_Template class
 */
class UserWall_Template {
	/**
	 * Get a template from the plugin's templates folder
	 *
	 * @param string $template_name The name of the template file.
	 * @return mixed
	 */
	public static function get_template( $template_name = '' ) {
		// Check if the template file exists in the theme folder.
		$theme_template = locate_template( 'userwall-wp/' . $template_name );

		// If the template file exists in the theme folder, use it.
		if ( $theme_template ) {
			return $theme_template;
		}

		// If not found in the theme, use the plugin's default template.
		return USERWALL_WP_PLUGIN_DIR . 'templates/' . $template_name;
	}

	/**
	 * Enqueue Assets needed.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		// Enqueue JavaScript.
		wp_enqueue_script( 'userwall-wp-js', USERWALL_WP_PLUGIN_URL . '/assets/js/userwall-wp.js', array( 'jquery', 'wp-util', 'wp-hooks', 'jquery-ui-core' ), USERWALL_WP_VERSION, true );

		$ajax_nonce = wp_create_nonce( 'userwall_wp_nonce' );

		$options = get_option( 'userwall_wp' );

		// Create an array to pass data to JavaScript.
		$userwall_wp_data = array(
			'ajax_url'           => admin_url( 'admin-ajax.php' ), // WordPress AJAX URL.
			'nonce'              => $ajax_nonce,
			'user_id'            => get_current_user_id(),
			'reply_placeholder'  => __( 'What are you\'re thoughs?', 'userwall-wp' ),
			'enable_rich_editor' => ! empty( $options['enable_rich_editor'] ) ? true : false,
			'toolbar'            => ! empty( $options['editor_options'] ) ? array_keys( $options['editor_options'] ) : array(),
			'char_limit'         => ! empty( $options['character_limit'] ) ? absint( $options['character_limit'] ) : 0,
			'user_wall'          => get_query_var( 'profile_id' ),
			'settings'           => array(
				'allow_titles' => ! empty( $options['allow_titles'] ) ? true : false,
				'open_posts'   => ! empty( $options['open_posts'] ) ? true : false,
			),
			'isSinglePost'       => get_query_var( 'thread_id' ),
		);

		// Localize the script with the data.
		wp_localize_script( 'userwall-wp-js', 'userwallWPObject', apply_filters( 'userwall_wp_localize_script', $userwall_wp_data ) );

		// Enqueue CSS.
		wp_enqueue_style( 'userwall-wp-css', USERWALL_WP_PLUGIN_URL . '/assets/css/userwall-wp.css', array(), USERWALL_WP_VERSION, 'all' );
	}
}
