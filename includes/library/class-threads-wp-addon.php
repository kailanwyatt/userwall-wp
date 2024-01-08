<?php
class Threads_WP_Base_Addon {
    protected $addon_name = '';
    protected $addon_description = '';
    protected $addon_author = '';
    protected $addon_version = '';
    protected $addon_id = '';
    protected $is_active = false;

    public function __construct() {
        $this->addon_name = $this->get_name();
        $this->addon_description = $this->get_description();
        $this->addon_author = $this->get_author();
        $this->addon_version = $this->get_version();
        $this->addon_id      = $this->get_id();
    }

    // Abstract methods to be implemented by subclasses
    public function get_id() {
        $this->addon_id;
    }

    public function get_name() {
        return $this->addon_name;
    }

    public function get_description() {
        return $this->addon_name;
    }

    public function get_author() {
        return $this->addon_author;
    }

    public function get_version() {
        return $this->addon_version;
    }

    public function activate_addon() {
        // Add addon-specific activation logic here
    }

    public function deactivate_addon() {
        // Add addon-specific deactivation logic here
    }

    /**
     * Check if the addon is currently active.
     *
     * @return bool Whether the addon is active or not.
     */
    public function is_active() {
        return $this->is_active;
    }
}
