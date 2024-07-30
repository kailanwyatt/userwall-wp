<?php
/**
 * UserWall_WP_Addons class.
 * This class handles the registration, activation, and deactivation of addons in UserWall_WP plugin.
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Addons class.
 */
class UserWall_WP_Addons {
	/**
	 * Addons list.
	 *
	 * @var array
	 */
	private $addons = array();

	/**
	 * Active Addons list.
	 *
	 * @var array
	 */
	private $active_addons;

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->active_addons = get_option( 'userwall_wp_active_addons', array() );
	}

	/**
	 * Registers addons.
	 *
	 * @param array $addons Addons array.
	 * @return void
	 */
	public function register_addons( $addons = array() ) {
		$addons = array();

		// Register internal addons via glob.
		$addons = $this->register_internal_addons( $addons );

		// Register third-party addons via filter hook.
		$addons = apply_filters( 'userwall_wp_register_addons', $addons );

		$this->addons = $addons;
	}

	/**
	 * Register the internal addons.
	 *
	 * @param array $addons Addons array.
	 * @return array $addons
	 */
	private function register_internal_addons( $addons = array() ) {

		$addons = array();

		// Define the folder path where your addon files are located (with trailing slash).
		$addon_dir = USERWALL_WP_PLUGIN_ADDONS_DIR;

		// Use glob to find PHP files with class names starting with 'UserWallWP_Addon_' in the specified folder.
		$addon_files = glob( $addon_dir . 'class-userwallwp-addon-*.php' );

		// Loop through the found files and include them.
		foreach ( $addon_files as $addon_file ) {
			// Include the addon file to get the class name.
			require_once $addon_file;

			// Extract the class name from the file path and instantiate it.
			$class_name = basename( $addon_file, '.php' );
			$class_name = ucfirst( str_replace( 'class-userwallwp-addon-', '', $class_name ) );
			$class_name = 'UserWallWP_Addon_' . $class_name;

			// Check if the class exists and if it's an instance of UserWall_WP_Base_Addon class.
			if ( class_exists( $class_name ) && is_subclass_of( $class_name, 'UserWall_WP_Base_Addon' ) ) {
				$addon_id = strtolower( str_replace( 'UserWallWP_Addon_', '', $class_name ) );
				// Instantiate the class and add it to the addons array using the addon ID as the key.
				$addons[ $addon_id ] = new $class_name();
			}
		}
		return $addons;
	}

	/**
	 * Returns the addons available.
	 *
	 * @return array
	 */
	public function get_addons() {
		return $this->addons;
	}

	/**
	 * Get addon by ID.
	 *
	 * @param string $id Addon ID.
	 * @return mixed
	 */
	public function get_addon_by_id( $id = '' ) {
		if ( ! $id ) {
			return;
		}

		if ( isset( $this->addons[ $id ] ) ) {
			return $this->addons[ $id ];
		}
	}

	/**
	 * Get installed addons.
	 *
	 * @return array
	 */
	public function get_installed_addons() {
		// Get the list of installed addons from your storage mechanism.
		$addons = get_option( 'userwall_wp_installed_addons', array() ); // You can use options to store addon data.

		// Create an array to hold the addon objects.
		$addon_objects = array();

		// Iterate through the installed addons and create addon objects.
		foreach ( $addons as $addon ) {
			$addon_object                  = new UserWall_WP_Addon(
				$addon['id'],
				$addon['name'],
				$addon['description'],
				$addon['version'],
				$addon['active'],
				$addon['file']
			);
			$addon_objects[ $addon['id'] ] = $addon_object;
		}

		return $addon_objects;
	}

	/**
	 * Load addons.
	 *
	 * @return void
	 */
	public function load_addons() {
		if ( ! empty( $this->active_addons ) ) {
			// Load and activate addons.
			foreach ( $this->active_addons as $addon ) {
				if ( isset( $addon['file'] ) && file_exists( $addon['file'] ) && ! class_exists( $addon['class'] ) ) {
					include_once $addon['file'];
					$class_name  = $addon['class'];
					$addon_class = new $class_name();
				}
			}
		}
	}

	/**
	 * Check if addon is active.
	 *
	 * @param string $addon_id Active ID.
	 * @return boolean
	 */
	public function is_active( $addon_id = '' ) {
		return in_array( $addon_id, array_keys( $this->active_addons ), true );
	}

	/**
	 * Activate addon.
	 *
	 * @param string $addon_id Addon ID.
	 * @return void
	 */
	public function activate_addon( $addon_id = '' ) {
		$addon = $this->get_addon_by_id( $addon_id )->activate_addon();
	}

	/**
	 * Deactivate addon.
	 *
	 * @param string $addon_id Addon ID.
	 * @return void
	 */
	public function deactivate_addon( $addon_id = '' ) {
		$addon = $this->get_addon_by_id( $addon_id )->deactivate_addon();
	}
}
