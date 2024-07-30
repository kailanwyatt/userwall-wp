<?php
/**
 * UserWall_WP_Admin class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Include settings library.
 */
require_once USERWALL_WP_PLUGIN_DIR . 'includes/library/class-userwall-wp-settings.php';

/**
 * UserWall_WP_Admin class
 */
class UserWall_WP_Admin {
	/**
	 * Instance
	 *
	 * @var UserWall_WP_Admin
	 */
	private static $instance;

	/**
	 * Undocumented variable
	 *
	 * @var object
	 */
	private $settings_api;

	/**
	 * Dashboard page key.
	 *
	 * @var string
	 */
	private $dashboard_page_key;

	/**
	 * Option for active addons.
	 *
	 * @var string
	 */
	private $option_active_addons = 'userwall_wp_active_addons';

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->dashboard_page_key = 'userwall-wp-dashboard';
		add_action( 'admin_menu', array( $this, 'add_menu' ), 6 );
		$this->settings_api = new UserWall_WP_Settings( 'userwall-wp-settings' );
		add_action( 'admin_init', array( $this->settings_api, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'process_addon_action' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		$template = new UserWall_Template();
		add_action( 'admin_enqueue_scripts', array( $template, 'enqueue_assets' ) );
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return UserWall_WP_Admin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		// Enqueue your scripts and styles here.
		wp_enqueue_style( 'my-admin-style', plugin_dir_url( __FILE__ ) . 'admin-style.css', array(), '1.0' );
		wp_enqueue_script( 'my-admin-script', plugin_dir_url( __FILE__ ) . 'admin-script.js', array( 'jquery' ), '1.0', true );
	}

	/**
	 * Add menu page.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			__( 'User Wall', 'userwall-wp' ),
			__( 'User Wall', 'userwall-wp' ),
			'manage_options',
			$this->dashboard_page_key,
			array( $this, 'posts_page' ),
			'dashicons-admin-generic'
		);

		// Instantiate the addon management class.
		$addons_manager = new UserWall_WP_Addons();

		if ( $addons_manager->is_active( 'polls' ) ) {
			$this->add_submenu( 'Polls', 'Polls', 'userwall-wp-polls', array( $this, 'polls_page' ) );
		}

		// Add a hook to add additional submenus.
		$submenus = apply_filters( 'userwall_wp_submenus', array() );
		if ( ! empty( $submenus ) ) {
			foreach ( $submenus as $submenu ) {
				if (
					! empty( $submenu['page_title'] ) ||
					! empty( $submenu['menu_title'] ) ||
					! empty( $submenu['menu_slug'] ) ||
					! empty( $submenu['callback'] )
				) {
					$this->add_submenu(
						sanitize_text_field( $submenu['page_title'] ),
						sanitize_text_field( $submenu['menu_title'] ),
						sanitize_text_field( $submenu['menu_slug'] ),
						$submenu['callback']
					);
				}
			}
		}
		$this->add_submenu( 'Addons', 'Addons', 'userwall-wp-addons', array( $this, 'addons_page' ) );
		$this->add_submenu( 'Settings', 'Settings', 'userwall-wp-settings', array( $this, 'settings_page' ) );
	}

	/**
	 * Add submenu page.
	 *
	 * @param string   $title       The title of the submenu page.
	 * @param string   $menu_title  The menu title of the submenu page.
	 * @param string   $menu_slug   The menu slug of the submenu page.
	 * @param callable $callback    The callback function to render the submenu page.
	 * @return void
	 */
	private function add_submenu( $title, $menu_title, $menu_slug, $callback ) {
		add_submenu_page(
			$this->dashboard_page_key,
			$title,
			$menu_title,
			'manage_options',
			$menu_slug,
			$callback
		);
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 */
	public function dashboard_page() {
		// Dashboard page content goes here.
	}

	/**
	 * Render the posts page.
	 *
	 * @return void
	 */
	public function posts_page() {
		$this->add_post_template();
	}

	/**
	 * Render the add post template.
	 *
	 * @return void
	 */
	private function add_post_template() {
		include USERWALL_WP_PLUGIN_DIR . '/includes/admin/templates/add-post.php';
	}

	/**
	 * Render the addons page.
	 *
	 * @return void
	 */
	public function addons_page() {
		include USERWALL_WP_PLUGIN_DIR . '/includes/admin/templates/addons.php';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function settings_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		$this->settings_api->show_navigation();
		echo '<div>';
		$this->settings_api->show_forms();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Render admin notices.
	 *
	 * @return void
	 */
	public function admin_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['addon_status'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended 
			$addon_id = ! empty( $_GET['addon_id'] ) ? sanitize_text_field( wp_unslash( $_GET['addon_id'] ) ) : '';
			if ( ! $addon_id ) {
				return;
			}
			// Instantiate the addon management class.
			$addons_manager = new UserWall_WP_Addons();
			$addons         = $addons_manager->register_addons();
			$addon          = $addons_manager->get_addon_by_id( $addon_id );
			echo '<div class="notice notice-success is-dismissible">';
			// translators: Placeholder for the addon name.
			echo '<p>' . sprintf( esc_html__( 'Addon "%s" has been deactivated.', 'userwall-wp' ), esc_html( $addon->get_name() ) ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Process the addon action.
	 *
	 * @return void
	 */
	public function process_addon_action() {
		// Check if the form was submitted.
		if ( current_user_can( 'manage_options' ) && isset( $_POST['_wpnonce'] ) && isset( $_POST['addon_id'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'userwall-wp-addon-action-' . sanitize_text_field( wp_unslash( $_POST['addon_id'] ) ) ) ) {
			if ( isset( $_POST['addon_action'] ) ) {
				if ( isset( $_POST['addon_id'] ) ) {
					$addon_id = sanitize_text_field( wp_unslash( $_POST['addon_id'] ) );
				}
				$active_addons     = get_option( $this->option_active_addons, array() );
				$active_addons_ids = array_keys( $active_addons );
				$redirect_url      = admin_url( 'admin.php?page=userwall-wp-addons' );
				// Instantiate the addon management class.
				$addons_manager = new UserWall_WP_Addons();
				$addons_manager->register_addons();
				$addons = $addons_manager->get_addons();
				if ( 'activate' === $_POST['addon_action'] ) {
					// Activate the addon.
					if ( empty( $active_addons_ids ) || ! in_array( $addon_id, $active_addons_ids, true ) ) {
						$addon = ! empty( $addons[ $addon_id ] ) ? $addons[ $addon_id ] : array();
						if ( $addon ) {
							$class_name       = get_class( $addon );
							$reflection_class = new ReflectionClass( $class_name );
							$file_path        = $reflection_class->getFileName();

							$addon_array                = array();
							$addon_array['id']          = $addon_id;
							$addon_array['name']        = $addon->get_name();
							$addon_array['description'] = $addon->get_description();
							$addon_array['version']     = $addon->get_version();
							$addon_array['active']      = $addon->is_active();
							$addon_array['file']        = $file_path;
							$addon_array['class']       = get_class( $addon );
							$active_addons[ $addon_id ] = $addon_array;
							update_option( $this->option_active_addons, $active_addons );
							$addons_manager->activate_addon( $addon_id );
							do_action( 'userwall_wp_after_addon_activated', $addon_id );
							$redirect_url = add_query_arg(
								array(
									'addon_status' => 'activated',
									'addon_id'     => $addon_id,
								),
								$redirect_url
							);
						}
					}
				} elseif ( 'deactivate' === $_POST['addon_action'] ) {
					// Deactivate the addon.
					if ( in_array( $addon_id, $active_addons_ids, true ) ) {
						unset( $active_addons[ $addon_id ] );
						update_option( $this->option_active_addons, $active_addons );
						// Deactivate addon by ID.
						$addons_manager->deactivate_addon( $addon_id );
						$redirect_url = add_query_arg(
							array(
								'addon_status' => 'deactivated',
								'addon_id'     => $addon_id,
							),
							$redirect_url
						);
						do_action( 'userwall_wp_after_addon_activated', $addon_id );
					}
				}
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}
}

// Initialize the admin class.
UserWall_WP_Admin::get_instance();
