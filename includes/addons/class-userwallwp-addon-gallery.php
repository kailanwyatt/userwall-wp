<?php
/**
 * UserWallWP_Addon_Gallery class
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserWallWP_Addon_Gallery
 */
class UserWallWP_Addon_Gallery extends UserWall_WP_Base_Addon {
	/**
	 * The table name.
	 *
	 * @var string
	 */
	private $table;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();
		global $wpdb;
		$this->table = $wpdb->prefix . 'userwall_media';
	}

	/**
	 * Get the ID.
	 *
	 * @return string The ID.
	 */
	public function get_id() {
		return 'gallery';
	}

	/**
	 * Get the name.
	 *
	 * @return string The name.
	 */
	public function get_name() {
		return __( 'Gallery', 'userwall-wp' );
	}

	/**
	 * Get the description.
	 *
	 * @return string The description.
	 */
	public function get_description() {
		return __( 'An easy way to add Photo uploads to user wall', 'userwall-wp' );
	}

	/**
	 * Get the author.
	 *
	 * @return string The author.
	 */
	public function get_author() {
		return __( 'UserWallWP', 'userwall-wp' );
	}

	/**
	 * Get the version.
	 *
	 * @return string The version.
	 */
	public function get_version() {
		return '1.0';
	}

	/**
	 * Check if the addon is ready.
	 *
	 * @return bool True if ready, false otherwise.
	 */
	public function activate_addon() {
		global $wpdb;

		$table_posts  = $wpdb->prefix . 'userwall_posts';
		$table_media  = $wpdb->prefix . 'userwall_media';
		$table_albums = $wpdb->prefix . 'userwall_albums';

		// SQL query to create the 'userwall_media' table.
		$sql_query_media = "CREATE TABLE IF NOT EXISTS $table_media (
            media_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            file_path VARCHAR(255) NOT NULL,
            description TEXT,
            post_id BIGINT UNSIGNED NOT NULL,
            INDEX post_id_index (post_id),
            FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
        )";

		// Array of SQL queries for the first 5 tables.
		$sql_queries = array(
			$sql_query_media,
		);

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Execute the SQL queries to create the tables.
		foreach ( $sql_queries as $sql_query ) {
			dbDelta( $sql_query );
		}

		return true;
	}

	/**
	 * Deactivate the addon.
	 */
	public function deactivate_addon() {
		global $wpdb;

		$table_media = $wpdb->prefix . 'userwall_media';

		// Include the WordPress database upgrade file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Delete the table.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_media ) );
	}

	/**
	 * Add hooks.
	 */
	public function hooks() {
		add_filter( 'userwall_wp_post_tabs', array( $this, 'add_tab' ) );
		add_action( 'userwall_wp_after_post_form', array( $this, 'post_form_addition' ) );
		add_action( 'userwall_wp_create_post', array( $this, 'upload_media_files' ), 10, 1 );
		add_filter( 'userwall_wp_get_post_by_id', array( $this, 'userwall_wp_get_post_by_id' ), 10, 2 );
		add_filter( 'userwall_wp_get_posts', array( $this, 'add_image_to_posts_userwall' ), 10, 2 );
		add_action( 'userwall_wp_before_delete_post', array( $this, 'userwall_wp_before_delete_post' ), 10, 1 );
		add_filter( 'userwall_wp_get_post_content_types', array( $this, 'add_content_type' ), 10, 1 );
		add_filter( 'userwall_wp_drop_tables', array( $this, 'drop_tables' ) );
	}

	/**
	 * Add content type
	 *
	 * @param array $content_post_types The content post types.
	 * @return array
	 */
	public function add_content_type( $content_post_types = array() ) {
		$content_post_types['image'] = array(
			'title' => __( 'Image', 'userwall-wp' ),
			'icon'  => 'photo',
		);
		return $content_post_types;
	}

	/**
	 * Before delete post
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function userwall_wp_before_delete_post( $post_id ) {
		global $wpdb;

		$table_media = $wpdb->prefix . 'userwall_media';
		$table_posts = $wpdb->prefix . 'userwall_posts';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$media = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
				'SELECT m.*, p.post_type FROM %i AS m LEFT JOIN %i AS p ON p.post_id=m.post_id WHERE m.post_id = %d',
				array(
					$table_media,
					$table_posts,
					$post_id,
				)
			)
		);

		if ( ! empty( $media ) ) {
			foreach ( $media as $row ) {
				// Delete the file.
				$file_manager = new UserWall_WP_FileManager();
				$file_manager->delete_file( $row->file_path );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->delete( $table_media, array( 'media_id' => $row->media_id ) );
			}
		}
	}

	/**
	 * Add tab
	 *
	 * @param array $tabs The tabs.
	 * @return array
	 */
	public function add_tab( $tabs = array() ) {
		$tabs['image'] = __( 'Image', 'userwall-wp' );
		return $tabs;
	}

	/**
	 * Post form addition
	 *
	 * @return void
	 */
	public function post_form_addition() {
		?>
		<div class="image-upload-area" style="display: none;">
			<input type="file" name="post_images[]" style="display: none;" accept="image/*" id="image-upload" multiple>
		</div>
		<?php
	}

	/**
	 * Upload media files
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function upload_media_files( $post_id = 0 ) {
		global $wpdb;

		if ( ! $post_id ) {
			return;
		}

		$nonce_field           = 'nonce';
		$nonce_action          = 'userwall_wp_nonce';
		$nonce_field_sanitized = sanitize_key( $nonce_field );
		if ( ! isset( $_REQUEST[ $nonce_field_sanitized ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_field_sanitized ] ) ), $nonce_action ) ) {
			return;
		}

		$user_id      = get_current_user_id();
		$file_manager = new UserWall_WP_FileManager( $user_id );
		$media_ids    = array();
		$file_count   = 0;

		// phpcs:ignore	WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$media_files = isset( $_FILES['post_images'] ) ? userwall_wp_clean( wp_unslash( $_FILES['post_images'] ) ) : array();
		if ( ! empty( $media_files['name'] ) && is_array( $media_files ) ) {
			$file_count = count( $media_files['name'] );
		}

		if ( $file_count > 0 ) {
			foreach ( $media_files['name'] as $index => $value ) {
				if ( isset( $media_files['error'][ $index ] ) && 0 === absint( $media_files['error'][ $index ] ) ) {
					$file_path = $file_manager->upload_file(
						array(
							'name'      => sanitize_file_name( $media_files['name'][ $index ] ),
							'full_path' => sanitize_text_field( $media_files['full_path'][ $index ] ), // Validate this as a path.
							'type'      => sanitize_mime_type( $media_files['type'][ $index ] ),
							'tmp_name'  => sanitize_text_field( $media_files['tmp_name'][ $index ] ), // Validate this as a string.
							'error'     => intval( $media_files['error'][ $index ] ), // Convert to integer.
							'size'      => intval( $media_files['size'][ $index ] ), // Convert to integer.
						)
					);

					if ( $file_path ) {
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
						$insert_result = $wpdb->insert(
							$wpdb->prefix . 'userwall_media',
							array(
								'post_id'   => $post_id,
								'file_path' => $file_path,
							)
						);

						if ( false !== $insert_result ) {
							$media_ids[] = $insert_result;
							do_action( 'userwall_wp_after_image_added', $insert_result, $post_id, $file_path );
						}
					}
				}
			}

			do_action( 'userwall_wp_after_image_upload_complete', $post_id, $media_ids );
		}
		// phpcs:enable
	}

	/**
	 * Transform media
	 *
	 * @param array  $media The media.
	 * @param object $post The post.
	 * @return array
	 */
	private function transform_media( $media, $post ) {
		$obj          = array();
		$file_manager = new UserWall_WP_FileManager( $post->user_id );
		if ( ! empty( $media ) ) {
			foreach ( $media as $media_item ) {
				$file_path = $media_item->file_path;
				if ( ! file_exists( $file_path ) ) {
					continue;
				}
				// Use pathinfo() to get the file name.
				$file_info = pathinfo( $file_path );

				// Access the 'filename' key in the $file_info array to get the file name.
				$file_name       = $file_info['basename'];
				$media_item->url = $file_manager->get_file_url( $file_name );
				$obj[]           = $media_item;
			}
		}

		return $obj;
	}

	/**
	 * Get post by ID
	 *
	 * @param mixed $post The post.
	 * @param int   $post_id The post ID.
	 * @return array
	 */
	public function userwall_wp_get_post_by_id( $post = array(), $post_id = 0 ) {
		global $wpdb;
		$cache_key = 'userwall_wp_media_' . $post_id;
		$media     = wp_cache_get( $cache_key );

		if ( false === $media ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$media = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
					'SELECT * FROM %i WHERE post_id = %d',
					array(
						$this->table,
						$post_id,
					)
				)
			);

			wp_cache_set( $cache_key, $media );
		}

		if ( ! empty( $media ) ) {
			$post->images = $this->transform_media( $media, $post );
		}
		return $post;
	}

	/**
	 * Get images by post ID
	 *
	 * @param int $post_id The post ID.
	 * @return array|false
	 */
	private function get_images_by_post_id( $post_id = 0 ) {
		global $wpdb;

		if ( ! $post_id ) {
			return false;
		}

		$cache_key = 'userwall_wp_media_' . $post_id;
		$media     = wp_cache_get( $cache_key, 'userwall_wp_media' );

		if ( false === $media ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$media = $wpdb->get_results(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder
					'SELECT * FROM %i WHERE post_id = %d',
					array(
						$this->table,
						$post_id,
					)
				)
			);

			wp_cache_set( $cache_key, $media, 'userwall_wp_media' );
		}

		return ! empty( $media ) ? $media : false;
	}

	/**
	 * Add image to posts userwall
	 *
	 * @param array $posts The posts.
	 * @return array
	 */
	public function add_image_to_posts_userwall( $posts = array() ) {
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $index => $post ) {
				$post_id = $post->post_id;
				$media   = $this->get_images_by_post_id( $post_id );

				if ( ! empty( $media ) ) {
					$posts[ $index ]->images = $this->transform_media( $media, $post );
				}
			}
		}
		return $posts;
	}

	/**
	 * Add JS
	 *
	 * @return void
	 */
	public function add_js() {
		?>
			jQuery(document).ready(function($) {
				wp.hooks.addFilter('userwall_wp_content_filter', 'custom_userwall_wp_filter', function(post) {
					// Modify the content here using your custom logic.                    
					return post;
				});
				var imageModal = jQuery('#imageModal').UserWallWPModal({
					content: '<div class="author">New Author</div><div class="caption">New Caption</div>',
					showCloseBtn: true,
					openTransition: 'fade',
					closeTransition: 'fade',
					openSpeed: 500,
					closeSpeed: 500,
					onOpen: function() {
						console.log('Modal opened.');
					},
					onClose: function() {
						console.log('Modal closed.');
					}
				})[0];
				jQuery( document ).on( 'click', '.userwall-wp-wall-image', function( e ) {
					e.preventDefault();
					imageModal.openModal();
				});

				wp.hooks.addAction('userwall_wp_after_post_submitted', 'resetGallery', function() {
					jQuery('.image-preview').remove();
					jQuery('#image-upload').val('');
				});

				wp.hooks.addAction('userwall_wp_post_rendered', 'customAction', function(post){
					var threadDiv;
					if ( post.images && post.images.length > 0 ) {
						threadDiv = jQuery('.userwall-wp-thread[data-postid="' + post.post_id + '"]');
						threadDiv.find('.userwall-wp-thread-content').after( `<div class="userwall-wp-content-images userwall-wp-slider userwall-wp-image-slider" data-gallery="` + post.post_id + `"></div>` );
						jQuery.each(post.images, function(i, image ) {
							threadDiv.find('.userwall-wp-content-images').prepend(
								`<div class="userwall-wp-content-image-item userwall-wp-slide" data-media_id="${image.media_id}"><img class="userwall-wp-wall-image" src="${image.url}" /></div>`
							);
						});


						jQuery(".userwall-wp-image-slider").UserWallWPSlider({
							slideClass: 'userwall-wp-slide',
							slideWrapperClass: 'slider-container',
							showArrows: true,
							leftArrowHTML: `<span class="userwall-wp-gallery-arrow" data-slide-left><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
	<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
</svg>
</span>`,
							rightArrowHTML: `<span class="userwall-wp-gallery-arrow" data-slide-right><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
	<path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
</svg>
</span>`,
							showPagination: true,
							slideSpeed: 500,
							draggable: false,
							keyboardNavigation: true,
							onLeftSlide: function(currentSlide) {
								console.log("Left slide event: " + currentSlide);
							},
							onRightSlide: function(currentSlide) {
								console.log("Right slide event: " + currentSlide);
							},
							onSlide: function(currentSlide) {
								console.log("Slide event: " + currentSlide);
							}
						});
					}
				});
			});
		<?php
	}

	/**
	 * Drop tables
	 *
	 * @param array $sql_queries The SQL queries.
	 * @return array
	 */
	public function drop_tables( $sql_queries = array() ) {
		global $wpdb;

		$table_media = $wpdb->prefix . 'userwall_media';

		$sql_queries[] = $table_media;

		return $sql_queries;
	}
}
