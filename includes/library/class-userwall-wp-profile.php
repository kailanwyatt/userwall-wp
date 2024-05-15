<?php
/**
 * Class UserWall_WP_Profile
 * Class for managing a WordPress user profile.
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserWall_WP_Profile
 * Class for managing a WordPress user profile.
 */
class UserWall_WP_Profile {

	/**
	 * The user ID.
	 *
	 * @var int
	 */
	protected $user_id;

	/**
	 * Constructor.
	 *
	 * @param int $user_id User ID.
	 */
	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Get the username.
	 *
	 * @return string The username.
	 */
	public function get_username() {
		return get_the_author_meta( 'user_login', $this->user_id );
	}

	/**
	 * Get the display name.
	 *
	 * @return string The display name.
	 */
	public function get_display_name() {
		return get_the_author_meta( 'display_name', $this->user_id );
	}

	/**
	 * Get the user posts.
	 *
	 * @return WP_Post[] Array of user's posts.
	 */
	public function get_user_posts() {
		$args = array(
			'author' => $this->user_id,
		);

		return get_posts( $args );
	}

	/**
	 * Get the user post count.
	 *
	 * @return int Number of posts the user has authored.
	 */
	public function get_user_post_count() {
		return count_user_posts( $this->user_id );
	}

	/**
	 * Get the user avatar.
	 *
	 * @return string URL of the user's avatar image.
	 */
	public function get_user_avatar() {
		return get_avatar_url( $this->user_id );
	}

	/**
	 * Get the user bio.
	 *
	 * @return string The user's biographical information.
	 */
	public function get_user_bio() {
		return get_the_author_meta( 'description', $this->user_id );
	}

	/**
	 * Set the display name.
	 *
	 * @param string $display_name New display name.
	 */
	public function set_display_name( $display_name ) {
		wp_update_user(
			array(
				'ID'           => $this->user_id,
				'display_name' => $display_name,
			)
		);
	}

	/**
	 * Set the user bio.
	 *
	 * @param string $bio New biographical information.
	 */
	public function set_user_bio( $bio ) {
		update_user_meta( $this->user_id, 'description', $bio );
	}

	/**
	 * Get all profile info.
	 *
	 * @return array Array of all user profile information.
	 */
	public function get_all_profile_info() {
		return array(
			'username'     => $this->get_username(),
			'display_name' => $this->get_display_name(),
			'user_posts'   => $this->get_user_posts(),
			'post_count'   => $this->get_user_post_count(),
			'avatar'       => $this->get_user_avatar(),
			'bio'          => $this->get_user_bio(),
		);
	}
}
