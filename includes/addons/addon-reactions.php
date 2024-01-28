<?php
class UserWallWP_Addon_Reactions extends UserWall_WP_Base_Addon {
    public function get_id() {
        return 'reactions';
    }

    public function get_name() {
        return __( 'Reactions', 'userwall-wp' );
    }

    public function get_description() {
        return __( 'Adds User Reactions to posts and comments', 'userwall-wp' );
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

    public function is_ready() {
        return false;
    }
}