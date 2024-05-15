<?php
/**
 * This file is the helper functions for User Wall.
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get the template.
 *
 * @param string $template_name Name of the template.
 * @param array  $args Arguments for the template.
 * @return void
 */
function userwall_wp_get_template( $template_name = '', $args = array() ) {
	// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	extract( $args );
	include UserWall_Template::get_template( $template_name, $args );
}

/**
 * Helper function to get data from UserWall_WP_Profile.
 *
 * @param string   $profile_key Key for the profile data to retrieve.
 * @param int|null $user_id User ID. If not set, use current user ID.
 * @return mixed The requested user profile data.
 */
function userwall_wp_get_userwall_wp_profile_data( $profile_key = '', $user_id = null ) {
	global $uwwp_profile_info;
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// Initialize UserWall_WP_Profile class.
	$user_profile = ! empty( $uwwp_profile_info ) && $uwwp_profile_info instanceof UserWall_WP_Profile ? $uwwp_profile_info : new UserWall_WP_Profile( $user_id );

	// Determine which data to return based on the provided profile_key.
	switch ( $profile_key ) {
		case 'username':
			return $user_profile->get_username();
		case 'display_name':
			return $user_profile->get_display_name();
		case 'user_posts':
			return $user_profile->get_user_posts();
		case 'post_count':
			return $user_profile->get_user_post_count();
		case 'avatar':
			return $user_profile->get_user_avatar();
		case 'bio':
			return $user_profile->get_user_bio();
		default:
			// Optionally handle unknown profile keys.
			return null;
	}
}

/**
 * Get the user profile URL.
 *
 * @param string      $username The username for the profile.
 * @param string|null $profile_tab Optional profile tab.
 * @return string The user profile URL.
 */
function userwall_wp_get_user_profile_url( $username, $profile_tab = null ) {
	// Initialize the URL.
	$url = home_url();
	// Check if permalinks are enabled ('plain' indicates they are not).
	if ( '' === get_option( 'permalink_structure' ) ) {
		// Build a plain URL.
		$url     = home_url();
		$options = userwall_wp_get_options();
		if ( ! empty( $options['user_page'] ) ) {
			$url = get_permalink( $options['user_page'] );
		}
		$url = add_query_arg(
			array(
				'username'     => rawurlencode( $username ),
				'user_profile' => 1,
			),
			$url
		);
		$url = home_url( '/' ) . '?pagename=u&username=' . rawurlencode( $username ) . '&user_profile=1';
		if ( $profile_tab ) {
			$url = add_query_arg( array( 'profile_tab' => rawurlencode( $profile_tab ) ), $url );
		}
	} else {
		// Build a pretty URL.
		$options = userwall_wp_get_options();
		if ( ! empty( $options['user_page'] ) ) {
			$url = get_permalink( $options['user_page'] );
			$url = rtrim( $url, '/' ) . '/' . $username . '/';
			if ( $profile_tab ) {
				$url = rtrim( $url, '/' ) . '/' . $profile_tab . '/';
			}
		}
	}

	return $url;
}

/**
 * Get the permalink for a post.
 *
 * @param int $post_id The ID of the post.
 * @return string The permalink for the post.
 */
function userwall_wp_get_permalink( $post_id = 0 ) {
	$url = '';
	// Check if permalinks are enabled ('plain' indicates they are not).
	if ( '' === get_option( 'permalink_structure' ) ) {
		// Build a plain URL.
		$url     = home_url();
		$options = userwall_wp_get_options();
		if ( ! empty( $options['single_post_page'] ) ) {
			$url = get_permalink( $options['single_post_page'] );
		}
		$url = add_query_arg(
			array(
				'post' => absint( $post_id ),
			),
			$url
		);
	} else {
		// Build a pretty URL.
		$options = userwall_wp_get_options();
		if ( ! empty( $options['single_post_page'] ) ) {
			$url = get_permalink( $options['single_post_page'] );
		}
		$url = rtrim( $url, '/' ) . '/' . $post_id . '/';
	}

	return $url;
}

/**
 * Get the content types used by User Wall.
 *
 * @return array The content types used by User Wall.
 */
function userwall_wp_get_content_types() {
	$userwall_wp_core = new UserWall_WP_Post_Core();
	return $userwall_wp_core->get_post_content_types();
}

