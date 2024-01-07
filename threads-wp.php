<?php
/**
 * Plugin Name: Threads WP
 * Description: Your plugin description here.
 * Version: 1.0.0
 * Author: Your Name
 */

// Define plugin constants
define('THREADS_WP_PLUGIN_FILE', __FILE__);
define('THREADS_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('THREADS_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once( THREADS_WP_PLUGIN_DIR . 'activation.php');
require_once( THREADS_WP_PLUGIN_DIR . 'deactivation.php');

// Register the activation hook
register_activation_hook(__FILE__, 'threads_wp_activate');


// Hook into plugin deactivation
register_deactivation_hook(__FILE__, 'threads_wp_deactivate');

require_once( THREADS_WP_PLUGIN_DIR . 'includes/class-threads-wp-admin.php' );
require_once( THREADS_WP_PLUGIN_DIR . 'includes/class-threads-wp-blocks.php' );
// Autoloader for include files (excluding addons)
//spl_autoload_register('threads_wp_autoload');

function threads_wp_autoload($class_name) {
    
    // If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'Threads_WP_' ) ) {
		return;
	}
    $class_file = 'class-' . str_replace('_', '-', strtolower($class_name)) . '.php';
    
    $class_path = trailingslashit(plugin_dir_path(__FILE__)) . 'includes/' . $class_file;

    if (file_exists($class_path)) {
        require_once($class_path);
    }
}

if (!class_exists('Threads_WP')) {
    class Threads_WP {
        private static $instance;

        /**
         * Constructor (private to enforce Singleton pattern).
         */
        private function __construct() {
            // Define the path to the addons folder
            $addons_folder = trailingslashit(plugin_dir_path(__FILE__)) . 'addons';

            // Initialize the plugin
            $this->init($addons_folder);
        }

        /**
         * Get the Singleton instance.
         *
         * @return Threads_WP Singleton instance.
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
    Threads_WP::get_instance();
}
