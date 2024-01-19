<?php
/**
 * Plugin Name: UserWall WP
 * Description: A versatile plugin that combines microblogging and forum functionality within WordPress, enhancing user engagement and interaction.
 * Version: 1.0.0
 * Author: UserWall WP
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: userwall-wp
 * Domain Path: /languages
 */

// Define plugin constants
define('USERWALL_WP_PLUGIN_FILE', __FILE__);
define('USERWALL_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('USERWALL_WP_PLUGIN_ADDONS_DIR', plugin_dir_path(__FILE__) . 'includes/addons/');
define('USERWALL_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once( USERWALL_WP_PLUGIN_DIR . 'activation.php');
require_once( USERWALL_WP_PLUGIN_DIR . 'deactivation.php');

// Register the activation hook
register_activation_hook(__FILE__, 'userwall_wp_activate');


// Hook into plugin deactivation
register_deactivation_hook(__FILE__, 'userwall_wp_deactivate');

// Include the addon management class (UserWall_WP_Addons)
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-addon.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-post.php' );
include_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-template.php' );
include_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-addons.php';
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-admin.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-blocks.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-ajax-manager.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-post-manager.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-template.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-shortcode.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-filemanager.php' );
require_once( USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-table-manager.php' );

// Create an instance of the UserWall_WP_Addons class
$addons_manager = new UserWall_WP_Addons();
$addons_manager->load_addons();

require_once( USERWALL_WP_PLUGIN_DIR . 'includes/integrations/ultimate-member.php' );
function userwall_wp_loaded() {
    if ( class_exists('UM') ) {
        //require_once( USERWALL_WP_PLUGIN_DIR . 'includes/integrations/ultimate-member.php' );
    }
}
add_action( 'wp', 'userwall_wp_loaded' );
// Autoloader for include files (excluding addons)
//spl_autoload_register('userwall_wp_autoload');

function userwall_wp_autoload($class_name) {
    
    // If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'UserWall_WP_' ) ) {
		return;
	}
    $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
    
    $class_path = trailingslashit(plugin_dir_path(__FILE__)) . 'includes/' . $class_file;

    if (file_exists($class_path)) {
        require_once($class_path);
    }
}

if (!class_exists('UserWall_WP')) {
    class UserWall_WP {
        private static $instance;

        /**
         * Constructor (private to enforce Singleton pattern).
         */
        private function __construct() {
            // Enqueue assets on the front end
            $template = new UserWall_Template();
            add_action( 'wp_enqueue_scripts',  array($template, 'enqueue_assets' ) );

            // Define the path to the addons folder
            $addons_folder = trailingslashit(plugin_dir_path(__FILE__)) . 'addons';

            // Initialize the plugin
            $this->init($addons_folder);
        }

        /**
         * Get the Singleton instance.
         *
         * @return UserWall_WP Singleton instance.
         */
        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Initialize the plugin.
         *
         * @param string $addons_folder Path to the addons folder.
         */
        private function init($addons_folder) {
            // Autoload classes for addons
            $this->autoload_addon_classes($addons_folder);

            // Add any other initialization code here

            // Add hooks and filters here
        }

        /**
         * Autoload classes for addons.
         *
         * @param string $addons_folder Path to the addons folder.
         */
        private function autoload_addon_classes($addons_folder) {
            if (is_dir($addons_folder)) {
                $addon_classes = glob($addons_folder . '/*/*.php');

                foreach ($addon_classes as $class_file) {
                    require_once($class_file);
                }
            }
        }
    }

    // Initialize the Singleton instance
    UserWall_WP::get_instance();
}

function add_type_attribute($tag, $handle, $src) {
    // if not your script, do nothing and return original $tag
    if ( 'userwall-wp-js' !== $handle ) {
        return $tag;
    }
    // change the script tag by adding type="module" and return it.
    $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}
add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);

function fetch_usernames_callback() {
    // Check for the 'term' in the AJAX request
    if (isset($_GET['term'])) {
        $search_term = sanitize_text_field( trim( str_replace( '@', '', $_GET['term'] ) ) );
        
        // Query for users
        $user_query = new WP_User_Query(array(
            'search'         => '*' . esc_attr($search_term) . '*',
            'search_columns' => array('user_login', 'user_nicename'),
            'number'         => 10, // Limit the number of results
        ));
        
        $users = $user_query->get_results();
        
        $usernames = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $usernames[] = $user->user_login; // or user_nicename, depending on your preference
            }
        }

        wp_send_json_success($usernames);
    }

    wp_send_json_error('No term found');
}
add_action('wp_ajax_fetch_usernames', 'fetch_usernames_callback'); // For logged-in users
add_action('wp_ajax_nopriv_fetch_usernames', 'fetch_usernames_callback'); // For logged-out users
