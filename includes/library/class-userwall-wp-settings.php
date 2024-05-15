<?php
/**
 * UserWall_WP_Settings class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Settings class
 */
class UserWall_WP_Settings {
	/**
	 * Tabs array
	 *
	 * @var array
	 */
	private $tabs = array();
	/**
	 * Subtabs array
	 *
	 * @var array
	 */
	private $subtabs = array();
	/**
	 * Options array
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->tabs = array(
			'general' => __( 'General', 'userwall-wp' ),
		);

		$this->subtabs = array(
			'general' => array(
				'main' => __( 'Main', 'userwall-wp' ),
			),
		);

		$this->options = get_option( 'userwall_wp' );
	}

	/**
	 * Initialize the settings
	 */
	public function admin_init() {
		register_setting( 'userwall_wp', 'userwall_wp' );
		$this->init_general_main_settings();
	}

	/**
	 * Initialize the general main settings
	 */
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
				'label' => __( 'User Page', 'userwall-wp' ),
			)
		);

		$this->get_field(
			array(
				'type'  => 'pages_dropdown',
				'name'  => 'single_post_page',
				'label' => __( 'Single Post Page', 'userwall-wp' ),
			)
		);

		// Character Limit Field.
		$this->get_field(
			array(
				'type'  => 'text',
				'name'  => 'character_limit',
				'label' => __( 'Character Limit', 'userwall-wp' ),
			)
		);

		$this->get_field(
			array(
				'type'  => 'checkbox',
				'name'  => 'allow_titles',
				'label' => __( 'Allow Titles on Posts', 'userwall-wp' ),
			)
		);

		$this->get_field(
			array(
				'type'  => 'checkbox',
				'name'  => 'open_posts',
				'label' => __( 'Open Posts Page', 'userwall-wp' ),
			)
		);

		// Editor Settings Fields.
		$this->get_field(
			array(
				'type'    => 'checkbox',
				'name'    => 'editor_options',
				'label'   => __( 'Editor Options', 'userwall-wp' ),
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
					$input   .= '<option value="' . esc_attr( $option_value ) . '" ' . esc_html( $selected ) . '>' . esc_html( $option_label ) . '</option>';
				}
				$input .= '</select>';
				break;
			case 'checkbox':
				if ( ! empty( $args['options'] ) ) {
					$checked = '';
					foreach ( $args['options'] as $option_key => $option ) {
						if ( ! empty( $args['value'] ) && is_array( $args['value'] ) ) {
							$checked = checked( in_array( $option_key, array_keys( $args['value'] ), true ), true, false );
						}
						$input .= '<div><label>';
						$input .= '<input type="checkbox" name="userwall_wp[' . esc_attr( $args['name'] ) . '][' . esc_attr( $option_key ) . ']" ' . esc_html( $checked ) . '/>';
						$input .= esc_html( $option ) . '</label></div>';
					}
				} else {
					$checked = checked( $args['value'], 'on', false );
					$input   = '<input type="checkbox" name="userwall_wp[' . esc_attr( $args['name'] ) . ']" ' . esc_html( $checked ) . '/>';
				}
				break;
			case 'radio':
				$input = '';
				foreach ( $args['options'] as $option_value => $option_label ) {
					$checked = checked( $args['value'], $option_value, false );
					$input  .= '<input type="radio" name="userwall_wp[' . esc_attr( $args['name'] ) . ']" value="' . esc_attr( $option_value ) . '" ' . esc_html( $checked ) . '/> ' . esc_html( $option_label ) . '<br>';
				}
				break;
			case 'pages_dropdown':
				$input = '<select name="userwall_wp[' . esc_attr( $args['name'] ) . ']">';

				// Get the list of pages.
				$pages    = get_pages();
				$selected = selected( $args['value'], '', false );
				$input   .= '<option value="" ' . esc_html( $selected ) . '>' . esc_html__( '-Page-', 'userwall-wp' ) . '</option>';
				if ( ! empty( $pages ) ) {
					foreach ( $pages as $page ) {
						$selected = selected( $args['value'], $page->ID, false );
						$input   .= '<option value="' . esc_attr( $page->ID ) . '" ' . esc_html( $selected ) . '>' . esc_html( $page->post_title ) . '</option>';
					}
				}
				$input .= '</select>';
				break;
		}

		add_settings_field(
			$args['name'],
			$args['label'],
			function () use ( $input ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wp_kses(
					$input,
					apply_filters(
						'userwall_wp_allowed_tags',
						array(
							'div'      => array(
								'class'  => true,
								'id'     => true,
								'style'  => true,
								'title'  => true,
								'role'   => true,
								'aria-*' => true,
								'data-*' => true,
								'align'  => true,
							),
							'label'    => array(
								'for'    => true,
								'class'  => true,
								'id'     => true,
								'style'  => true,
								'title'  => true,
								'role'   => true,
								'aria-*' => true,
								'data-*' => true,
								'align'  => true,
							),
							'input'    => array(
								'type'    => true,
								'name'    => true,
								'value'   => true,
								'checked' => true,
							),
							'textarea' => array(
								'name' => true,
								'rows' => true,
								'cols' => true,
							),
							'select'   => array(
								'name'     => true,
								'size'     => true,
								'multiple' => true,
								'disabled' => true,
								'class'    => true,
								'id'       => true,
								'style'    => true,
								'title'    => true,
								'role'     => true,
								'aria-*'   => true,
								'data-*'   => true,
								'align'    => true,
							),
							'option'   => array(
								'value'    => true,
								'selected' => true,
							),
						)
					)
				);
			},
			$args['section'],
			$args['section']
		);
	}

	/**
	 * Add the settings page
	 */
	public function show_navigation() {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->tabs as $tab => $name ) {
			$class = ( $tab === $this->get_current_tab() ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . esc_attr( $class ) . ' href="' . esc_url( admin_url( 'admin.php?page=userwall-wp-settings&tab=' . esc_attr( $tab ) ) ) . '">' . esc_attr( $name ) . '</a>';
		}
		echo '</h2>';

		if ( isset( $this->subtabs[ $this->get_current_tab() ] ) ) {
			echo '<ul class="subsubsub">';
			foreach ( $this->subtabs[ $this->get_current_tab() ] as $subtab => $name ) {
				$class = ( $subtab === $this->get_current_subtab() ) ? ' current' : '';
				echo '<li><a class="' . esc_attr( $class ) . ' href="' . esc_url( admin_url( 'admin.php?page=userwall-wp-settings&tab=' . $this->get_current_tab() . '&subtab=' . esc_attr( $subtab ) ) ) . '">' . esc_attr( $name ) . '</a> | </li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * Get the current tab
	 *
	 * @return string
	 */
	private function get_current_tab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
		return $tab;
	}

	/**
	 * Get the current subtab
	 *
	 * @return string
	 */
	private function get_current_subtab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['subtab'] ) ? sanitize_text_field( wp_unslash( $_GET['subtab'] ) ) : 'main';
	}

	/**
	 * Show the settings forms
	 */
	public function show_forms() {
		echo '<form method="post" action="options.php">';
		settings_fields( 'userwall_wp' );

		$current_tab = $this->get_current_tab();
		switch ( $current_tab ) {
			case 'general':
				$this->show_general_settings();
				break;
		}

		submit_button();
		echo '</form>';
	}

	/**
	 * Show the general settings
	 */
	private function show_general_settings() {
		$current_subtab = $this->get_current_subtab();
		switch ( $current_subtab ) {
			case 'main':
				do_settings_sections( 'userwall_settings_general' );
				break;
			case 'secondary':
				do_settings_sections( 'userwall_wp_general_secondary' );
				break;
		}
	}
}
