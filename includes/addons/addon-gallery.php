<?php
class ThreadsWP_Addon_Gallery extends Threads_WP_Base_Addon {
    private $table;

    public function __construct() {
       
        parent::__construct();
        global $wpdb;
        $this->table = $wpdb->prefix . 'threads_media';
    }
    public function get_id() {
        return 'gallery';
    }

    public function get_name() {
        return __( 'Gallery', 'thread-wp' );
    }

    public function get_description() {
        return __( 'Gallery', 'thread-wp' );
    }

    public function get_author() {
        return __( 'ThreadWP', 'thread-wp' );
    }

    public function get_version() {
        return '1.0';
    }

    public function activate_addon() {
        global $wpdb;
        
        $table_posts = $wpdb->prefix . 'threads_posts';
        $table_media = $wpdb->prefix . 'threads_media';
        $table_albums = $wpdb->prefix . 'threads_albums';

       // SQL query to create the 'threads_media' table
        $sql_query_media = "CREATE TABLE IF NOT EXISTS $table_media (
            media_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            file_path VARCHAR(255) NOT NULL,
            description TEXT,
            post_id INT UNSIGNED NOT NULL,
            INDEX post_id_index (post_id),
            FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
        )";

         // Array of SQL queries for the first 5 tables
        $sql_queries = array(
            $sql_query_media,
        );

