<?php
class Threads_WP_Post_Manager {
    private $wpdb;
    private $table_posts;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_posts = $wpdb->prefix . 'threads_posts';
    }

    public function save_post($post_content, $post_type, $user_id) {
        $data = array(
            'post_content' => $post_content,
            'post_type' => $post_type,
            'creation_date' => current_time('mysql'),
            'user_id' => $user_id,
        );

        $format = array('%s', '%s', '%s', '%d');

        $this->wpdb->insert($this->table_posts, $data, $format);

        if ($this->wpdb->insert_id) {
            return $this->wpdb->insert_id;
        }

        return false;
    }

    public function delete_post($post_id) {
        return $this->wpdb->delete($this->table_posts, array('post_id' => $post_id), array('%d'));
    }

    public function get_post_by_id($post_id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->table_posts} WHERE post_id = %d", $post_id),
            ARRAY_A
        );
    }

    public function get_posts_by_user_id($user_id) {
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM {$this->table_posts} WHERE user_id = %d", $user_id),
            ARRAY_A
        );
    }
}
