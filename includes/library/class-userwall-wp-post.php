<?php
class UserWall_WP_Post {
    /**
     * @var int
     */
    private $post_id;

    /**
     * @var string
     */
    private $post_title;

    /**
     * @var string
     */
    private $post_content;

    /**
     * @var string
     */
    private $post_type;

    /**
     * @var string
     */
    private $post_status;

    /**
     * @var string
     */
    private $creation_date;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $comments_count;

    /**
     * @var int
     */
    private $reactions_count;

    /**
     * WordPress_Post constructor.
     *
     * @param stdClass $object
     */
    public function __construct($object) {
        $this->post_id = $object->post_id;
        $this->post_title = $object->post_title;
        $this->post_content = $object->post_content;
        $this->post_type = $object->post_type;
        $this->post_status = $object->post_status;
        $this->creation_date = $object->creation_date;
        $this->user_id = $object->user_id;
        $this->comments_count = $object->comments_count;
        $this->reactions_count = $object->reactions_count;
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
