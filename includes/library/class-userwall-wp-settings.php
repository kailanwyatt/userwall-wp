<?php
class WP_Custom_Settings_API {
	private $tabs    = array();
	private $subtabs = array();
	private $options = array();

	public function __construct() {
		$this->tabs = array(
			'general'  => __( 'General', 'userwall-wp' ),
			'advanced' => __( 'Advanced', 'userwall-wp' ),
		);

		$this->subtabs = array(
			'general' => array(
				'main'      => __( 'Main', 'userwall-wp' ),
				'secondary' => __( 'Secondary', 'userwall-wp' ),
			),
		);

		$this->options = get_option( 'userwall_wp' );
	}

	public function admin_init() {
		register_setting( 'userwall_wp', 'userwall_wp' );
		$this->init_general_main_settings();
		$this->init_general_secondary_settings();
		$this->init_advanced_settings();
	}

	private function init_general_main_settings() {
		add_settings_section(
			'userwall_settings_general',
			null,
			false,
			'userwall_settings_general'
		);

		$this->get_field(
			array(
				'type'  => 'pages_dropdown',
				'name'  => 'user_page',
				'label' => __( 'User Page', 'userwall_wp' ),
			)
		);

		$this->get_field(
			array(
				'type'  => 'pages_dropdown',
				'name'  => 'single_post_page',
				'label' => __( 'Single Post Page', 'userwall_wp' ),
			)
		);

		$this->get_field(
			array(
				'type'  => 'pages_dropdown',
				'name'  => 'post_index_page',
				'label' => __( 'Post Index Page', 'userwall_wp' ),
			)
		);

		$this->get_field(
			array(
				'type'    => 'select',
				'name'    => 'post_types',
				'label'   => __( 'Post Types', 'userwall_wp' ),
				'options' => array(
					'post'               => 'Post',
					'page'               => 'Page',
					'custom_post_type_1' => 'Custom Post Type 1',
					'custom_post_type_2' => 'Custom Post Type 2',
					// Add more post types as needed
				),
			)
		);

		// Character Limit Field
		$this->get_field(
			array(
				'type'  => 'text',
				'name'  => 'character_limit',
				'label' => __( 'Character Limit', 'userwall_wp' ),
			)
		);

		// Editor Settings Fields
		$this->get_field(
			array(
				'type'    => 'checkbox',
				'name'    => 'editor_options',
				'label'   => __( 'Editor Options', 'userwall_wp' ),
				'options' => array(
					'bold'       => __( 'Bold', 'userwall-wp' ),
					'italic'     => __( 'Italic', 'userwall-wp' ),
					'underline'  => __( 'Underline', 'userwall-wp' ),
					'strike'     => __( 'Strike', 'userwall-wp' ),
					'list'       => __( 'List', 'userwall-wp' ),
					'header'     => __( 'Headers', 'userwall-wp' ),
					'blockquote' => __( 'Blockquote', 'userwall-wp' ),
					'code-block' => __( 'Code Block', 'userwall-wp' ),
				),
			)
		);
	}

