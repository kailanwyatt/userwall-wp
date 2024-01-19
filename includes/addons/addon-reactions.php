<?php
class UserWallWP_Addon_Reactions extends UserWall_WP_Base_Addon {
    public function get_id() {
        return 'reactions';
    }

    public function get_name() {
        return __( 'Reactions', 'userwall-wp' );
    }

    public function get_description() {
        return __( 'Reactions', 'userwall-wp' );
    }

    public function get_author() {
        return __( 'UserWallWP', 'userwall-wp' );;
    }

    public function get_version() {
        return '1.0';
    }

    public function activate_addon() {
        // Add activation logic specific to this addon (e.g., create tables)
    }

    public function deactivate_addon() {
        // Add deactivation logic specific to this addon (e.g., cleanup)
    }
}

function register_userwall_wp_addons($addons) {
    // Instantiate your addon class and add it to the addons list
    $userwall_wp_poll_addon = new UserWallWP_Poll_Addon();
    $addons['polls'] = $userwall_wp_poll_addon;

    // You can add more addons in a similar manner if needed
    // $another_addon = new Another_Addon_Class();
    // $addons[] = $another_addon;

    return $addons;
}

// Hook the function to the filter 'userwall_wp_register_addons'
//add_filter('userwall_wp_register_addons', 'register_userwall_wp_addons');