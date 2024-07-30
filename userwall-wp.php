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
 * Requires at least: 6.2
 * Requires PHP: 7.0
 *
 * @package UserWall_WP
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'USERWALL_WP_VERSION', '1.0.0' );
define( 'USERWALL_WP_PLUGIN_FILE', __FILE__ );
define( 'USERWALL_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'USERWALL_WP_PLUGIN_ADDONS_DIR', plugin_dir_path( __FILE__ ) . 'includes/addons/' );
define( 'USERWALL_WP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once USERWALL_WP_PLUGIN_DIR . 'activation.php';
require_once USERWALL_WP_PLUGIN_DIR . 'deactivation.php';

// Register the activation hook.
register_activation_hook( __FILE__, 'userwall_wp_activate' );

// Hook into plugin deactivation.
register_deactivation_hook( __FILE__, 'userwall_wp_deactivate' );


require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-post-core.php';

require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-post.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-profile.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-template.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/helper-functions.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/icons.php';

// Include the addon management class (UserWall_WP_Addons).
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-base-addon.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-addons.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-admin.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-blocks.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-ajax-manager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-post-manager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-template.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-shortcode.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-filemanager.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp-table-manager.php';

// Main file.
require_once USERWALL_WP_PLUGIN_DIR . 'includes/class-userwall-wp.php';

// Create an instance of the UserWall_WP_Addons class.
$userwall_wp_addons_manager = new UserWall_WP_Addons();
$userwall_wp_addons_manager->load_addons();
