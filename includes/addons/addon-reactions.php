<?php
class UserWallWP_Addon_Reactions extends Threads_WP_Base_Addon {
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
        return __( 'ThreadWP', 'userwall-wp' );;
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

function register_threads_wp_addons($addons) {
    // Instantiate your addon class and add it to the addons list
    $threads_wp_poll_addon = new UserWallWP_Poll_Addon();
    $addons['polls'] = $threads_wp_poll_addon;

    // You can add more addons in a similar manner if needed
    // $another_addon = new Another_Addon_Class();
    // $addons[] = $another_addon;

    return $addons;
}

// Hook the function to the filter 'threads_wp_register_addons'
//add_filter('threads_wp_register_addons', 'register_threads_wp_addons');