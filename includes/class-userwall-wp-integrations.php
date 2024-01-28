<?php
require_once USERWALL_WP_PLUGIN_DIR . 'includes/integrations/ultimate-member.php';
require_once USERWALL_WP_PLUGIN_DIR . 'includes/integrations/buddypress.php';
function userwall_wp_loaded() {
	if ( class_exists( 'UM' ) ) {
		//require_once( USERWALL_WP_PLUGIN_DIR . 'includes/integrations/ultimate-member.php' );
	}
}
add_action( 'wp', 'userwall_wp_loaded' );
