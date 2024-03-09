<?php
/**
 * UserWall_WP_Posts_Table class
 *
 * @package Userwall_WP
 */

/**
 * Class UserWall_WP_Posts_Table
 */
class UserWall_WP_Posts_Table extends WP_List_Table {
	/**
	 * The table posts.
	 *
	 * @var string
	 */
	private $table_posts;
	/**
	 * The table comments.
	 *
	 * @var string
	 */
	private $table_comments;
	/**
	 * The table likes.
	 *
	 * @var string
	 */
	private $table_likes;
	/**
	 * The table bookmarks.
	 *
	 * @var string
	 */
	private $table_bookmarks;
	/**
	 * The table media.
	 *
	 * @var string
	 */
	private $table_media;
	/**
	 * The table albums.
	 *
	 * @var string
	 */
	private $table_albums;
	/**
	 * The table reports.
	 *
	 * @var string
	 */
	private $table_reports;
	/**
	 * The table user reputation.
	 *
	 * @var string
	 */
	private $table_user_reputation;
	/**
	 * The table badges.
	 *
	 * @var string
	 */
	private $table_badges;
	/**
	 * The table hashtags.
	 *
	 * @var string
	 */
	private $table_hashtags;
	/**
	 * The table user settings.
	 *
	 * @var string
	 */
	private $table_user_settings;
	/**
	 * The table notifications.
	 *
	 * @var string
	 */
	private $table_notifications;
	/**
	 * The table search history.
	 *
	 * @var string
	 */
	private $table_search_history;
	/**
	 * The table user followers.
	 *
	 * @var string
	 */
	private $table_user_followers;
	/**
	 * The table user following.
	 *
	 * @var string
	 */
	private $table_user_following;
	/**
	 * The table user notifications.
	 *
	 * @var string
	 */
	private $table_user_notifications;

