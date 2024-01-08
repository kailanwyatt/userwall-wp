<?php
class WP_Custom_Settings_API {
    private $tabs = array();
    private $subtabs = array();

    public function __construct() {
        $this->tabs = array(
            'general' => __( 'General', 'wp-custom-admin-settings-panel' ),
            'advanced' => __( 'Advanced', 'wp-custom-admin-settings-panel' )
        );

        $this->subtabs = array(
            'general' => array(
                'main' => __( 'Main', 'wp-custom-admin-settings-panel' ),
                'secondary' => __( 'Secondary', 'wp-custom-admin-settings-panel' )
            ),
            // Advanced tab doesn't have subtabs for this example.
        );
    }

    public function admin_init() {
        register_setting( 'threads_wp', 'threads_wp' );
        $this->init_general_main_settings();
        $this->init_general_secondary_settings();
        $this->init_advanced_settings();
    }

    private function init_general_main_settings() {
        add_settings_section(
            'wp_custom_general_main',
            __( 'Main Settings', 'wp-custom-admin-settings-panel' ),
            function() { echo '<p>Main settings section description.</p>'; },
            'wp_custom_general_main'
        );

        add_settings_field(
            'example_field',
            __( 'Example Field', 'wp-custom-admin-settings-panel' ),
            array( $this, 'example_field_callback' ),
            'wp_custom_general_main',
            'wp_custom_general_main'
        );
    }

    public function example_field_callback() {
        $options = get_option( 'threads_wp' );
        echo '<input type="text" name="threads_wp[example_field]" value="' . esc_attr( $options['example_field'] ?? '' ) . '"/>';
    }

    private function init_general_secondary_settings() {
        add_settings_section(
            'wp_custom_general_secondary',
            __( 'Secondary Settings', 'wp-custom-admin-settings-panel' ),
            function() { echo '<p>Secondary settings section description.</p>'; },
            'wp_custom_general_secondary'
        );

        // Additional fields for the Secondary Subtab can be added here.
    }

    private function init_advanced_settings() {
        add_settings_section(
            'wp_custom_advanced',
            __( 'Advanced Settings', 'wp_custom_admin_settings_panel' ),
            function() { echo '<p>Advanced settings section description.</p>'; },
            'wp_custom_advanced'
        );

        // Additional fields for the Advanced Tab can be added here.
    }

    public function show_navigation() {
        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->tabs as $tab => $name ) {
            $class = ( $tab == $this->get_current_tab() ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=threads-wp-settings&tab=$tab'>$name</a>";
        }
        echo '</h2>';

        if ( isset( $this->subtabs[ $this->get_current_tab() ] ) ) {
            echo '<ul class="subsubsub">';
            foreach ( $this->subtabs[ $this->get_current_tab() ] as $subtab => $name ) {
                $class = ( $subtab == $this->get_current_subtab() ) ? ' current' : '';
                echo "<li><a class='$class' href='?page=threads-wp-settings&tab={$this->get_current_tab()}&subtab=$subtab'>$name</a> | </li>";
            }
            echo '</ul>';
        }
    }

    private function get_current_tab() {
        return isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    }

    private function get_current_subtab() {
        return isset( $_GET['subtab'] ) ? $_GET['subtab'] : 'main';
    }

    public function show_forms() {
        echo '<form method="post" action="options.php">';
        settings_fields( 'threads_wp' );

        $current_tab = $this->get_current_tab();
        switch ( $current_tab ) {
            case 'general':
                $this->show_general_settings();
                break;
            case 'advanced':
                $this->show_advanced_settings();
                break;
        }

        submit_button();
        echo '</form>';
    }

    private function show_general_settings() {
        $current_subtab = $this->get_current_subtab();
        switch ( $current_subtab ) {
            case 'main':
                do_settings_sections( 'wp_custom_general_main' );
                break;
            case 'secondary':
                do_settings_sections( 'wp_custom_general_secondary' );
                break;
        }
    }

    private function show_advanced_settings() {
        do_settings_sections( 'wp_custom_advanced' );
    }
}