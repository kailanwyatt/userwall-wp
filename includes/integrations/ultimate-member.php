<?php
class WP_Thread_Integration_Ultimate_Member {
	public function __construct() {
	}

	public function hooks() {
		add_filter( 'um_profile_tabs', array( $this, 'add_custom_tab' ), 1000 );
		add_action( 'um_profile_content_activity', array( $this, 'custom_tab_content' ) );
	}

	public function add_custom_tab( $tabs ) {
		$tabs['activity'] = array(
			'name' => 'Activity',
			'icon' => 'fas fa-star', // Customize the icon
		);
		return $tabs;
	}

	public function custom_tab_content( $args ) {
		$current_user_profile_id = um_profile_id();
		echo do_shortcode( '[userwall_wp_post_form type="user-posts" per_page="5" object_id="' . $current_user_profile_id . '"]' );
	}
}

$um_integration = new WP_Thread_Integration_Ultimate_Member();
$um_integration->hooks();
