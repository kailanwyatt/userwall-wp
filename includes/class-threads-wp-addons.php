<?php
class Threads_WP_Addons {
    private $addons = array();

    private $active_addons;

    public function __construct() {
        $this->active_addons = get_option('threads_wp_active_addons', []);
    }

    public function register_addons($addons = array() ) {
        $addons = array();
        
        
        // Register internal addons
        $addons = $this->register_internal_addons($addons);

        // Register third-party addons via filter
        $addons = apply_filters('threads_wp_register_addons', $addons);

        $this->addons = $addons;
    }

    private function register_internal_addons($addons) {

        $addons = array();

        // Define the folder path where your addon files are located
        $addon_dir = THREADS_WP_PLUGIN_ADDONS_DIR;

        // Use glob to find PHP files with class names starting with 'ThreadsWP_Addon_'
        $addon_files = glob($addon_dir . 'addon-*.php');

        // Loop through the found files
        foreach ($addon_files as $addon_file) {
            // Include the addon file
            require_once($addon_file);

            // Extract the class name from the file path
            $class_name = basename($addon_file, '.php');
            $class_name = ucfirst( str_replace( 'addon-', '', $class_name ) );
            $class_name = 'ThreadsWP_Addon_' . $class_name;
            // Check if the class exists and if it's an instance of Threads_WP_Base_Addon
            if (class_exists($class_name) && is_subclass_of($class_name, 'Threads_WP_Base_Addon')) {
                $addon_id = strtolower(str_replace('ThreadsWP_Addon_', '', $class_name));
                // Instantiate the class and add it to the addons array
                $addons[ $addon_id ] = new $class_name();
            }
        }
        return $addons;
    }

    public function get_addons() {
        return $this->addons;
    }

    public function get_addon_by_id( $id ) {
        if ( ! $id ) {
            return;
        }

        if ( isset( $this->addons[ $id ] ) ) {
            return $this->addons[ $id ];
        }
    }

    public function get_installed_addons() {
        // Get the list of installed addons from your storage mechanism
        $addons = get_option('threads_wp_installed_addons', array()); // You can use options to store addon data

        // Create an array to hold the addon objects
        $addon_objects = array();

        // Iterate through the installed addons and create addon objects
        foreach ($addons as $addon) {
            $addon_object = new Threads_WP_Addon(
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

    public function load_addons() {
        if ( ! empty( $this->active_addons ) ) {
            // Load and activate addons
            foreach ( $this->active_addons as $addon) {
                
                if (isset($addon['file']) && file_exists($addon['file']) && ! class_exists( $addon['class'] ) ) {
                    
                    include_once( $addon['file'] );
                    $class_name = $addon['class'];
                    $addon_class = new $class_name();
                }
            }
        } 
    }

    public function is_active($addon_id) {
        return in_array($addon_id, array_keys( $this->active_addons ) );
    }

    public function activate_addon( $addon_id ) {
        $addon = $this->get_addon_by_id( $addon_id )->activate_addon();
    }

    public function deactivate_addon( $addon_id ) {
        $addon = $this->get_addon_by_id( $addon_id )->deactivate_addon();
    }
}