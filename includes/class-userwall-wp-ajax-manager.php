<?php
/**
 * UserWall_WP_AJAX_Manager class
 *
 * @package UserWall_WP
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * UserWall_WP_AJAX_Manager class
 *
 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared WordPress.DB.PreparedSQL.InterpolatedNotPrepared
 */
class UserWall_WP_AJAX_Manager {
	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	private $nonce_action = 'userwall_wp_nonce';

	/**
	 * Nonce field.
	 *
	 * @var string
	 */
	private $nonce_field = 'nonce';

	/**
	 * Construct function.
	 */
	public function __construct() {
		// Add action hooks for handling AJAX requests.
		add_action( 'wp_ajax_userwall_wp_save_post', array( $this, 'userwall_wp_save_post' ) );
		add_action( 'wp_ajax_fetch_data_by_thread', array( $this, 'fetch_data_by_thread' ) );
		add_action( 'wp_ajax_nopriv_fetch_data_by_thread', array( $this, 'fetch_data_by_thread' ) );

		add_action( 'wp_ajax_fetch_latest_thread_notice', array( $this, 'fetch_latest_thread_notice' ) );
		add_action( 'wp_ajax_nopriv_fetch_latest_thread_notice', array( $this, 'fetch_latest_thread_notice' ) );

		add_action( 'wp_ajax_fetch_latest_thread', array( $this, 'fetch_latest_thread' ) );
		add_action( 'wp_ajax_nopriv_fetch_latest_thread', array( $this, 'fetch_latest_thread' ) );

		// Post action.
		add_action( 'wp_ajax_userwall_wp_posts_action', array( $this, 'handle_post_action' ) );

		add_action( 'wp_ajax_userwall_wp_load_more_posts', array( $this, 'load_more_posts' ) );
		add_action( 'wp_ajax_nopriv_userwall_wp_load_more_posts', array( $this, 'load_more_posts' ) );

		add_action( 'wp_ajax_userwall_wp_load_comments', array( $this, 'load_comments' ) );
		add_action( 'wp_ajax_nopriv_userwall_wp_load_comments', array( $this, 'load_comments' ) );

		add_action( 'wp_ajax_userwall_wp_update_post', array( $this, 'userwall_wp_update_post' ) );
		add_action( 'wp_ajax_userwall_wp_update_comment', array( $this, 'userwall_wp_update_comment' ) );

		add_action( 'wp_ajax_userwall_wp_post_comment', array( $this, 'post_comment' ) );

		add_action( 'wp_ajax_userwall_wp_comment_reply', array( $this, 'comment_reply_callback' ) );
		add_action( 'wp_ajax_userwall_wp_post_like', array( $this, 'userwall_wp_post_like' ) );

		add_action( 'wp_ajax_fetch_usernames', array( $this, 'fetch_usernames_callback' ) );
		add_action( 'wp_ajax_nopriv_fetch_usernames', array( $this, 'fetch_usernames_callback' ) );
	}