/**
 * Get the post types used by User Wall.
 *
 * @return array The post types used by User Wall.
 */
function userwall_wp_get_post_types() {
	$userwall_wp_core = new UserWall_WP_Post_Core();
	return $userwall_wp_core->get_post_types();
}

/**
 * Get the options for User Wall.
 *
 * @param string $key The option key.
 * @param mixed  $default_var The default value if the option is not found.
 * @return mixed The option value.
 */
function userwall_wp_get_options( $key = '', $default_var = '' ) {
	$options = get_option( 'userwall_wp' );
	if ( empty( $options ) ) {
		$defaults = array();
		$options  = apply_filters( 'userwall_wp_get_options_defaults', $defaults );
	}

	$defaults = array();

	$options = wp_parse_args( $options, $defaults );

	$options = apply_filters( 'userwall_wp_get_options', $options );

	if ( $key && empty( $options[ $key ] ) ) {
		return $default_var;
	}

	if ( $key && ! empty( $options[ $key ] ) ) {
		return apply_filters( 'userwall_wp_get_options_' . $key, $options[ $key ] );
	}
	return $options;
}

/**
 * Get the interaction template based on the type.
 *
 * @param string $type The type of interaction.
 */
function userwall_wp_get_interaction_tmpl( $type = '' ) {
	?>
	<div class="userwall-wp-activity-section">
		<div class="userwall-wp-reaction-count userwall-wp-activity-block" aria-label="<?php esc_html_e( 'Reactions count', 'userwall-wp' ); ?>">
			<span class="userwall-wp-comment-img">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
				</svg>
			</span>
			<span class="span-count">{{ <?php echo 'thread' === $type ? 'thread.reactions_count' : 'comment.reactions_count'; ?> }}</span>
		</div>

		<!-- Comment Count -->
		<div class="userwall-wp-comment-count userwall-wp-activity-block" aria-label="<?php esc_html_e( 'Comment Count', 'userwall-wp' ); ?>">
			<span class="userwall-wp-comment-img">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
			</svg>
			</span>
			<span class="userwall-wp-comment-span span-count">{{ <?php echo 'thread' === $type ? 'thread.comments_count' : 'comment.replies_count'; ?> }}</span>
		</div>

		<div class="userwall-wp-share userwall-wp-activity-block" aria-label="<?php esc_html_e( 'Share', 'userwall-wp' ); ?>">
			<span class="userwall-wp-comment-img">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
				</svg>
			</span>
		</div>
	</div>
		<?php
}

/**
 * Get the group meta.
 *
 * @param int    $group_id The group ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return a single value.
 * @return mixed The group meta.
 */
function userwall_wp_get_group_meta( $group_id, $key = '', $single = false ) {
	$group_meta = get_post_meta( $group_id, 'group_meta', true );
	if ( $key ) {
		if ( $single ) {
			return ! empty( $group_meta[ $key ] ) ? $group_meta[ $key ] : '';
		}
		return ! empty( $group_meta[ $key ] ) ? $group_meta[ $key ] : array();
	}
	return $group_meta;
}

/**
 * Get the group members.
 *
 * @return array
 */
function userwall_wp_kses_extended_ruleset() {
	$kses_defaults = wp_kses_allowed_html( 'post' );

	$svg_args = array(
		'svg'   => array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'focusable'       => true,
			'role'            => true,
			'fill'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'viewbox'         => true, // <= Must be lower case!
			'viewBox'         => true, // <= Must be camel case!
		),
		'g'     => array( 'fill' => true ),
		'title' => array( 'title' => true ),
		'path'  => array(
			'd'               => true,
			'fill'            => true,
			'clip-rule'       => true,
			'fill-rule'       => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'stroke-width'    => true,
			'stroke'          => true,
		),
	);
	return array_merge( $kses_defaults, $svg_args );
}


/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored. Addapted from WooCommerce wc_clean.
 *
 * @param string|array $raw_value Data to sanitize.
 * @return string|array
 */
function userwall_wp_clean( $raw_value ) {
	if ( is_array( $raw_value ) ) {
		return array_map( 'userwall_wp_clean', $raw_value );
	} else {
		return is_scalar( $raw_value ) ? sanitize_text_field( $raw_value ) : $raw_value;
	}
}
