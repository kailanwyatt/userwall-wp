<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'BP_Component' ) ) {
	class BP_Your_Component extends BP_Component {

		/**
		 * Constructor method.
		 */
		public function __construct() {
			parent::start(
				// Your component ID.
				'custom',
				// Your component Name.
				__( 'Custom component', 'custom-text-domain' ),
				/*
				 * The path from where additional files should be included.
				 *
				 * FYI: this class is inside an `/inc` subdirectory of your add-on directory.
				 *
				 * Below is a typical relative path for it:
				 * `/wp-content/plugins/bp-custom/inc/classes/class-bp-custom-component.php
				 */
				plugin_dir_path( __DIR__ ),
			);
		}

		/**
		 * Register your custom menu items into the single Member's navigation.
		 *
		 * @since 12.0.0
		 *
		 * @param array $main_nav Associative array
		 * @param array $sub_nav  Optional. Multidimensional Associative array.
		 */
		public function register_nav( $main_nav = array(), $sub_nav = array() ) {
			parent::register_nav(
				array(
					'name'                => $this->name,
					'slug'                => $this->slug,
					'position'            => 100,
					'screen_function'     => array( $this, 'bp_custom_add_on_screen_callback' ),
					'default_subnav_slug' => 'default-subnav-slug',
					'item_css_id'         => $this->id,
				),
			);
		}

		/**
		 * Setup BP Specific globals and custom ones.
		 *
		 * @since BuddyPress 1.5.0
		 *
		 * @param array $bp_globals {
		 *     All values are optional.
		 *     @type string   $slug                  The portion of URL to use for a member's page about your component.
		 *                                           Default: the component's ID.
		 *     @type string   $root_slug             The portion of URL to use for your component's directory page.
		 *     @type boolean  $has_directory         Whether your component is using a directory page or not.
		 *     @type array    $rewrite_ids           Your components rewrite IDs.
		 *     @type string   $directory_title       The title of your component's directory page.
		 *     @type string   $search_string         The placeholder text in the component directory search box.
		 *                                           Eg: 'Search Custom objects...'.
		 *     @type callable $notification_callback The callable function that formats the component's notifications.
		 *     @type array    $global_tables         An array of database table names.
		 *     @type array    $meta_tables           An array of metadata table names.
		 *     @type array    $block_globals         An array of globalized data for your component's Blocks.
		 * }
		 */
		public function setup_globals( $bp_globals = array() ) {
			$bp_globals = array(
				'slug'          => 'custom-slug',
				'has_directory' => false,
			);

			// BP Specigic globals.
			parent::setup_globals( $bp_globals );

			// Your component's globals (if needed).
			$this->custom_global = true;
		}

		function bp_custom_add_on_screen_displayed() {
			echo 'It works!';
		}

		/**
		 * The screen function referenced as the `$screen_function` of your
		 * navigation arrays. Of course you can use different screen functions
		 * for each of your sub navigation items.
		 */
		function bp_custom_add_on_screen_callback() {
			bp_core_load_template( 'members/single/home' );

			add_action( 'bp_template_content', array( $this, 'bp_custom_add_on_screen_displayed' ) );
		}
	}


	function bp_your_component_init() {
		buddypress()->custom = new BP_Your_Component();
	}
	add_action( 'bp_setup_components', 'bp_your_component_init' );
}
