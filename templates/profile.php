<div class="userwall-wp-profile-wrapper">
	<div class="userwall-wp-profile-header">
		<div class="userwall-wp-profile-avatar">
			<img src="<?php echo esc_attr( get_userwall_wp_profile_data( 'avatar' ) ); ?>" alt="<?php esc_attr( sprintf( __( 'Avatar of %s', 'userwall-wp' ), get_userwall_wp_profile_data( 'display_name' ) ) ); ?>" />
		</div>
		<div class="userwall-wp-profile-name">
			<div class="userwall-wp-profile-display-name"><h1><?php echo esc_html( get_userwall_wp_profile_data( 'display_name' ) ); ?></h1></div>
			<div class="userwall-wp-profile-user-name"><?php echo esc_html( get_userwall_wp_profile_data( 'username' ) ); ?></div>
		</div>
	</div>
	<div class="userwall-wp-profile-content">
		<?php do_action( 'userwall_profile_content_' . $profile_tab, $profile_id ); ?>
		<?php do_action( 'userwall_profile_content', $profile_tab, $profile_id ); ?>
	</div>
</div>
