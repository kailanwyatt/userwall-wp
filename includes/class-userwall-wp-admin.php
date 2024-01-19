<?php
require_once(  USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-settings.php' );


class Threads_WP_Admin {
    private static $instance;

    private $settings_api;

    private $dashboard_page_key;

    private $option_active_addons = 'threads_wp_active_addons';

    private function __construct() {
        $this->dashboard_page_key = 'userwall-wp-dashboard';
        add_action('admin_menu', array($this, 'add_menu'));
        $this->settings_api = new WP_Custom_Settings_API( 'userwall-wp-settings' );
        add_action( 'admin_init', array( $this->settings_api, 'admin_init' ) );
        add_action( 'admin_init', array( $this, 'process_addon_action' ) );
        add_action('admin_notices', array( $this, 'admin_notices' ) );
        $template = new Threads_Template();
        add_action( 'admin_enqueue_scripts',  array($template, 'enqueue_assets' ) );
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function enqueue_admin_scripts() {
        // Enqueue your scripts and styles here
        // Example:
        wp_enqueue_style('my-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
        wp_enqueue_script('my-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), '1.0', true);
    }
    
    public function add_menu() {
        
        add_menu_page(
            'Threads WP',
            'Threads WP',
            'manage_options',
            $this->dashboard_page_key,
            array($this, 'dashboard_page'),
            'dashicons-admin-generic'
        );

        // Instantiate the addon management class
        $addons_manager = new Threads_WP_Addons();

        // Additional menus
        $this->add_submenu('Posts', 'Posts', 'userwall-wp-posts', array($this, 'posts_page'));
        $this->add_submenu('Comments', 'Comments', 'userwall-wp-comments', array($this, 'comments_page'));
        if ( $addons_manager->is_active( 'groups' ) ) {
            $this->add_submenu('Groups', 'Groups', 'userwall-wp-groups', array($this, 'groups_page'));
        }
        if ( $addons_manager->is_active( 'polls' ) ) {
            $this->add_submenu('Polls', 'Polls', 'userwall-wp-polls', array($this, 'polls_page'));
        }
        if ( $addons_manager->is_active( 'gallery' ) ) {
            $this->add_submenu('Media', 'Media', 'userwall-wp-media', array($this, 'media_page'));
            $this->add_submenu('Albums', 'Albums', 'userwall-wp-albums', array($this, 'albums_page'));
        }
        $this->add_submenu('Reports', 'Reports', 'userwall-wp-reports', array($this, 'reports_page'));
        $this->add_submenu('User Reputation', 'User Reputation', 'userwall-wp-user-reputation', array($this, 'user_reputation_page'));
        $this->add_submenu('Addons', 'Addons', 'userwall-wp-addons', array($this, 'addons_page'));
        $this->add_submenu('Settings', 'Settings', 'userwall-wp-settings', array($this, 'settings_page'));
    }

    private function add_submenu($title, $menu_title, $menu_slug, $callback) {
        add_submenu_page(
            $this->dashboard_page_key,
            $title,
            $menu_title,
            'manage_options',
            $menu_slug,
            $callback
        );
    }

    public function dashboard_page() {
        // Dashboard page content goes here
    }

    // Define callback methods for each submenu page
    public function comments_page() {
        // Comments page content goes here
    }

    public function groups_page() {
        // Groups page content goes here
    }

    public function polls_page() {
        // Polls page content goes here
    }

    public function media_page() {
        // Media page content goes here
    }

    public function albums_page() {
        // Albums page content goes here
    }

    public function reports_page() {
        // Reports page content goes here
    }

    public function user_reputation_page() {
        // User Reputation page content goes here
    }

    public function posts_page() {
        $this->add_post_template();
        return;
        if ( isset( $_GET['add-post'] ) ) {
            
        } else {
            $this->posts_view_template();
        }
    }

    private function add_post_template() {
        include( USERWALL_WP_PLUGIN_DIR . '/includes/admin/templates/add-post.php' );
        include( USERWALL_WP_PLUGIN_DIR . '/templates/tmpls.php' );
    }

    private function posts_view_template() {
        if ( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }
        require_once(  USERWALL_WP_PLUGIN_DIR . 'includes/admin/tables/posts.php' );
        $posts_table = new Threads_WP_Posts_Table();

        echo '<div class="wrap"><h1>Manage Posts</h1>';
        $posts_table->display_notices();
        ?>
        <a href="<?php echo admin_url( 'admin.php?page=userwall-wp-posts&add-post' ); ?>"><?php echo esc_html_e( 'Add Post', 'userwall-wp' ); ?></a>
        <form method="get" action="<?php echo admin_url('admin.php'); ?>">
            <input type="hidden" name="page" value="userwall-wp-posts" />
            <?php
            //$posts_table->prepare_items();
            //$posts_table->search_box('Search Posts', 'post_title');
            //$posts_table->display();
            ?>
        </form>
        <?php
    }

    public function addons_page() {
        include( USERWALL_WP_PLUGIN_DIR . '/includes/admin/templates/addons.php' );
    }

    public function settings_page() {
        
        echo '<div class="wrap">';
        echo '<h1>' . get_admin_page_title() . '</h1>';
        $this->settings_api->show_navigation();
        echo '<div>';
        $this->settings_api->show_forms();
        echo '</div>';
        echo '</div>';
    }

    public function admin_notices() {
        if ( isset( $_GET['addon_status'] ) ) {
            $addon_id =  ! empty( $_GET['addon_id'] ) ? sanitize_text_field( $_GET['addon_id'] ) : '';
            if ( ! $addon_id ) {
                return;
            }
            // Instantiate the addon management class
            $addons_manager = new Threads_WP_Addons();
            $addons = $addons_manager->register_addons();
            $addon = $addons_manager->get_addon_by_id( $addon_id );
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . sprintf(__('Addon "%s" has been deactivated.', 'userwall-wp'), $addon->get_name() ) . '</p>';
            echo '</div>';
        }
    }

    public function process_addon_action() {
        // Check if the form was submitted
        if (isset($_POST['addon_action'])) {
            $addon_id = sanitize_text_field($_POST['addon_id']);
            $active_addons = get_option( $this->option_active_addons, []);
            $active_addons_ids = array_keys( $active_addons );
            $redirect_url = admin_url('admin.php?page=userwall-wp-addons');
            // Instantiate the addon management class
            $addons_manager = new Threads_WP_Addons();
            $addons_manager->register_addons();
            $addons = $addons_manager->get_addons();
            if ($_POST['addon_action'] === 'activate') {
                // Activate the addon
                if ( empty( $active_addons_ids ) || ! in_array($addon_id, $active_addons_ids)) {
                    $addon = ! empty( $addons[ $addon_id ] ) ? $addons[ $addon_id ] : array();
                    if ( $addon ) {
                        //$active_addons[][ $addon_id ] = $addon;
                        $class_name = get_class( $addon );
                        $reflectionClass = new ReflectionClass( $class_name );
                        $filePath = $reflectionClass->getFileName();

                        $addon_array = array();
                        $addon_array['id'] = $addon_id;
                        $addon_array['name'] = $addon->get_name();
                        $addon_array['description'] = $addon->get_description();
                        $addon_array['version'] = $addon->get_version();
                        $addon_array['active'] = $addon->is_active();
                        $addon_array['file'] = $filePath;
                        $addon_array['class'] = get_class( $addon );
                        $active_addons[$addon_id] = $addon_array;
                        update_option( $this->option_active_addons, $active_addons);
                        $addons_manager->activate_addon($addon_id);
                        do_action( 'threads_wp_after_addon_activated', $addon_id );
                        $redirect_url = add_query_arg( array( 'addon_status' => 'activated', 'addon_id' => $addon_id ), $redirect_url );
                    }
                }

                
            } elseif ($_POST['addon_action'] === 'deactivate') {
                // Deactivate the addon
                if ( in_array($addon_id, $active_addons_ids)) {
                    unset($active_addons[$addon_id]);
                    update_option( $this->option_active_addons, $active_addons);
                    // Deactivate addon by ID
                    $addons_manager->deactivate_addon( $addon_id );
                    $redirect_url = add_query_arg( array( 'addon_status' => 'deactivated', 'addon_id' => $addon_id ), $redirect_url );
                    do_action( 'threads_wp_after_addon_activated', $addon_id );
                }
            }
            wp_redirect( $redirect_url );
            exit;
        }
    }
}

// Initialize the admin class
Threads_WP_Admin::get_instance();