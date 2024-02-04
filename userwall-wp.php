<?php
/**
 * Plugin Name: UserWall WP
 * Description: A versatile plugin that combines microblogging and forum functionality within WordPress, enhancing user engagement and interaction.
 * Version: 0.0.1
 * Author: UserWall WP
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: userwall-wp
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.0
 */

// Define plugin constants
define( 'USERWALL_WP_PLUGIN_FILE', __FILE__ );
define( 'USERWALL_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'USERWALL_WP_PLUGIN_ADDONS_DIR', plugin_dir_path( __FILE__ ) . 'includes/addons/' );
define( 'USERWALL_WP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once USERWALL_WP_PLUGIN_DIR . 'activation.php';
require_once USERWALL_WP_PLUGIN_DIR . 'deactivation.php';

// Register the activation hook
register_activation_hook( __FILE__, 'userwall_wp_activate' );

// Hook into plugin deactivation
register_deactivation_hook( __FILE__, 'userwall_wp_deactivate' );

// Include the addon management class (UserWall_WP_Addons)
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-addon.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-post.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-profile.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-template.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-core.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/helper-functions.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/icons.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-addons.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-admin.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-blocks.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-ajax-manager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-post-manager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-template.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-shortcode.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-filemanager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-table-manager.php';

// Main file.
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp.php';

// Create an instance of the UserWall_WP_Addons class
$addons_manager = new UserWall_WP_Addons();
$addons_manager->load_addons();

function user_wall_wp_add_type_attribute( $tag, $handle, $src ) {
	// if not your script, do nothing and return original $tag
	if ( 'userwall-wp-js' !== $handle ) {
		return $tag;
	}
	// change the script tag by adding type="module" and return it.
	$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
	return $tag;
}
add_filter( 'script_loader_tag', 'user_wall_wp_add_type_attribute', 10, 3 );
