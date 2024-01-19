<?php
class UserWall_WP_Posts_Table extends WP_List_Table {
    private $table_posts;
    private $table_comments;
    private $table_likes;
    private $table_bookmarks;
    private $table_media;
    private $table_albums;
    private $table_reports;
    private $table_user_reputation;
    private $table_badges;
    private $table_hashtags;
    private $table_user_settings;
    private $table_notifications;
    private $table_search_history;
    private $table_user_followers;
    private $table_user_following;
    private $table_user_notifications;

    public function __construct() {
        global $wpdb;
        $this->table_posts = $wpdb->prefix . 'userwall_posts';
        $this->table_comments = $wpdb->prefix . 'userwall_comments';
        $this->table_likes = $wpdb->prefix . 'userwall_likes';
        $this->table_bookmarks = $wpdb->prefix . 'userwall_bookmarks';
        $this->table_media = $wpdb->prefix . 'userwall_media';
        $this->table_albums = $wpdb->prefix . 'userwall_albums';
        $this->table_reports = $wpdb->prefix . 'userwall_reports';
        $this->table_user_reputation = $wpdb->prefix . 'userwall_user_reputation';
        $this->table_badges = $wpdb->prefix . 'userwall_badges';
        $this->table_hashtags = $wpdb->prefix . 'userwall_hashtags';
        $this->table_user_settings = $wpdb->prefix . 'userwall_user_settings';
        $this->table_notifications = $wpdb->prefix . 'userwall_notifications';
        $this->table_search_history = $wpdb->prefix . 'userwall_search_history';
        $this->table_user_followers = $wpdb->prefix . 'userwall_user_followers';
        $this->table_user_following = $wpdb->prefix . 'userwall_user_following';
        $this->table_user_notifications = $wpdb->prefix . 'userwall_user_notifications';
        
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ]);
    }

    public function prepare_items() {
        global $wpdb;
        
        $per_page = $this->get_items_per_page('posts_per_page', 10);
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;
        
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        // Ensure that $sortable is defined as an associative array
        $sortable = [
            'title' => ['title', false], // Example sortable column
            'content' => ['content', false], // Example sortable column
            // Add more sortable columns as needed
        ];
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Updated SQL query for the new table
        $query = "SELECT * FROM {$this->table_posts}";
        
        if (!empty($_REQUEST['orderby']) && isset($sortable[$_REQUEST['orderby']])) {
            $order = $_REQUEST['order'] === 'asc' ? 'ASC' : 'DESC';
            $query .= " ORDER BY {$sortable[$_REQUEST['orderby']][0]} $order";
        }
        
        $query .= " LIMIT $per_page OFFSET $offset";
        
        $this->items = $wpdb->get_results($query, ARRAY_A);
    }
    
    
    public function get_hidden_columns() {
        return array();
    }

    public function get_posts_data() {
        global $wpdb;
    
        // Define your SQL query to fetch post data
        $query = "SELECT p.*, u.display_name AS user_name,
                  (SELECT COUNT(*) FROM {$wpdb->prefix}comments WHERE comment_post_ID = p.post_id) AS comments_count,
                  (SELECT COUNT(*) FROM your_reaction_table WHERE post_id = p.post_id) AS reactions_count
                  FROM {$wpdb->prefix}posts p
                  LEFT JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
                  WHERE p.post_type = 'post'"; // Adjust your post type as needed
    
        // Handle sorting if necessary
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'creation_date';
        $order = !empty($_GET['order']) ? $_GET['order'] : 'DESC';
        $query .= " ORDER BY $orderby $order";
    
        // Fetch the data
        $data = $wpdb->get_results($query, ARRAY_A);
    
        return $data;
    }    
    

    public function get_columns() {
        // Define your columns here
        return [
            'title' => 'Title',
            'content' => 'Content',
            'type' => 'Type',
            'user' => 'User',
            'creation_date' => 'Created Date',
            'comments_count' => 'Comments Count',
            'reactions_count' => 'Reactions Count',
            'actions' => 'Actions',
        ];
    }

    public function get_sortable_columns() {
        $sortable_columns = [
            'user_name' => ['user_name', false],
            'creation_date' => ['creation_date', false],
        ];
    
        return $sortable_columns;
    }
    
    public function column_user_name($item) {
        $column = 'user_name';
        $title = $item[$column];
    
        $actions = [
            'edit' => sprintf(
                '<a href="?page=%s&action=%s&post_id=%s">Edit</a>',
                esc_attr($_REQUEST['page']),
                'edit',
                absint($item['ID'])
            ),
            'comments' => sprintf(
                '<a href="%s">View Comments</a>',
                esc_url(get_comments_link($item['ID']))
            ),
        ];
    
        return $title . $this->row_actions($actions);
    }
    
    public function column_creation_date($item) {
        return $item['creation_date'];
    }

    public function display_notices() {
        if (isset($_GET['message']) && $_GET['message'] === 'updated') {
            echo '<div class="updated"><p>Post status updated successfully.</p></div>';
        }
    }

    public function column_default($item, $column_name) {
        // Define how to display each column's data
        switch ($column_name) {
            case 'title':
                return $item['post_title'];
            case 'content':
                return $item['post_content'];
            case 'type':
                return $item['post_type'];
            case 'user':
                return $item['user_name'];
            case 'creation_date':
                return $item['creation_date'];
            case 'comments_count':
                return $item['comments_count'];
            case 'reactions_count':
                return $item['reactions_count'];
            case 'actions':
                return $this->get_actions_html($item['post_id']);
            default:
                return ''; // Return empty string for other columns
        }
    }

    public function get_actions_html($post_id) {
        // Define actions (edit, delete, etc.) and generate HTML for the Actions column
        // You can create action buttons or links here
    }
    
    // Implement pagination, if needed

    // Implement any additional methods for fetching and processing data

    public function display_table() {
        $this->prepare_items();
        $this->display();
    }

    public function get_bulk_actions() {
        $actions = [
            'bulk-draft' => __('Change to Draft', 'userwall-wp'),
            'bulk-publish' => __('Publish', 'userwall-wp'),
        ];
    
        return $actions;
    }
    
    public function process_bulk_action() {
        global $wpdb;
    
        if ('bulk-draft' === $this->current_action() || 'bulk-publish' === $this->current_action()) {
            $post_ids = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : [];
    
            if (empty($post_ids)) {
                return;
            }
    
            $new_status = ('bulk-draft' === $this->current_action()) ? 'draft' : 'publish';
    
            foreach ($post_ids as $post_id) {
                // Update the post status for each selected post
                $wpdb->update(
                    $wpdb->prefix . 'posts',
                    ['post_status' => $new_status],
                    ['ID' => $post_id],
                    ['%s'],
                    ['%d']
                );
            }
        }
    }
    
}