	/**
	 * Check if the AJAX nonce is valid.
	 *
	 * @param string $nonce_field The nonce field name.
	 * @param string $nonce_action The nonce action name.
	 *
	 * @return bool Whether the nonce is valid.
	 */
	private function is_valid_nonce( $nonce_field = 'nonce', $nonce_action = 'userwall_wp_nonce' ) {
		$nonce_field_sanitized = sanitize_key( $nonce_field );
		if ( isset( $_REQUEST[ $nonce_field_sanitized ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_field_sanitized ] ) ), $nonce_action ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Handle the post action.
	 */
	public function handle_post_action() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Get the action type from the AJAX request.
		$action_type = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';

		// Get the post ID associated with the action (if needed).
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		$current_user_id = get_current_user_id();
		$post_manager    = new UserWall_WP_Post_Manager();

		// Perform actions based on the action type.
		switch ( $action_type ) {
			case 'Delete':
				if ( $post_manager->can_moderate( $post_id, $current_user_id ) ) {
					// Handle Delete action.

					$posts = $post_manager->delete_post( $post_id );
				} else {
					wp_send_json_error( array( 'message' => 'You do not have permission to delete this post.' ) );
				}
				break;
			case 'Block':
				$blocked_user_id = $post_manager->get_user_id_by_post_id( $post_id );
				// Handle Block action.
				$post_manager->block_user_for_post( $current_user_id, $blocked_user_id, $post_id );
				// You can perform your block logic here.
				break;
			case 'Report':
				// Handle Report action.
				// You can perform your report logic here.
				break;
			case 'Embed-post':
				// Handle Embed Post action.
				// You can perform your embed logic here.
				break;
			case 'Save':
				// Handle Save action.
				$post_manager->save_post_to_bookmarks( $current_user_id, $post_id );
				break;
			case 'Follow':
				// Handle Follow action.
				// $post->follow_author_of_post() // @todo Complete this method.
				break;
			default:
				// Handle unknown action type.
				wp_send_json_error( array( 'message' => 'Unknown action type.' ) );
		}

		// Send a success response (if needed).
		wp_send_json_success( array( 'message' => 'Action successful.' ) );
	}