	/**
	 * UserWall_WP_Posts_Table constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_posts              = $wpdb->prefix . 'userwall_posts';
		$this->table_comments           = $wpdb->prefix . 'userwall_comments';
		$this->table_likes              = $wpdb->prefix . 'userwall_likes';
		$this->table_bookmarks          = $wpdb->prefix . 'userwall_bookmarks';
		$this->table_media              = $wpdb->prefix . 'userwall_media';
		$this->table_albums             = $wpdb->prefix . 'userwall_albums';
		$this->table_reports            = $wpdb->prefix . 'userwall_reports';
		$this->table_user_reputation    = $wpdb->prefix . 'userwall_user_reputation';
		$this->table_badges             = $wpdb->prefix . 'userwall_badges';
		$this->table_hashtags           = $wpdb->prefix . 'userwall_hashtags';
		$this->table_user_settings      = $wpdb->prefix . 'userwall_user_settings';
		$this->table_notifications      = $wpdb->prefix . 'userwall_notifications';
		$this->table_search_history     = $wpdb->prefix . 'userwall_search_history';
		$this->table_user_followers     = $wpdb->prefix . 'userwall_user_followers';
		$this->table_user_following     = $wpdb->prefix . 'userwall_user_following';
		$this->table_user_notifications = $wpdb->prefix . 'userwall_user_notifications';

		parent::__construct(
			array(
				'singular' => 'post',
				'plural'   => 'posts',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare data.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page     = $this->get_items_per_page( 'posts_per_page', 10 );
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		// Ensure that $sortable is defined as an associative array.
		$sortable = array(
			'title'   => array( 'title', false ),
			'content' => array( 'content', false ),
		);

		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Updated SQL query for the new table.
		$query = "SELECT * FROM {$this->table_posts}";

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['orderby'] ) && isset( $sortable[ $_REQUEST['orderby'] ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
			$order  = isset( $_REQUEST['order'] ) && 'asc' === $_REQUEST['order'] ? 'ASC' : 'DESC';
			$query .= " ORDER BY {$sortable[ sanitize_text_field( $_REQUEST['orderby'] ) ][0]} $order";
		}

		$query .= " LIMIT $per_page OFFSET $offset";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$this->items = $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Get hidden columns.
	 *
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Get sortable columns.
	 *
	 * @return array
	 */
	public function get_posts_data() {
		global $wpdb;

		// Define your SQL query to fetch post data.
		$query = "SELECT p.*, u.display_name AS user_name,
                  (SELECT COUNT(*) FROM {$wpdb->prefix}comments WHERE comment_post_ID = p.post_id) AS comments_count,
                  (SELECT COUNT(*) FROM your_reaction_table WHERE post_id = p.post_id) AS reactions_count
                  FROM {$wpdb->prefix}posts p
                  LEFT JOIN {$wpdb->prefix}users u ON p.post_author = u.ID
                  WHERE p.post_type = 'post'";

		// Handle sorting if necessary.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'creation_date';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		$order  = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC';
		$query .= " ORDER BY $orderby $order";

		// Fetch the data.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$data = $wpdb->get_results( $query, ARRAY_A );

		return $data;
	}


	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		// Define your columns here.
		return array(
			'title'           => 'Title',
			'content'         => 'Content',
			'type'            => 'Type',
			'user'            => 'User',
			'creation_date'   => 'Created Date',
			'comments_count'  => 'Comments Count',
			'reactions_count' => 'Reactions Count',
			'actions'         => 'Actions',
		);
	}

	/**
	 * Get sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'user_name'     => array( 'user_name', false ),
			'creation_date' => array( 'creation_date', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param array $item        Data of the current row.
	 *
	 * @return mixed
	 */
	public function column_user_name( $item ) {
		$column  = 'user_name';
		$title   = $item[ $column ];
		$actions = array();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['page'] ) ) {
			$actions['edit'] = sprintf(
				'<a href="?page=%s&action=%s&post_id=%s">Edit</a>',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ),
				'edit',
				absint( $item['ID'] )
			);
			$actions['comments'] = sprintf(
				'<a href="%s">View Comments</a>',
				esc_url( get_comments_link( $item['ID'] ) )
			);
		}

		return $title . $this->row_actions( $actions );
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param array $item Data of the current row.
	 *
	 * @return mixed
	 */
	public function column_creation_date( $item ) {
		return $item['creation_date'];
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @return mixed
	 */
	public function display_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['message'] ) && 'updated' === $_GET['message'] ) {
			echo '<div class="updated"><p>Post status updated successfully.</p></div>';
		}
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param array  $item        Data of the current row.
	 * @param string $column_name The name of the column.
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		// Define how to display each column's data.
		switch ( $column_name ) {
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
				return $this->get_actions_html( $item['post_id'] );
			default:
				return ''; // Return empty string for other columns.
		}
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param array $post_id Data of the current row.
	 *
	 * @return mixed
	 */
	public function get_actions_html( $post_id = 0 ) {
		// Define actions (edit, delete, etc.) and generate HTML for the Actions column.
		// You can create action buttons or links here.
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @return mixed
	 */
	public function display_table() {
		$this->prepare_items();
		$this->display();
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @return mixed
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-draft'   => __( 'Change to Draft', 'userwall-wp' ),
			'bulk-publish' => __( 'Publish', 'userwall-wp' ),
		);

		return $actions;
	}

	/**
	 * Define what data to show on each column of the table.
	 *
	 * @return mixed
	 */
	public function process_bulk_action() {
		global $wpdb;

		if ( 'bulk-draft' === $this->current_action() || 'bulk-publish' === $this->current_action() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_ids = isset( $_REQUEST['post_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_id'] ) ) : array();

			if ( empty( $post_ids ) ) {
				return;
			}

			$new_status = ( 'bulk-draft' === $this->current_action() ) ? 'draft' : 'publish';

			foreach ( $post_ids as $post_id ) {
				// Update the post status for each selected post.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->update(
					$wpdb->prefix . 'posts',
					array( 'post_status' => $new_status ),
					array( 'ID' => $post_id ),
					array( '%s' ),
					array( '%d' )
				);
			}
		}
	}
}
