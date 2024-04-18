<?php
/**
 * The main plugin file.
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This is the UserWall_WP class.
 */
class UserWall_WP {
	/**
	 * The Singleton instance.
	 *
	 * @var UserWall_WP
	 */
	private static $instance;

	/**
	 * Constructor (private to enforce Singleton pattern).
	 */
	private function __construct() {
		// Enqueue assets on the front end.
		$template = new UserWall_Template();
		add_action( 'wp_enqueue_scripts', array( $template, 'enqueue_assets' ) );
		add_filter( 'wp_inline_script_attributes', array( $template, 'add_script_attrs_to_wall_tmpl' ), 10, 1 );
		add_filter( 'script_loader_tag', array( $template, 'add_module_attribute' ), 10, 2 );
		// Define the path to the addons folder.
		$addons_folder = trailingslashit( USERWALL_WP_PLUGIN_DIR ) . 'addons';

		// Initialize the plugin.
		$this->init( $addons_folder );
	}

	/**
	 * Get the Singleton instance.
	 *
	 * @return UserWall_WP Singleton instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @param string $addons_folder Path to the addons folder.
	 */
	private function init( $addons_folder ) {
		// Autoload classes for addons.
		$this->autoload_addon_classes( $addons_folder );
	}

	/**
	 * Autoload classes for addons.
	 *
	 * @param string $addons_folder Path to the addons folder.
	 */
	private function autoload_addon_classes( $addons_folder ) {
		if ( is_dir( $addons_folder ) ) {
			$addon_classes = glob( $addons_folder . '/*/*.php' );

			foreach ( $addon_classes as $class_file ) {
				require_once $class_file;
			}
		}
	}
}

// Initialize the Singleton instance.
UserWall_WP::get_instance();
