<?php
function uswp_get_template( $template_name = '', $args = array() ) {
	extract( $args );
	include UserWall_Template::get_template( $template_name, $args );
}

/**
 * Helper function to get data from UserWall_WP_Profile.
 *
 * @param string $profile_key Key for the profile data to retrieve.
 * @param int|null $user_id User ID. If not set, use current user ID.
 * @return mixed The requested user profile data.
 */
function get_userwall_wp_profile_data( $profile_key = '', $user_id = null ) {
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
 * @param string $username The username for the profile.
 * @param string|null $profile_tab Optional profile tab.
 * @return string The user profile URL.
 */
function user_wall_get_user_profile_url( $username, $profile_tab = null ) {
	// Check if permalinks are enabled ('plain' indicates they are not).
	if ( get_option( 'permalink_structure' ) == '' ) {
		// Build a plain URL.
		$url     = home_url();
		$options = user_wall_get_options();
		if ( ! empty( $options['user_page'] ) ) {
			$url = get_permalink( $options['user_page'] );
		}
		$url = add_query_arg(
			array(
				'username'     => urlencode( $username ),
				'user_profile' => 1,
			),
			$url
		);
		$url = home_url( '/' ) . '?pagename=u&username=' . urlencode( $username ) . '&user_profile=1';
		if ( $profile_tab ) {
			$url = add_query_arg( array( 'profile_tab' => urlencode( $profile_tab ) ), $url );
		}
	} else {
		// Build a pretty URL.
		$options = user_wall_get_options();
		if ( ! empty( $options['user_page'] ) ) {
			$url = get_permalink( $options['user_page'] );
		}
		$url = rtrim( $url, '/' ) . '/' . $username . '/';
		if ( $profile_tab ) {
			$url = rtrim( $url, '/' ) . '/' . $profile_tab . '/';
		}
	}

	return $url;
}

function user_wall_get_permalink( $post_id = 0 ) {
	// Check if permalinks are enabled ('plain' indicates they are not).
	if ( '' === get_option( 'permalink_structure' ) ) {
		// Build a plain URL.
		$url     = home_url();
		$options = user_wall_get_options();
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
		$options = user_wall_get_options();
		if ( ! empty( $options['single_post_page'] ) ) {
			$url = get_permalink( $options['single_post_page'] );
		}
		$url = rtrim( $url, '/' ) . '/' . $post_id . '/';
	}

	return $url;
}
function user_wall_get_post_types() {
	$user_wall_core = new UserWall_WP_Post_Core();
	return $user_wall_core->get_post_types();
}

function user_wall_get_content_types() {
	$user_wall_core = new UserWall_WP_Post_Core();
	return $user_wall_core->get_post_content_types();
}

function user_wall_get_options( $key = '', $default_var = '' ) {
	$options = get_option( 'userwall_wp' );
	if ( empty( $options ) ) {
		$defaults = array();
		$options  = apply_filters( 'user_wall_get_options_defaults', $defaults );
	}

	$defaults = array();

	$options = wp_parse_args( $options, $defaults );

	$options = apply_filters( 'user_wall_get_options', $options );

	if ( $key && empty( $options[ $key ] ) ) {
		return $default_var;
	}

	if ( $key && ! empty( $options[ $key ] ) ) {
		return apply_filters( 'user_wall_get_options_' . $key, $options[ $key ] );
	}
	return $options;
}
