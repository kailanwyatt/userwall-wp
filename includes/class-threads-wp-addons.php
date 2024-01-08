<?php
class Threads_WP_Addons {
    private $addons = array();

    private $active_addons;

    public function __construct() {
        $this->active_addons = get_option('threads_wp_active_addons', []);
    }

    public function register_addons($addons) {
        $addons = array();
        
        require_once( THREADS_WP_PLUGIN_ADDONS_DIR . 'polls/polls.php' );
        // Register internal addons
        $addons = $this->register_internal_addons($addons);

        // Register third-party addons via filter
        $addons = apply_filters('threads_wp_register_addons', $addons);

        $this->addons = $addons;
    }

    private function register_internal_addons($addons) {
        // Register your internal addons here
        // Add them to the $addons array
        // Example:
        /*
        $addons[] = array(
            'name' => 'Internal Addon',
            'description' => 'Description of internal addon',
            'version' => '1.0',
            'author' => 'Your Name',
            'file' => __DIR__ . '/internal-addon.php', // Adjust the path
        );
        */
        
        /* $addons[] = array(
            'name' => __( 'Polls', 'threads-wp' ),
            'description' => 'Description of internal addon',
            'version' => '1.0',
            'author' => 'ThreadsWP',
            'file' => THREADS_WP_PLUGIN_DIR . 'poll/polls.php',
        ); */
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
                $addon['active']
            );
            $addon_objects[] = $addon_object;
        }

        return $addon_objects;
    }

    public function load_addons($addons) {
        if ( empty( $addons ) ) {
            return;
        }
        // Load and activate addons
        foreach ($addons as $addon) {
            if (isset($addon['file']) && file_exists($addon['file'])) {
                include_once $addon['file'];
                // Add logic to activate the addon if needed
            }
        }
    }

    public function is_active($addon_id) {
        return in_array($addon_id, $this->active_addons);
    }

    public function activate_addon( $addon_id ) {
        $addon = $this->get_addon_by_id( $addon_id )->activate_addon();
    }

    public function deactivate_addon( $addon_id ) {
        $addon = $this->get_addon_by_id( $addon_id )->deactivate_addon();
    }
}

// Create an instance of the Threads_WP_Addons class
$addons_manager = new Threads_WP_Addons();

