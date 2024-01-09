<?php
class Threads_WP_Post_Manager {
    private $table_posts;
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_posts = $wpdb->prefix . 'threads_posts';
    }

    /**
     * Create a new post.
     *
     * @param array $data Post data including title, content, user ID, and other fields.
     * @return int|false The new post ID on success, false on failure.
     */
    public function create_post($data) {
        $defaults = array(
            'title' => '',
            'content' => '',
            'user_id' => 0,
            'post_type' => 'post',
            'creation_date' => current_time( 'mysql' ),
        );

        $data = wp_parse_args($data, $defaults);

        // Sanitize and validate data as needed
        
        // Insert data into the posts table
        $insert_data = array(
            'post_title'    => $data['title'],
            'post_content'  => $data['content'],
            'user_id'       => $data['user_id'],
            'post_type'     => $data['post_type'],
            'creation_date' => $data['creation_date'],
        );

        $result = $this->wpdb->insert($this->table_posts, $insert_data);

        if ($result) {
            $post_id = $this->wpdb->insert_id;
            do_action( 'thread_wp_create_post', $post_id, $insert_data);
            return apply_filters('thread_wp_create_post', $post_id, $insert_data);
        } else {
            return false;
        }
    }

    /**
     * Update an existing post.
     *
     * @param int $post_id The ID of the post to update.
     * @param array $data Post data to update.
     * @return bool Whether the update was successful or not.
     */
    public function update_post($post_id, $data) {
        // Sanitize and validate data as needed

        $update_data = array(
            'post_title' => $data['title'],
            'post_content' => $data['content'],
            // Update more fields as needed
        );

        $result = $this->wpdb->update($this->table_posts, $update_data, array('ID' => $post_id));
        do_action( 'thread_wp_update_post', $post_id, $post_data);
        return $result !== false;
    }

    /**
     * Delete a post by ID.
     *
     * @param int $post_id The ID of the post to delete.
     * @return bool Whether the deletion was successful or not.
     */
    public function delete_post($post_id) {
        do_action( 'thread_wp_before_delete_post', $post_id );
        $result = $this->wpdb->delete($this->table_posts, array('ID' => $post_id));
        do_action( 'thread_wp_after_delete_post', $post_id );
        return $result !== false;
    }

    /**
     * Get a post by its ID.
     *
     * @param int $post_id The ID of the post to retrieve.
     * @return object|false Post object on success, false on failure.
     */
    public function get_post_by_id($post_id) {
        $tables = Threads_WP_Table_Manager::get_table_names();
        $post = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT p.*, 
                (SELECT COUNT(*) FROM {$tables['comments']} WHERE post_id = p.post_id) AS comments_count,
                (SELECT COUNT(*) FROM {$tables['likes']} WHERE post_id = p.post_id) AS reactions_count
                FROM {$this->table_posts} p
                WHERE p.post_id = %d",
                $post_id
            )
        );

        return apply_filters('thread_wp_get_post_by_id', $post, $post_id);
    }

    /**
     * Get posts by user ID.
     *
     * @param int $user_id The ID of the user.
     * @param int $limit Number of posts to retrieve (optional).
     * @return array List of post objects by the user.
     */
    public function get_posts_by_user_id($user_id, $limit = -1) {
        $posts = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table_posts} WHERE user_id = %d ORDER BY ID DESC LIMIT %d",
                $user_id,
                $limit
            )
        );

        return apply_filters('thread_wp_get_posts_by_user_id', $posts, $user_id, $limit);
    }

    /**
     * Get posts by group ID.
     *
     * @param int $group_id The ID of the group.
     * @param int $limit Number of posts to retrieve (optional).
     * @return array List of post objects by the group.
     */
    public function get_posts_by_group($group_id, $limit = -1) {
        // Implement the query to fetch posts by group ID here

        return apply_filters('thread_wp_get_posts_by_group', $posts, $group_id, $limit);
    }

    /**
     * Get the latest posts since a given post ID.
     *
     * @param int $last_post_id The ID of the last post.
     * @param int $limit Number of posts to retrieve (optional).
     * @return array List of post objects.
     */
    public function get_posts_latest($last_post_id, $limit = 30) {
        // Implement the query to fetch the latest posts since $last_post_id here

        return apply_filters('thread_wp_get_posts_latest', $posts, $last_post_id, $limit);
    }
}
