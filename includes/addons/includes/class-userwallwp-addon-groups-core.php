<?php
/**
 * Class UserWallWP_Addon_Groups_Core
 *
 * @package UserWallWP_Addon_Groups_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserWallWP_Addon_Groups_Core
 */
class UserWallWP_Addon_Groups_Core {
	/**
	 * Admin slug.
	 *
	 * @var string
	 */
	private $admin_slug = 'userwall-wp-groups';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'userwall_wp_submenus', array( $this, 'add_admin_menu' ), 10, 1 );
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
	}

	/**
	 * Add admin menu.
	 *
	 * @param array $submenus Array of submenus.
	 * @return array Modified array of submenus.
	 */
	public function add_admin_menu( $submenus = array() ) {
		$submenus[] = array(
			'page_title' => 'Groups',
			'menu_title' => 'Groups',
			'menu_slug'  => $this->admin_slug,
			'callback'   => array( $this, 'groups_page' ),
		);
		return $submenus;
	}

	/**
	 * Add rewrite rules.
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^g/([^/]*)/([^/]*)/?$', 'index.php?pagename=g&group=$matches[1]&wall_type=group&tab=[2]', 'top' );

		add_rewrite_rule( '^g/([^/]*)/?$', 'index.php?pagename=g&group=$matches[1]&wall_type=group&tab=$matches[2]', 'top' );
	}

	/**
	 * Register query vars.
	 *
	 * @param array $vars Array of query vars.
	 * @return array Modified array of query vars.
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'wall_type';
		$vars[] = 'group';
		$vars[] = 'tab';
		return $vars;
	}

	/**
	 * Render groups page.
	 */
	public function groups_page() {

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		if ( 'add_new' === $action ) {
			// Check user capability.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Check if the form is submitted.
			if ( isset( $_POST['userwall_group_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['userwall_group_nonce'] ) ), 'userwall_create_group' ) ) {
				echo '<div class="updated"><p>' . esc_html__( 'Group created successfully.', 'userwall-wp' ) . '</p></div>';
			}
			include USERWALL_WP_PLUGIN_DIR . 'includes/admin/templates/add-group.php';
		}
	}
}
