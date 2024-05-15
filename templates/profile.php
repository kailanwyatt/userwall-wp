<?php
/**
 * This file is the template for the user profile page.
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="userwall-wp-profile-wrapper">
	<div class="userwall-wp-profile-header">
		<div class="userwall-wp-profile-avatar">
			<img src="<?php echo esc_attr( userwall_wp_get_userwall_wp_profile_data( 'avatar' ) ); ?>" alt="<?php // translators: Avatar alt text placeholder. ?><?php esc_attr( sprintf( __( 'Avatar of %s', 'userwall-wp' ), userwall_wp_get_userwall_wp_profile_data( 'display_name' ) ) ); ?>" />
		</div>
		<div class="userwall-wp-profile-name">
			<div class="userwall-wp-profile-display-name"><h1><?php echo esc_html( userwall_wp_get_userwall_wp_profile_data( 'display_name' ) ); ?></h1></div>
			<div class="userwall-wp-profile-user-name"><?php echo esc_html( userwall_wp_get_userwall_wp_profile_data( 'username' ) ); ?></div>
		</div>
	</div>
	<div class="userwall-wp-profile-content">
		<?php do_action( 'userwall_profile_content_' . $profile_tab, $profile_id ); ?>
		<?php do_action( 'userwall_profile_content', $profile_tab, $profile_id ); ?>
	</div>
</div>