	/**
	 * Update Comment.
	 *
	 * @return void
	 */
	public function userwall_wp_update_comment() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to update comment.', 'userwall-wp' ) ) );
		}

		// Get current user ID.
		$current_user_id = get_current_user_id();

		// Get post content from the form.
		$post_content = ! empty( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		$comment_id = ! empty( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;

		$post_manager  = new UserWall_WP_Post_Manager();
		$insert_result = $post_manager->update_comment( $comment_id, $post_content );

		if ( false === $insert_result ) {
			wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
		} else {
			$comments    = $post_manager->get_comment_by_id( $comment_id );
			$return_data = array(
				'post_id'  => $post_id,
				'comments' => array(
					$comments,
				),
			);
			wp_send_json_success( $return_data );
		}
	}

	/**
	 * Update Post.
	 *
	 * @return void
	 */
	public function userwall_wp_update_post() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to create a post.', 'userwall-wp' ) ) );
		}

		// Get current user ID.
		$current_user_id = get_current_user_id();

		// Get post content from the form.
		$post_content = ! empty( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		// Prepare data to insert into the userwall_posts table.
		$post_data = array(
			'content' => $post_content,
		);

		$post_manager  = new UserWall_WP_Post_Manager();
		$insert_result = $post_manager->update_post( $post_id, $post_data );

		if ( false === $insert_result ) {
			wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
		} else {
			$post        = $post_manager->get_post_by_id( $post_id );
			$return_data = array(
				'post_type' => $post->post_type,
				'posts'     => array(
					$post,
				),
			);
			wp_send_json_success( $return_data );
		}
	}

	/**
	 * Save Post.
	 *
	 * @return void
	 */
	public function userwall_wp_save_post() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to create a post.', 'userwall-wp' ) ) );
		}

		// Get current user ID.
		$current_user_id = get_current_user_id();

		// Get post content from the form.
		$post_content = ! empty( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		$post_tab = ! empty( $_POST['post_tab'] ) ? sanitize_text_field( wp_unslash( $_POST['post_tab'] ) ) : 'post';

		// Title.
		$post_title = ! empty( $_POST['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST['post_title'] ) ) : '';

		// Prepare data to insert into the userwall_posts table.
		$post_data = array(
			'title'         => $post_title,
			'content'       => $post_content,
			'post_type'     => 'posts', // Change to your desired post type.
			'creation_date' => current_time( 'mysql' ),
			'user_id'       => $current_user_id,
		);

		$post_manager  = new UserWall_WP_Post_Manager();
		$insert_result = $post_manager->create_post( $post_data );
		if ( false === $insert_result ) {
			wp_send_json_error( array( 'message' => __( 'Post creation failed. Please try again.', 'userwall-wp' ) ) );
		} else {
			$post        = $post_manager->get_post_by_id( $insert_result );
			$return_data = array(
				'post_type' => $post->post_type,
				'posts'     => array(
					$post,
				),
			);
			wp_send_json_success( $return_data );
		}
		//phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Fetch data by thread.
	 *
	 * @return void
	 */
	public function fetch_data_by_thread() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$per_page = ! empty( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 30;

		$page = ! empty( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;

		$object_id = ! empty( $_GET['object_id'] ) ? absint( $_GET['object_id'] ) : 30;

		$post_type    = ! empty( $_GET['post_type'] ) ? absint( $_GET['post_type'] ) : 'posts';
		$args         = array(
			'per_page'  => $per_page,
			'page'      => $page,
			'object_id' => $object_id,
			'type'      => $post_type,
		);
		$post_manager = new UserWall_WP_Post_Manager();
		$posts        = $post_manager->get_posts( $args );
		wp_send_json(
			array(
				'posts'  => $posts,
				'params' => $args,
			)
		);
	}

	/**
	 * Fetch Latest Thread Notice.
	 *
	 * @return void
	 */
	public function fetch_latest_thread_notice() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$post_id      = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
		$post_manager = new UserWall_WP_Post_Manager();
		$posts        = $post_manager->get_posts_latest_count( $post_id );
		wp_send_json_success( array( 'message' => $posts ) );
	}

	/**
	 * Fetch Latest Thread.
	 *
	 * @return void
	 */
	public function fetch_latest_thread() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$post_id      = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : 0;
		$post_manager = new UserWall_WP_Post_Manager();
		$posts        = $post_manager->get_posts_latest( $post_id );
		wp_send_json_success( array( 'posts' => $posts ) );
	}

	/**
	 * Load more posts.
	 *
	 * @return void
	 */
	public function load_more_posts() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$latest_id = ! empty( $_POST['last_post'] ) ? absint( $_POST['last_post'] ) : 0;

		$per_page = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 5;

		$user_id = ! empty( $_POST['user_wall'] ) ? absint( $_POST['user_wall'] ) : 0;

		$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		$post_manager = new UserWall_WP_Post_Manager();
		$posts        = $post_manager->get_posts(
			array(
				'oldest_id' => $latest_id,
				'post_id'   => $post_id,
				'per_page'  => $per_page,
				'order'     => 'DESC',
				'object_id' => $user_id,
			)
		);
		wp_send_json_success( array( 'posts' => $posts ) );
	}

	/**
	 * Load comments.
	 *
	 * @return void
	 */
	public function load_comments() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$post_id      = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		$post_manager = new UserWall_WP_Post_Manager();
		$comments     = $post_manager->get_comments_by_post_id( $post_id );
		if ( empty( $comments ) ) {
			$return_data = array(
				'comments' => array(),
			);
		} else {
			$return_data = array(
				'comments' => array(
					$comments,
				),
			);
		}

		wp_send_json_success( $return_data );
	}

	/**
	 * Post comment.
	 *
	 * @return void
	 */
	public function post_comment() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Check if user is logged in.
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to add a comment.', 'userwall-wp' ) ) );
		}

		// Get current user ID.
		$current_user_id = get_current_user_id();

		// Get post content from the form.
		$post_content = ! empty( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		// Prepare data to insert into the userwall_posts table.
		$post_data = array(
			'content' => $post_content,
		);

		$post_manager  = new UserWall_WP_Post_Manager();
		$insert_result = $post_manager->add_comment( $current_user_id, $post_id, $post_content );

		if ( false === $insert_result ) {
			wp_send_json_error( array( 'message' => __( 'Could not add comment. Please try again.', 'userwall-wp' ) ) );
		} else {
			$comments    = $post_manager->get_comment_by_id( $insert_result );
			$total       = $post_manager->get_total_comment_by_post_id( $post_id );
			$return_data = array(
				'total'    => $total,
				'comments' => array(
					$comments,
				),
			);
			wp_send_json_success( $return_data );
		}
	}

	/**
	 * Comment reply callback.
	 *
	 * @return void
	 */
	public function comment_reply_callback() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Get the comment content from the AJAX request.
		if ( isset( $_POST['commentContent'] ) ) {
			$comment_content = sanitize_text_field( wp_unslash( $_POST['commentContent'] ) );
		} else {
			$comment_content = '';
		}

		// Get current user ID.
		$current_user_id = get_current_user_id();
		$post_id         = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$parent_comment  = ! empty( $_POST['commentId'] ) ? intval( $_POST['commentId'] ) : 0;

		$post_manager  = new UserWall_WP_Post_Manager();
		$insert_result = $post_manager->add_comment( $current_user_id, $post_id, $comment_content, $parent_comment );

		if ( false === $insert_result ) {
			wp_send_json_error( array( 'message' => __( 'Could not add comment. Please try again.', 'userwall-wp' ) ) );
		} else {
			$comments    = $post_manager->get_comment_by_id( $insert_result );
			$return_data = array(
				'comments' => array(
					$comments,
				),
			);
			wp_send_json_success( $return_data );
		}
	}

	/**
	 * Fetch usernames for tagging.
	 *
	 * @return void
	 */
	public function fetch_usernames_callback() {
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		// Check for the 'term' in the AJAX request.
		if ( isset( $_GET['term'] ) ) {
			$search_term = isset( $_GET['term'] ) ? trim( str_replace( '@', '', sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) ) : '';

			// Query for users.
			$user_query = new WP_User_Query(
				array(
					'search'         => '*' . esc_attr( $search_term ) . '*',
					'search_columns' => array( 'user_login', 'user_nicename' ),
					'number'         => 10, // Limit the number of results.
				)
			);

			$users = $user_query->get_results();

			$usernames = array();
			if ( ! empty( $users ) ) {
				foreach ( $users as $user ) {
					$usernames[] = $user->user_login; // or user_nicename, depending on your preference.
				}
			}

			wp_send_json_success( $usernames );
		}

		wp_send_json_error( esc_html__( 'No term found', 'userwall-wp' ) );
	}

	/**
	 * Post like for post.
	 *
	 * @return void
	 */
	public function userwall_wp_post_like() {
		global $wpdb;
		// Check for nonce security.
		if ( ! isset( $_REQUEST[ sanitize_key( $this->nonce_field ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ sanitize_key( $this->nonce_field ) ] ) ), $this->nonce_action ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'userwall-wp' ) ) );
		}

		$table_likes = $wpdb->prefix . 'userwall_likes';
		$user_id     = get_current_user_id();
		$post_id     = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$comment_id  = ! empty( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$like_id = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
				'SELECT like_id FROM %i WHERE post_id = %d AND comment_id = %d AND user_id = %d',
				array(
					$table_likes,
					$post_id,
					$comment_id,
					$user_id,
				)
			)
		);

		// If exists then remove it.
		if ( $like_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->delete(
				$table_likes,
				array(
					'post_id'    => $post_id,
					'comment_id' => $comment_id,
					'user_id'    => $user_id,
				)
			);
		} else {
			$post_manager = new UserWall_WP_Post_Manager();
			$posts        = $post_manager->add_like_or_reaction( $user_id, $post_id, $comment_id, 'like' );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$total_likes = $wpdb->get_var(
			$wpdb->prepare(
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
				'SELECT COUNT(like_id) FROM %i WHERE post_id = %d AND comment_id = %d',
				array(
					$table_likes,
					$post_id,
					$comment_id,
				)
			)
		);
		wp_send_json_success( array( 'total' => $total_likes ) );
	}
}

new UserWall_WP_AJAX_Manager();
