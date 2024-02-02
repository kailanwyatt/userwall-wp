<?php
/**
 * Class for Managing Posts and Comments.
 *
 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 */
class UserWall_WP_Post_Manager {
	private $table_posts;
	private $wpdb;
	private $authors;

	public function __construct() {
		global $wpdb;
		$this->wpdb        = $wpdb;
		$this->table_posts = $wpdb->prefix . 'userwall_posts';
		$this->authors     = array();
	}

	private function get_author_info( $user_id = 0 ) {
		// Ensure the user ID is an integer
		$user_id = intval( $user_id );

		if ( ! $user_id ) {
			return array(
				'author_name'       => '',
				'author_url'        => '',
				'author_avatar_url' => '',
			);
		}

		// If the author is not in the array. Add it now to reduce DB calls.
		if ( empty( $this->authors[ $user_id ] ) ) {
			// Get the user by user ID
			$user = get_userdata( $user_id );

			// Check if user exists
			if ( false === $user ) {
				// Return empty array.
				return array(
					'author_name'       => '',
					'author_url'        => '',
					'author_avatar_url' => '',
				);
			}

			// Get the author's display name
			$author_name = $user->display_name;

			// Get the author URL
			$author_url = user_wall_get_user_profile_url( $user->user_login );

			// Get the avatar URL
			$author_avatar_url = get_avatar_url( $user_id, apply_filters( 'userwall_wp_avatar_size', array( 'size' => 50 ), $user_id ) );

			$this->authors[ $user_id ] = array(
				'author_name'       => $author_name,
				'author_url'        => $author_url,
				'author_avatar_url' => $author_avatar_url,
			);
		}

		return apply_filters( 'userwall_wp_get_author_info', $this->authors[ $user_id ], $user_id );
	}
	/**
	 * Create a new post.
	 *
	 * @param array $data Post data including title, content, user ID, and other fields.
	 * @return int|false The new post ID on success, false on failure.
	 */
	public function create_post( $data ) {
		$defaults = array(
			'title'         => '',
			'content'       => '',
			'user_id'       => 0,
			'post_type'     => 'post',
			'creation_date' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		// Sanitize and validate data as needed

		// Insert data into the posts table
		$insert_data = array(
			'post_title'    => $data['title'],
			'post_content'  => $this->trim_p_tags( $data['content'] ),
			'user_id'       => $data['user_id'],
			'post_type'     => $data['post_type'],
			'creation_date' => $data['creation_date'],
		);

		$result = $this->wpdb->insert( $this->table_posts, $insert_data );

		if ( $result ) {
			$post_id = $this->wpdb->insert_id;
			do_action( 'userwall_wp_create_post', $post_id, $insert_data );
			return $post_id;
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
	public function update_post( $post_id, $data ) {
		$defaults = array(
			'title'         => '',
			'content'       => '',
			'user_id'       => 0,
			'post_type'     => 'post',
			'creation_date' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );
		// Sanitize and validate data as needed

		$update_data = array(
			'post_content' => $this->trim_p_tags( $data['content'] ),
		);

		$result = $this->wpdb->update( $this->table_posts, $update_data, array( 'post_id' => $post_id ) );
		do_action( 'userwall_wp_update_post', $post_id, $update_data );
		return false !== $result;
	}

	public function can_moderate( $post_id = 0, $current_user_id = 0 ) {
		// Allow admins to be able to moderate anything.
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$tables = UserWall_WP_Table_Manager::get_table_names();
		$query  = $this->wpdb->prepare(
			"SELECT p.user_id
            FROM {$this->table_posts} p
            WHERE p.post_id = %d",
			$post_id
		);

		$user_id = $this->wpdb->get_var( $query );

		if ( $user_id === $current_user_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Delete a post by ID.
	 *
	 * @param int $post_id The ID of the post to delete.
	 * @return bool Whether the deletion was successful or not.
	 */
	public function delete_post( $post_id ) {
		do_action( 'userwall_wp_before_delete_post', $post_id );
		$tables = UserWall_WP_Table_Manager::get_table_names();

		// Delete comments
		$result = $this->wpdb->delete( $tables['comments'], array( 'post_id' => $post_id ) );

		$result = $this->wpdb->delete( $tables['posts'], array( 'post_id' => $post_id ) );

		do_action( 'userwall_wp_after_delete_post', $post_id );
		return false !== $result;
	}

	/**
	 * Get a post by its ID.
	 *
	 * @param int $post_id The ID of the post to retrieve.
	 * @return object|false Post object on success, false on failure.
	 */
	public function get_post_by_id( $post_id ) {
		$tables = UserWall_WP_Table_Manager::get_table_names();
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$post = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT p.*, 
                (SELECT COUNT(*) FROM {$tables['comments']} WHERE post_id = p.post_id AND parent_id = 0) AS comments_count,
                (SELECT COUNT(*) FROM {$tables['likes']} WHERE post_id = p.post_id) AS reactions_count
                FROM {$this->table_posts} p
                WHERE p.post_id = %d",
				$post_id
			)
		);
		$post = $this->transform_post( $post );

		return apply_filters( 'userwall_wp_get_post_by_id', $post, $post_id );
	}

	/**
	 * Get posts by user ID.
	 *
	 * @param int $user_id The ID of the user.
	 * @param int $limit Number of posts to retrieve (optional).
	 * @return array List of post objects by the user.
	 */
	public function get_posts_by_user_id( $user_id, $limit = -1 ) {
		$posts = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table_posts} WHERE user_id = %d ORDER BY ID DESC LIMIT %d",
				$user_id,
				$limit
			)
		);
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$posts[] = $this->transform_post( $post );
			}
		}
		return apply_filters( 'userwall_wp_get_posts_by_user_id', $posts, $user_id, $limit );
	}

	/**
	 * Get posts by group ID.
	 *
	 * @param int $group_id The ID of the group.
	 * @param int $limit Number of posts to retrieve (optional).
	 * @return array List of post objects by the group.
	 */
	public function get_posts_by_group( $group_id, $limit = -1 ) {
		// Implement the query to fetch posts by group ID here
		$posts = array();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$posts[] = $this->transform_post( $post );
			}
		}
		return apply_filters( 'userwall_wp_get_posts_by_group', $posts, $group_id, $limit );
	}

	/**
	 * Get the latest posts since a given post ID.
	 *
	 * @param int $last_post_id The ID of the last post.
	 * @param int $limit Number of posts to retrieve (optional).
	 * @return array List of post objects.
	 */
	public function get_posts_latest( $last_post_id = 0, $limit = 30 ) {
		// Implement the query to fetch the latest posts since $last_post_id here
		$tables = UserWall_WP_Table_Manager::get_table_names();
		$query  = $this->wpdb->prepare(
			"SELECT p.*, 
            (SELECT COUNT(*) FROM {$tables['comments']} WHERE post_id = p.post_id AND parent_id = 0) AS comments_count,
            (SELECT COUNT(*) FROM {$tables['likes']} WHERE post_id = p.post_id) AS reactions_count
            FROM {$this->table_posts} p
            WHERE p.post_id > %d
            ORDER BY p.post_id DESC
            LIMIT %d",
			$last_post_id,
			$limit
		);

		$posts = $this->wpdb->get_results( $query );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$posts[] = $this->transform_post( $post );
			}
		}
		return apply_filters( 'userwall_wp_get_posts_latest', $posts, $last_post_id, $limit );
	}

	private function transform_post( $post = array() ) {
		$author_info                      = $this->get_author_info( $post->user_id );
		$modified_post                    = $post;
		$modified_post->author_url        = $author_info['author_url'];
		$modified_post->author_avatar_url = $author_info['author_avatar_url'];
		$modified_post->author_name       = $author_info['author_name'];
		// Get timestamp for post.
		if ( isset( $modified_post->creation_date ) ) {
			$modified_post->post_timestamp = strtotime( $post->creation_date );
		}

		// Get timestamp for comment
		if ( isset( $modified_post->comment_date ) ) {
			$modified_post->comment_timestamp = strtotime( $post->comment_date );
		}

		if ( isset( $modified_post->post_content ) ) {
			$modified_post->post_content = $this->trim_p_tags( $post->post_content );
		}

		if ( isset( $modified_post->comment_content ) ) {
			$modified_post->comment_content = $this->trim_p_tags( $post->comment_content );
		}

		return apply_filters( 'userwall_wp_post_return_object', $modified_post );
	}
	/**
	 * Get the latest posts since a given post ID.
	 *
	 * @param int $last_post_id The ID of the last post.
	 * @param int $limit Number of posts to retrieve (optional).
	 * @return array List of post objects.
	 */
	public function get_posts_latest_count( $last_post_id = 0, $limit = 30 ) {
		// Implement the query to fetch the latest posts since $last_post_id here
		$tables = UserWall_WP_Table_Manager::get_table_names();
		$query  = $this->wpdb->prepare(
			"SELECT COUNT(p.post_id)
            FROM {$this->table_posts} p
            WHERE p.post_id > %d
            ORDER BY p.post_id ASC
            LIMIT %d",
			$last_post_id,
			$limit
		);

		$posts = $this->wpdb->get_var( $query );
		return apply_filters( 'userwall_wp_get_posts_latest_count', $posts, $last_post_id, $limit );
	}


	public function get_posts( $args = array() ) {
		$defaults = array(
			'type'      => 'posts',
			'per_page'  => '30',
			'page'      => 1,
			'object_id' => 0,
			'object'    => 'user',
			'oldest_id' => 0,
			'latest_id' => 0,
			'order_by'  => 'p.post_id',
			'order'     => 'DESC',
		);

		$args = wp_parse_args( $args, $defaults );

		$page     = intval( $args['page'] ); // Sanitize the page value
		$per_page = intval( $args['per_page'] ); // Sanitize the per_page value
		$offset   = ( $page - 1 ) * $per_page;

		// Create a WHERE clause based on the conditions in $args
		$where_clause = '1 = %d'; // Default condition
		$where_values = array( 1 ); // Default value for the default condition

		// Implement the query to fetch the latest posts since $last_post_id here
		$tables = UserWall_WP_Table_Manager::get_table_names();

		// Create a WHERE clause based on the conditions in $args
		$where_clause = '1 = 1'; // Default condition

		if ( 'posts' === $args['type'] ) {
			// Add a condition based on the type
			//$where_clause = "post_type = %s";
			$post_type = sanitize_text_field( $args['type'] ); // Sanitize the post_type
			//$where_values[] = $post_type;
		}

		if ( 0 !== $args['object_id'] && 'user-posts' === $args['type'] ) {
			// Add a condition based on the object_id
			$where_clause  .= ' AND user_id = %d';
			$object_id      = intval( $args['object_id'] ); // Sanitize the object_id
			$where_values[] = $object_id;
		}

		// Make some query filters only available to admin.
		if ( current_user_can( 'manage_options' ) ) {

			// Searching content by query.
			// phpcs:ignore: WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( isset( $_REQUESTS['sq'] ) ) {
				$search_query     = sanitize_text_field( $_REQUEST['sq'] ); // The wildcard character
				$escaped_wildcard = $this->wpdb->esc_like( $search_query ); // Escaping the wildcard
				$where_clause    .= $this->wpdb->prepare( ' AND post_content LIKE %s', $escaped_wildcard );
			}
			// phpcs:ignore: WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( isset( $_REQUESTS['user_id'] ) ) {
				$user_id       = absint( $_REQUEST['user_id'] );
				$where_clause .= $this->wpdb->prepare( ' AND user_id = %d', $user_id );
			}

			// Filter for date range.
			if ( isset( $_REQUEST['date_from'] ) && '' !== $_REQUEST['date_from'] ) {
				$date_from     = sanitize_text_field( $_REQUEST['date_from'] );
				$where_clause .= $this->wpdb->prepare( ' AND creation_date >= %s', $date_from );
			}

			if ( isset( $_REQUEST['date_end'] ) && '' !== $_REQUEST['date_end'] ) {
				$date_end      = sanitize_text_field( $_REQUEST['date_end'] );
				$where_clause .= $this->wpdb->prepare( ' AND creation_date <= %s', $date_end );
			} else {
				// If date_end is empty, set it to today
				//$today = date('Y-m-d');
				//$where_clause .= $this->wpdb->prepare(" AND creation_date <= %s", $today);
			}
		}

		if ( $args['oldest_id'] ) {
			$where_clause .= $this->wpdb->prepare( ' AND p.post_id < %d', absint( $args['oldest_id'] ) );
		}

		if ( $args['latest_id'] ) {
			$where_clause .= $this->wpdb->prepare( ' AND p.post_id > %d', absint( $args['latest_id'] ) );
		}

		if ( $args['object_id'] ) {
			$object_ids = array();
			if ( is_array( $args['object_id'] ) ) {
				$object_ids = $args['object_id'];
			} else {
				$integers = explode( ',', str_replace( ' ', '', $args['object_id'] ) );

				// Convert each element to an integer
				$object_ids = array_map( 'intval', $integers );
			}
			error_log( print_r( $object_ids, true ) );
			if ( ! empty( $object_ids ) ) {
				if ( 'user' === $args['object'] ) {
					if ( count( $object_ids ) > 1 ) {
						$where_clause .= $this->wpdb->prepare( ' AND p.user_id IN (%s) ', implode( ',', $object_ids ) );
					} else {
						$where_clause .= $this->wpdb->prepare( ' AND p.user_id = %d ', $object_ids[0] );
					}
				}
			}
		}

		// Add additional conditions based on other $args as needed
		$where_clause = apply_filters( 'userwall_wp_get_posts_where_clause', $where_clause, $args );

		//$where_values = apply_filters( 'userwall_wp_get_posts_where_values', $where_values, $args );
		// Build the SQL query
		$limit     = intval( $per_page ); // Sanitize the limit
		$sql_query = $this->wpdb->prepare(
			"SELECT p.*, 
            (SELECT COUNT(*) FROM {$tables['comments']} WHERE post_id = p.post_id AND parent_id = 0 ) AS comments_count,
            (SELECT COUNT(*) FROM {$tables['likes']} WHERE post_id = p.post_id) AS reactions_count
            FROM {$this->table_posts} p
            WHERE {$where_clause}
            ORDER BY {$args['order_by']} {$args['order']}
            LIMIT %d
            OFFSET %d", // Add the LIMIT and OFFSET clauses
			$limit, // Pass the limit value
			$offset, // Pass the offset value
			...$where_values // Pass the array of values
		);

		$posts = $this->wpdb->get_results( $sql_query );
		error_log( $this->wpdb->last_query );
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$posts[] = $this->transform_post( $post );
			}
		}

		return apply_filters( 'userwall_wp_get_posts', $posts, $args );
	}

	/**
	 * Report a post.
	 *
	 * @param int $post_id The ID of the post to report.
	 * @param int $reporter_user_id The ID of the user reporting the post.
	 * @param string $report_reason The reason for reporting the post.
	 * @return bool Whether the report was successful or not.
	 */
	public function report_post( $post_id, $reporter_user_id, $report_reason ) {
		$table_reports = $wpdb->prefix . 'userwall_reports';

		$insert_data = array(
			'reporter_user_id'    => $reporter_user_id,
			'reported_content_id' => $post_id,
			'report_reason'       => $report_reason,
			'report_date'         => current_time( 'mysql' ),
		);

		$result = $this->wpdb->insert( $table_reports, $insert_data );

		if ( false !== $result ) {
			do_action( 'userwall_wp_report_post', $reporter_user_id, $post_id, $report_reason );
		}
		return false !== $result;
	}

	/**
	 * Block a user from viewing a specific post.
	 *
	 * @param int $user_id The ID of the user who wants to block another user.
	 * @param int $blocked_user_id The ID of the user to be blocked.
	 * @param int $post_id The ID of the post from which the user is blocked.
	 * @return bool Whether the user was successfully blocked for the post or not.
	 */
	public function block_user_for_post( $user_id, $blocked_user_id, $post_id ) {
		// Implement the logic to add a record in your database table
		// to indicate that $user_id has blocked $blocked_user_id for $post_id

		$table_blocklist = $wpdb->prefix . 'userwall_blocklist';

		$insert_data = array(
			'user_id'         => $user_id,
			'blocked_user_id' => $blocked_user_id,
			'post_id'         => $post_id,
			'block_date'      => current_time( 'mysql' ),
		);

		$result = $this->wpdb->insert( $table_blocklist, $insert_data );

		if ( false !== $result ) {
			do_action( 'userwall_wp_user_blocked_for_post', $user_id, $blocked_user_id, $post_id );
		}

		return false !== $result;
	}


	/**
	 * Follow the author of a specific post.
	 *
	 * @param int $user_id The ID of the user who wants to follow the author.
	 * @param int $author_id The ID of the author to be followed.
	 * @param int $post_id The ID of the post for which the author is followed.
	 * @return bool Whether the user successfully followed the author or not.
	 */
	public function follow_author_of_post( $user_id, $author_id, $post_id ) {
		// Implement the logic to add a record in your database table
		// to indicate that $user_id is following $author_id for $post_id

		$table_followers = $wpdb->prefix . 'userwall_user_followers';

		$insert_data = array(
			'user_id'          => $user_id,
			'follower_user_id' => $author_id,
			'post_id'          => $post_id,
			'follow_date'      => current_time( 'mysql' ),
		);

		$result = $this->wpdb->insert( $table_followers, $insert_data );

		if ( false !== $result ) {
			do_action( 'userwall_wp_user_followed_author', $user_id, $author_id, $post_id );
		}

		return false !== $result;
	}


	/**
	 * Save a post to a user's bookmarks.
	 *
	 * @param int $user_id The ID of the user who wants to save the post.
	 * @param int $post_id The ID of the post to be saved to bookmarks.
	 * @return bool Whether the user successfully saved the post to bookmarks or not.
	 */
	public function save_post_to_bookmarks( $user_id = 0, $post_id = 0 ) {
		$table_bookmarks = $wpdb->prefix . 'userwall_bookmarks';

		$insert_data = array(
			'user_id'       => $user_id,
			'post_id'       => $post_id,
			'bookmark_date' => current_time( 'mysql' ),
		);

		$result = $this->wpdb->insert( $table_bookmarks, $insert_data );

		if ( false !== $result ) {
			do_action( 'userwall_wp_post_saved_to_bookmarks', $user_id, $post_id );
		}

		return false !== $result;
	}


	/**
	 * Add a comment to a post.
	 *
	 * @param int $user_id The ID of the user adding the comment.
	 * @param int $post_id The ID of the post to which the comment is added.
	 * @param string $comment_content The content of the comment.
	 * @return int|false The new comment ID on success, false on failure.
	 */
	public function add_comment( $user_id, $post_id, $comment_content, $parent_comment = 0 ) {
		$table_comments = $this->wpdb->prefix . 'userwall_comments';

		$insert_data = array(
			'user_id'         => $user_id,
			'post_id'         => $post_id,
			'comment_content' => $this->trim_p_tags( $comment_content ),
			'comment_date'    => current_time( 'mysql' ),
		);

		if ( $parent_comment ) {
			$insert_data['parent_id'] = $parent_comment;
		}
		$result = $this->wpdb->insert( $table_comments, $insert_data );

		if ( $result ) {
			$comment_id = $this->wpdb->insert_id;
			return $comment_id;
		} else {
			return false;
		}
	}

	/**
	 * Add a like or reaction to a post.
	 *
	 * @param int $user_id The ID of the user adding the like/reaction.
	 * @param int $post_id The ID of the post to which the like/reaction is added.
	 * @param string $reaction_type The type of reaction (e.g., 'like', 'love', 'haha').
	 * @return bool Whether the like/reaction was successfully added or not.
	 */
	public function add_like_or_reaction( $user_id, $post_id, $comment_id = 0, $reaction_type = 'like' ) {
		global $wpdb;
		$table_likes = $wpdb->prefix . 'userwall_likes';

		$insert_data = array(
			'user_id'       => $user_id,
			'post_id'       => $post_id,
			'comment_id'    => $comment_id,
			'reaction_type' => $reaction_type,
		);

		$result = $this->wpdb->insert( $table_likes, $insert_data );

		return false !== $result;
	}


	/**
	 * Delete a comment by ID.
	 *
	 * @param int $comment_id The ID of the comment to delete.
	 * @return bool Whether the deletion was successful or not.
	 */
	public function delete_comment( $comment_id ) {
		$table_comments = $wpdb->prefix . 'userwall_comments';

		do_action( 'userwall_wp_before_delete_comment', $comment_id );
		$result = $this->wpdb->delete( $table_comments, array( 'comment_id' => $comment_id ) );
		do_action( 'userwall_wp_after_delete_comment', $comment_id );

		return false !== $result;
	}

	/**
	 * Get user ID by post ID.
	 *
	 * @param int $post_id The ID of the post.
	 * @return int|false The user ID on success, false on failure.
	 */
	public function get_user_id_by_post_id( $post_id ) {
		$query = $this->wpdb->prepare(
			"SELECT user_id
            FROM {$this->table_posts}
            WHERE post_id = %d",
			$post_id
		);

		$user_id = $this->wpdb->get_var( $query );

		if ( null !== $user_id ) {
			return (int) $user_id;
		}

		return false;
	}

	/**
	 * Get a comment by its ID.
	 *
	 * @param int $comment_id The ID of the comment to retrieve.
	 * @return object|false Comment object on success, false on failure.
	 */
	public function get_comment_by_id( $comment_id ) {
		$tables  = UserWall_WP_Table_Manager::get_table_names();
		$comment = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT c.*
                FROM {$tables['comments']} c
                WHERE c.comment_id = %d",
				$comment_id
			)
		);
		$comment = $this->transform_post( $comment );

		return apply_filters( 'userwall_wp_get_comment_by_id', $comment, $comment_id );
	}

	/**
	 * Get a comment by its ID.
	 *
	 * @param int $comment_id The ID of the comment to retrieve.
	 * @return object|false Comment object on success, false on failure.
	 */
	public function get_comment_by_id3( $comment_id ) {
		$tables  = UserWall_WP_Table_Manager::get_table_names();
		$comment = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT c.*
                FROM {$tables['comments']} c
                WHERE c.comment_id = %d",
				$comment_id
			)
		);

		return apply_filters( 'userwall_wp_get_comment_by_id', $comment, $comment_id );
	}

	/**
	 * Get comments by post ID.
	 *
	 * @param int $post_id The ID of the post.
	 * @param int $limit Number of comments to retrieve (optional, default is 5).
	 * @return array List of comment objects for the post.
	 */
	public function get_comments_by_post_id( $post_id, $limit = 5 ) {
		$comments = $this->get_comments_recursive( $post_id, 0, $limit );

		return apply_filters( 'userwall_wp_get_comments_by_post_id', $comments, $post_id, $limit );
	}

	private function get_comments_recursive( $post_id, $parent_id, $limit ) {
		$tables  = UserWall_WP_Table_Manager::get_table_names();
		$results = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT 
					c.*,
					(SELECT COUNT(comment_id) FROM {$tables['comments']} WHERE parent_id = c.comment_id) AS replies_count,
            		(SELECT COUNT(like_id) FROM {$tables['likes']} WHERE comment_id = c.comment_id) AS reactions_count 
					FROM {$tables['comments']} AS c WHERE c.post_id = %d AND c.parent_id = %d ORDER BY c.comment_date DESC LIMIT %d",
				$post_id,
				$parent_id,
				$limit
			)
		);

		$comments = array();

		foreach ( $results as $comment ) {
			$comment->child_comments = array();
			$child_comments          = $this->get_comments_recursive( $post_id, $comment->comment_id, $limit );
			if ( ! empty( $child_comments ) ) {
				$comment->child_comments = $child_comments;
			}
			$comments[] = $this->transform_post( $comment );
		}

		return $comments;
	}

	/**
	 * Update an existing comment.
	 *
	 * @param int $comment_id The ID of the comment to update.
	 * @param string $new_content The updated comment content.
	 * @return bool Whether the update was successful or not.
	 */
	public function update_comment( $comment_id, $new_content ) {
		$tables = UserWall_WP_Table_Manager::get_table_names();
		// Sanitize and validate data as needed

		$update_data = array(
			'comment_content' => $new_content,
		);

		$result = $this->wpdb->update( $tables['comments'], $update_data, array( 'comment_id' => $comment_id ) );
		do_action( 'userwall_wp_update_comment', $comment_id, $update_data );

		return false !== $result;
	}

	public function get_total_comment_by_post_id( $post_id = 0 ) {
		if ( ! $post_id ) {
			return 0;
		}

		$tables   = UserWall_WP_Table_Manager::get_table_names();
		$comments = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(c.comment_id)
                FROM {$tables['comments']} c
                WHERE c.post_id = %d",
				absint( $post_id )
			)
		);

		return $comments;
	}

	private function trim_p_tags( $html_content = '' ) {
		// Remove empty paragraphs
		$cleaned_content = preg_replace( '/<p><br><\/p>/', '', $html_content );

		// You might want to remove paragraphs that contain only whitespace as well
		$cleaned_content = preg_replace( '/<p>\s*<\/p>/', '', $cleaned_content );

		return $cleaned_content;
	}
}
