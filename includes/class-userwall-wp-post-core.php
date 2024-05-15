<?php
/**
 * UserWall_WP_Post_Core class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Post_Core class
 */
class UserWall_WP_Post_Core {

	/**
	 * Constructor
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_filter( 'userwall_wp_profile_tabs', array( $this, 'userwall_wp_profile_tabs' ), 10, 1 );
		add_action( 'template_redirect', array( $this, 'template_loader' ) );
		add_action( 'userwall_profile_content_main', array( $this, 'userwall_profile_content' ), 10, 2 );
	}

	/**
	 * Add profile tabs
	 *
	 * @param array $tabs Profile tabs.
	 * @return array
	 */
	public function userwall_wp_profile_tabs( $tabs = array() ) {
		return $tabs;
	}

	/**
	 * Get post types
	 *
	 * @return array
	 */
	public function get_post_types() {
		$post_types         = apply_filters( 'userwall_wp_post_types', array() );
		$default_post_types = array(
			'user' => array(
				'title' => __( 'User', 'userwall-wp' ),
			),
		);
		return array_merge( $default_post_types, $post_types );
	}

	/**
	 * Get post content types
	 *
	 * @return array
	 */
	public function get_post_content_types() {
		$content_post_types = apply_filters( 'userwall_wp_get_post_content_types', array() );
		return $content_post_types;
	}

	/**
	 * Get post content types
	 *
	 * @param int $profile_id Profile ID.
	 */
	public function userwall_profile_content( $profile_id = 0 ) {
		echo do_shortcode( '[userwall_wp_post_form type="user-posts" per_page="5" object_id="' . $profile_id . '"]' );
	}

	/**
	 * Add rewrite rules
	 */
	public function add_rewrite_rules() {
		$options = userwall_wp_get_options();
		if ( ! empty( $options['user_page'] ) ) {
			$user_page_slug = get_post_field( 'post_name', $options['user_page'] );
			if ( $user_page_slug ) {
				add_rewrite_rule( '^' . $user_page_slug . '/([^/]*)/([^/]*)/?$', 'index.php?pagename=' . $user_page_slug . '&username=$matches[1]&user_profile=1&profile_tab=[2]', 'top' );

				add_rewrite_rule( '^' . $user_page_slug . '/([^/]*)/?$', 'index.php?pagename=' . $user_page_slug . '&username=$matches[1]&user_profile=1', 'top' );
			}
		}

		if ( ! empty( $options['single_post_page'] ) ) {
			$single_post_page_slug = get_post_field( 'post_name', $options['single_post_page'] );
			if ( $single_post_page_slug ) {
				add_rewrite_rule( '^' . $single_post_page_slug . '/([^/]*)/?$', 'index.php?pagename=' . $single_post_page_slug . '&thread_id=$matches[1]', 'top' );
			}
		}
	}

	/**
	 * Register query vars
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'username';
		$vars[] = 'thread_id';
		$vars[] = 'user_profile';
		$vars[] = 'profile_tab';
		$vars[] = 'profile_id';
		$vars[] = 'profile';
		return $vars;
	}

	/**
	 * Template loader
	 */
	public function template_loader() {
		global $uwwp_profile_info;
		$username     = get_query_var( 'username' );
		$thread_id    = get_query_var( 'thread_id' );
		$username     = get_query_var( 'username' );
		$all_userwall = get_query_var( 'all_userwall' );
		$user_profile = get_query_var( 'user_profile' );
		$profile_tab  = get_query_var( 'profile_tab' );

		if ( $username ) {
			$user = get_user_by( 'login', $username );
			if ( ! $user ) {
				// User does not exist, redirect to 404 page.
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit;
			} else {
				$uwwp_profile_info = new UserWall_WP_Profile( $user->ID );
				set_query_var( 'profile', $uwwp_profile_info );
				set_query_var( 'profile_id', $user->ID );
			}
			return;
		}
	}
}

$userwall_wp_core = new UserWall_WP_Post_Core();
$userwall_wp_core->hooks();
