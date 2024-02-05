<?php
class UserWallWP_Addon_Groups_Core {
	private $admin_slug = 'userwall-wp-groups';
	public function __construct() {
		add_filter( 'userwall_wp_submenus', array( $this, 'add_admin_menu' ), 10, 1 );
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
	}

	public function add_admin_menu( $submenus = array() ) {
		$submenus[] = array(
			'page_title' => 'Groups',
			'menu_title' => 'Groups',
			'menu_slug'  => $this->admin_slug,
			'callback'   => array( $this, 'groups_page' ),
		);
		return $submenus;
	}

	public function add_rewrite_rules() {
		add_rewrite_rule( '^g/([^/]*)/([^/]*)/?$', 'index.php?pagename=g&group=$matches[1]&wall_type=group&tab=[2]', 'top' );

		add_rewrite_rule( '^g/([^/]*)/?$', 'index.php?pagename=g&group=$matches[1]&wall_type=group&tab=$matches[2]', 'top' );
	}

	public function register_query_vars( $vars ) {
		$vars[] = 'wall_type';
		$vars[] = 'group';
		$vars[] = 'tab';
		return $vars;
	}

	public function groups_page() {
		if ( isset( $_POST ) &&
			isset( $_POST['name_of_nonce_field'] ) &&
			! wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' )
		) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			// process form data
		}
		require_once USERWALL_WP_PLUGIN_DIR . 'includes/admin/tables/groups.php';
		$groups_list = new Userwall_Groups_List_Table();
		
		$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( 'add_new' === $action ) {
			// Check user capability
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Check if the form is submitted
			if ( isset( $_POST['userwall_group_nonce'] ) && wp_verify_nonce( $_POST['userwall_group_nonce'], 'userwall_create_group' ) ) {
				// Process form data here
				// Use your logic to create a group, e.g., inserting data into the database
				// ...

				echo '<div class="updated"><p>Group created successfully.</p></div>';
			}
			include USERWALL_WP_PLUGIN_DIR . 'includes/admin/templates/add-group.php';
		} else {
			?>
		<div class="wrap">
			<h2>Userwall Groups</h2>
			<a href="<?php echo admin_url( 'admin.php?page=' . $this->admin_slug . '&action=add_new' ); ?>" class="page-title-action"><?php echo esc_html__( 'Add New Group', 'wp-userwall-groups-admin-table' ); ?></a>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$groups_list->prepare_items();
								$groups_list->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>  
		</div>
		<?php } ?>
		<?php
	}
}
