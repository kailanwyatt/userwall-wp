<?php
/**
 * Class UserWall_WP_Group
 * Class for managing a WordPress group.
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserWall_WP_Group
 * Class for managing a WordPress group.
 */
class UserWall_WP_Group {

	/**
	 * The group ID.
	 *
	 * @var int
	 */
	protected $group_id;

	/**
	 * Constructor.
	 *
	 * @param int $group_id Group ID.
	 */
	public function __construct( $group_id ) {
		$this->group_id = $group_id;
	}

	/**
	 * Get the group name.
	 *
	 * @return string The group name.
	 */
	public function get_group_name() {
		return userwall_wp_get_group_meta( $this->group_id, 'name', true );
	}

	/**
	 * Get the group slug.
	 *
	 * @return string The group slug.
	 */
	public function get_group_slug() {
		return userwall_wp_get_group_meta( $this->group_id, 'slug', true );
	}

	/**
	 * Get the group description.
	 *
	 * @return string The group description.
	 */
	public function get_group_description() {
		return userwall_wp_get_group_meta( $this->group_id, 'description', true );
	}

	/**
	 * Get the group avatar.
	 *
	 * @return string The group avatar URL or path.
	 */
	public function get_group_avatar() {
		return userwall_wp_get_group_meta( $this->group_id, 'avatar', true );
	}

	/**
	 * Get all group information.
	 *
	 * @return array An associative array of all group information.
	 */
	public function get_all_group_info() {
		return apply_filters(
			'userwall_wp_group_info',
			array(
				'name'        => $this->get_group_name(),
				'slug'        => $this->get_group_slug(),
				'description' => $this->get_group_description(),
				'avatar'      => $this->get_group_avatar(),
			),
			$this->group_id
		);
	}
}
