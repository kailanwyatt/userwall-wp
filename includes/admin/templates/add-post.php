<?php
/**
 * This file is the template for the add post page.
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap">
	<h2><?php esc_html_e( 'Add Post', 'userwall-wp' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="new_post"><?php esc_html_e( 'Add Post Details', 'userwall-wp' ); ?></label>
			</th>
			<td>
				<div class="userwall-wp-admin-posts-wrapper">
					<?php echo do_shortcode( '[userwall_wp_post_form show_userwall="false"]' ); ?>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="posts"><?php esc_html_e( 'Posts', 'userwall-wp' ); ?></label>
			</th>
			<td>
				<div class="userwall-wp-admin-posts-wrapper">
					<?php echo do_shortcode( '[userwall_wp_post_form show_form="false"]' ); ?>
				</div>
			</td>
		</tr>
</table>
</div>