	/**
	 * Generate a settings field based on field type.
	 *
	 * @param array $args {
	 *     An array of field configuration options.
	 *
	 *     @type string   $type         The type of field (e.g., 'text', 'textarea', 'select', 'checkbox', 'radio').
	 *     @type string   $name         The name of the field.
	 *     @type string   $value        The current value of the field (optional).
	 *     @type string   $label        The label for the field.
	 *     @type array    $options      Options for select and radio fields (optional).
	 *     @type string   $description  Description or additional information about the field (optional).
	 *     @type string   $section      The section where the field belongs.
	 * }
	 */
	private function get_field( $args = array() ) {
		$defaults = array(
			'type'        => 'text',
			'name'        => '',
			'value'       => $this->options[ $args['name'] ] ?? '',
			'label'       => '',
			'options'     => array(),
			'description' => '',
			'section'     => 'userwall_settings_general',
		);

		$args  = wp_parse_args( $args, $defaults );
		$input = '';

		switch ( $args['type'] ) {
			case 'text':
				$input = '<input type="text" name="userwall_wp[' . esc_attr( $args['name'] ) . ']" value="' . esc_attr( $args['value'] ?? '' ) . '"/>';
				break;
			case 'textarea':
				$input = '<textarea name="userwall_wp[' . esc_attr( $args['name'] ) . ']">' . esc_textarea( $args['value'] ?? '' ) . '</textarea>';
				break;
			case 'select':
				$input = '<select name="userwall_wp[' . esc_attr( $args['name'] ) . ']">';
				foreach ( $args['options'] as $option_value => $option_label ) {
					$selected = selected( $args['value'], $option_value, false );
					$input   .= '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . esc_html( $option_label ) . '</option>';
				}
				$input .= '</select>';
				break;
			case 'checkbox':
				if ( ! empty( $args['options'] ) ) {
					$checked = '';
					foreach ( $args['options'] as $option_key => $option ) {
						if ( ! empty( $args['value'] ) && is_array( $args['value'] ) ) {
							$checked = checked( in_array( $option_key, array_keys( $args['value'] ) ), true, false );
						}
						$input .= '<div><label>';
						$input .= '<input type="checkbox" name="userwall_wp[' . esc_attr( $args['name'] ) . '][' . $option_key . ']" ' . $checked . '/>';
						$input .= esc_html( $option ) . '</label></div>';
					}
				} else {
					$checked = checked( $args['value'], 'on', false );
					$input   = '<input type="checkbox" name="userwall_wp[' . esc_attr( $args['name'] ) . ']" ' . $checked . '/>';
				}
				break;
			case 'radio':
				$input = '';
				foreach ( $args['options'] as $option_value => $option_label ) {
					$checked = checked( $args['value'], $option_value, false );
					$input  .= '<input type="radio" name="userwall_wp[' . esc_attr( $args['name'] ) . ']" value="' . esc_attr( $option_value ) . '" ' . $checked . '/> ' . esc_html( $option_label ) . '<br>';
				}
				break;
			case 'pages_dropdown':
				$input = '<select name="userwall_wp[' . esc_attr( $args['name'] ) . ']">';

				// Get the list of pages
				$pages    = get_pages();
				$selected = selected( $args['value'], '', false );
				$input   .= '<option value="" ' . $selected . '>' . esc_html__( '-Page-', 'userwall-wp' ) . '</option>';
				if ( ! empty( $pages ) ) {
					foreach ( $pages as $page ) {
						$selected = selected( $args['value'], $page->ID, false );
						$input   .= '<option value="' . esc_attr( $page->ID ) . '" ' . $selected . '>' . esc_html( $page->post_title ) . '</option>';
					}
				}
				$input .= '</select>';
				break;
		}

		add_settings_field(
			$args['name'],
			$args['label'],
			function () use ( $input ) {
				echo $input;
			},
			$args['section'],
			$args['section']
		);
	}

	public function example_field_callback() {
		$options = get_option( 'userwall_wp' );
		echo '<input type="text" name="userwall_wp[example_field]" value="' . esc_attr( $options['example_field'] ?? '' ) . '"/>';
	}

	private function init_general_secondary_settings() {
		add_settings_section(
			'wp_custom_general_secondary',
			__( 'Secondary Settings', 'userwall-wp' ),
			function () {
				echo '<p>Secondary settings section description.</p>'; },
			'wp_custom_general_secondary'
		);

		// Additional fields for the Secondary Subtab can be added here.
	}

	private function init_advanced_settings() {
		add_settings_section(
			'wp_custom_advanced',
			__( 'Advanced Settings', 'wp_custom_admin_settings_panel' ),
			function () {
				echo '<p>Advanced settings section description.</p>'; },
			'wp_custom_advanced'
		);

		// Additional fields for the Advanced Tab can be added here.
	}

	public function show_navigation() {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->tabs as $tab => $name ) {
			$class = ( $tab == $this->get_current_tab() ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=userwall-wp-settings&tab=$tab'>$name</a>";
		}
		echo '</h2>';

		if ( isset( $this->subtabs[ $this->get_current_tab() ] ) ) {
			echo '<ul class="subsubsub">';
			foreach ( $this->subtabs[ $this->get_current_tab() ] as $subtab => $name ) {
				$class = ( $subtab == $this->get_current_subtab() ) ? ' current' : '';
				echo "<li><a class='$class' href='?page=userwall-wp-settings&tab={$this->get_current_tab()}&subtab=$subtab'>$name</a> | </li>";
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
		settings_fields( 'userwall_wp' );

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
				do_settings_sections( 'userwall_settings_general' );
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
