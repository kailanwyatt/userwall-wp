<?php
/**
 * UserWall_Template class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_register_script( 'userwall-wall-tmpl', '', array(), '', USERWALL_WP_VERSION, array( 'in_footer' => true ) );
		wp_enqueue_script( 'userwall-wall-tmpl' );
		wp_add_inline_script( 'userwall-wall-tmpl', $this->get_wall_template() );

		// Add the comments template.
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_register_script( 'userwall-wp-comments', '', array(), '', USERWALL_WP_VERSION, array( 'in_footer' => true ) );
		wp_enqueue_script( 'userwall-wp-comments' );
		wp_add_inline_script( 'userwall-wp-comments', $this->get_wall_comments_template() );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_register_script( 'userwall-wp-inline', '', array(), '', USERWALL_WP_VERSION, array( 'in_footer' => true ) );
		wp_enqueue_script( 'userwall-wp-inline' );
		wp_add_inline_script( 'userwall-wp-inline', apply_filters( 'userwall_wp_inline_js', '' ) );
		// Enqueue CSS.
		wp_enqueue_style( 'userwall-wp-css', USERWALL_WP_PLUGIN_URL . '/assets/css/userwall-wp.css', array(), USERWALL_WP_VERSION, 'all' );
	}

	/**
	 * Add defer attribute to the script tag
	 *
	 * @param array $attributes The script tag attributes.
	 *
	 * @return array
	 */
	public function add_script_attrs_to_wall_tmpl( $attributes = array() ) {

		if ( empty( $attributes['id'] ) ) {
			return $attributes;
		}

		if ( strpos( $attributes['id'], 'userwall-wp-comments' ) !== false ) {
			$attributes['type'] = 'text/html';
			$attributes['id']   = 'tmpl-userwall-wp-thread-comment-template';
		}

		if ( strpos( $attributes['id'], 'userwall-wall-tmpl' ) !== false ) {
			$attributes['type'] = 'text/html';
			$attributes['id']   = 'tmpl-userwall-wp-feed-template';
		}

		return $attributes;
	}

	/**
	 * Get the wall template
	 *
	 * @return string
	 */
	private function get_wall_template() {
		ob_start();
		include USERWALL_WP_PLUGIN_DIR . 'templates/wall.php';
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	/**
	 * Get the wall comments template
	 *
	 * @return string
	 */
	private function get_wall_comments_template() {
		ob_start();
		include USERWALL_WP_PLUGIN_DIR . 'templates/wall-comments.php';
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	/**
	 * Add module attribute to the script tag
	 *
	 * @param array  $html The script tag html.
	 * @param string $handle     The script handle.
	 *
	 * @return array
	 */
	public function add_module_attribute( $html = '', $handle = '' ) {
		if ( 'userwall-wp-js' === $handle ) {
			$html = str_replace( '></script>', ' type="module"></script>', $html );
		}

		return $html;
	}
}
