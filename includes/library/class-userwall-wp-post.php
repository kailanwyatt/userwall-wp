<?php
/**
 * UserWall_WP_Post class
 *
 * Class for managing a WordPress post.
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Post class
 */
class UserWall_WP_Post {
	/**
	 * The post ID.
	 *
	 * @var int
	 */
	private $post_id;

	/**
	 * The post title.
	 *
	 * @var string
	 */
	private $post_title;

	/**
	 * The post content.
	 *
	 * @var string
	 */
	private $post_content;

	/**
	 * The post type.
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The post status.
	 *
	 * @var string
	 */
	private $post_status;

	/**
	 * The creation date of the post.
	 *
	 * @var string
	 */
	private $creation_date;

	/**
	 * The user ID associated with the post.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * The number of comments on the post.
	 *
	 * @var int
	 */
	private $comments_count;

	/**
	 * The number of reactions on the post.
	 *
	 * @var int
	 */
	private $reactions_count;

	/**
	 * WordPress_Post constructor.
	 *
	 * @param stdClass $post The post object.
	 */
	public function __construct( $post ) {
		$this->post_id         = $post->post_id;
		$this->post_title      = $post->post_title;
		$this->post_content    = $post->post_content;
		$this->post_type       = $post->post_type;
		$this->post_status     = $post->post_status;
		$this->creation_date   = $post->creation_date;
		$this->user_id         = $post->user_id;
		$this->comments_count  = $post->comments_count;
		$this->reactions_count = $post->reactions_count;
	}

	/**
	 * Get the post ID.
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get the post title.
	 *
	 * @return string
	 */
	public function get_post_title() {
		return $this->post_title;
	}

	/**
	 * Get the post content.
	 *
	 * @return string
	 */
	public function get_post_content() {
		return $this->post_content;
	}

	/**
	 * Get the post type.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Get the post status.
	 *
	 * @return string
	 */
	public function get_post_status() {
		return $this->post_status;
	}

	/**
	 * Get the creation date of the post.
	 *
	 * @return string
	 */
	public function get_creation_date() {
		return $this->creation_date;
	}

	/**
	 * Get the user ID associated with the post.
	 *
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Get the number of comments on the post.
	 *
	 * @return int
	 */
	public function get_comments_count() {
		return $this->comments_count;
	}

	/**
	 * Get the number of reactions on the post.
	 *
	 * @return int
	 */
	public function get_reactions_count() {
		return $this->reactions_count;
	}
}