        // Include the WordPress database upgrade file
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Execute the SQL queries to create the tables
        foreach ($sql_queries as $sql_query) {
            dbDelta($sql_query);
        }
    }

    public function deactivate_addon() {
        global $wpdb;

        $table_media = $wpdb->prefix . 'threads_media';

        // SQL queries to drop the tables
        $sql_queries = array(
            "DROP TABLE IF EXISTS $table_media",
        );

        // Delete the tables
        foreach ($sql_queries as $sql_query) {
            $wpdb->query($sql_query);
        }
    }

    public function hooks() {
        add_filter( 'thread_wp_post_tabs', array( $this, 'add_tab' ) );
        add_action( 'threads_wp_tab_content', array( $this, 'tab_content' ) );
        add_action( 'thread_wp_create_post', array( $this, 'upload_media_files' ), 10, 1 );
        add_filter( 'thread_wp_get_post_by_id', array( $this, 'thread_wp_get_post_by_id' ), 10, 2 );
        add_filter( 'thread_wp_get_posts', array( $this, 'add_image_to_posts_threads' ), 10, 2 );
        add_action( 'thread_wp_before_delete_post', array( $this, 'thread_wp_before_delete_post' ), 10, 1 );
        add_action( 'wp_footer', array( $this, 'add_gallery_hook' ) );
    }

    public function thread_wp_before_delete_post( $post_id ) {
        global $wpdb;

        $table_media = $wpdb->prefix . 'threads_media';
        $table_posts = $wpdb->prefix . 'threads_posts';

        $media = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT m.*, p.post_type FROM {$table_media} AS m LEFT JOIN {$table_posts} AS p ON p.post_id=m.post_id WHERE m.post_id = %d",
                $post_id 
            )
        );

        if ( ! empty( $media  ) ) {
            foreach( $media as $row ) {
                // Delete the file
                $file_manager = new Threads_WP_FileManager();
                $file_manager->deleteFile( $row->file_path );

                $wpdb->delete( $table_media, array( 'media_id' => $row->media_id ) );
            }
        }
    }

    public function add_gallery_hook() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                /*wp.hooks.addFilter('thread_wp_content_filter', 'custom_thread_wp_filter', function(post) {
                    // Modify the content here using your custom logic.                    
                    return post;
                });*/

                wp.hooks.addAction('threads_wp_post_rendered', 'customAction', function(post){
                    var threadDiv;
                    if ( post.images && post.images.length > 0 ) {
                        threadDiv = jQuery('.threads-wp-thread[data-postid="' + post.post_id + '"]');
                        threadDiv.find('.threads-wp-thread-content').after( '<div class="threads-wp-content-images"></div>' );
                        jQuery.each(post.images, function(i, image ) {
                            console.log(image.url);
                            threadDiv.find('.threads-wp-content-images').prepend(
                                `<div class="" data-media_id="${image.media_id}"><img class="thread-wp-wall-image" src="${image.url})" /></div>`
                            );
                        });
                    }
                     //-> 'This is a test string' (from the above usage example)
                });
            });
        </script>
        <?php
    }

    public function add_tab( $tabs = array() ) {
        $tabs['image'] = __( 'Image', 'threads-wp' );
        return $tabs;
    }

    public function tab_content() {
        ?>
        <div class="threads-tab-content" data-tab="image">
            <!-- Content textarea -->
            <textarea name="image_content" placeholder="Write your content here"></textarea>
            <!-- Image upload area -->
            <div class="image-upload-area">
                <label for="image-upload" class="upload-label">Click to Upload Images</label>
                <input type="file" name="post_images[]" accept="image/*" id="image-upload" multiple>
            </div>
        </div>
        <?php
    }

    public function upload_media_files( $post_id = 0 ) {
        global $wpdb;

        if ( ! $post_id ) {
            return;
        }
        
        $user_id = get_current_user_id();
        $file_manager = new Threads_WP_FileManager( $user_id );
        $media_ids = array();
        if ( ! empty( $_FILES['post_images'] ) && count( $_FILES['post_images']['error'] ) == 0 ) {
            foreach( $_FILES['post_images']['name'] as $index => $value ) {
                $file_path = $file_manager->uploadFile( array(
                    'name' => $_FILES['post_images']['name'][ $index ],
                    'full_path' => $_FILES['post_images']['full_path'][ $index ],
                    'type' => $_FILES['post_images']['type'][ $index ],
                    'tmp_name' => $_FILES['post_images']['tmp_name'][ $index ],
                    'error' => $_FILES['post_images']['error'][ $index ],
                    'size' => $_FILES['post_images']['size'][ $index ],
                ));

                $insert_result = $wpdb->insert( $wpdb->prefix . 'threads_media', array('post_id' => $post_id, 'file_path' => $file_path ) );
                if ( $insert_result !== false ) {
                    $media_ids[] = $insert_result;
                    do_action( 'threads_wp_after_image_added', $insert_result, $post_id,  $file_path );
                }
            }

            do_action( 'threads_wp_after_image_upload_complete', $post_id, $media_ids );
        }
    }

    private function transform_media( $media, $post ) {
        $obj = array();
        $file_manager = new Threads_WP_FileManager( $post->user_id );
        if ( ! empty( $media ) ) {
            foreach ( $media as $media_item ) {
                //unset( $media_item->file_path );
                $file_path = $media_item->file_path;
                if ( ! file_exists( $file_path ) ) {
                    continue;
                }
                // Use pathinfo() to get the file name
                $file_info = pathinfo($file_path);

                // Access the 'filename' key in the $file_info array to get the file name
                $file_name = $file_info['basename'];
                $media_item->url = $file_manager->getFileUrl( $file_name );
                $obj[] = $media_item;
            }
        }
        
        return $obj;
    }

    public function thread_wp_get_post_by_id( $post = array(), $post_id = 0 ) {
        global $wpdb;
        $media = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE post_id = %d",
                $post_id
            )
        );

        if ( ! empty( $media ) ) {
            $post->images = $this->transform_media( $media, $post );
        } 
        return $post;
    }
    
    private function get_images_by_post_id( $post_id = 0 ) {
        global $wpdb;

        if ( ! $post_id ) {
            return false;
        }

        $media = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE post_id = %d",
                $post_id
            )
        );

        return ! empty( $media ) ? $media : false;
    }

    public function add_image_to_posts_threads( $posts = array() ) {
        if ( ! empty( $posts ) ) {
            foreach ( $posts as $index => $post ) {
                //$post = new Threads_WP_Post( $post );
                //$post_id = $post->get_post_id();
                $post_id = $post->post_id;
                $media = $this->get_images_by_post_id( $post_id );
                
                if ( ! empty( $media ) ) {
                    $posts[ $index ]->images = $this->transform_media( $media, $post );
                }
            }
        }
        return $posts;
    }
}