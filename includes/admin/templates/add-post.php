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
<style>
	.userwall-wp-admin-posts-wrapper {
		max-width: 600px
	}
	.comment-edit-form,
	.userwall-wp-activity-section {
		display: none;
	}
	.filter-container {
		display: flex;
		align-items: center;
	}
	.filter-label {
		margin-right: 10px;
	}
</style>
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
			<?php
			/*
			<div class="filter-container">
					<label class="filter-label" for="date-from">Date From:</label>
					<input type="text" id="date-from" class="date-picker small-text">

					<label class="filter-label" for="date-end">Date End:</label>
					<input type="text" id="date-end" class="date-picker small-text">

					<label class="filter-label" for="search">Search:</label>
					<input type="text" id="search">

					<label class="filter-label" for="user">User:</label>
					<select id="user">
						<option value="user1">User 1</option>
						<option value="user2">User 2</option>
						<!-- Add more user options as needed -->
					</select>

					<label class="filter-label" for="sort-by">Sort By:</label>
					<select id="sort-by">
						<option value="created-desc">Created (DESC)</option>
						<option value="created-asc">Created (ASC)</option>
					</select>

					<button id="apply-filter">Apply Filter</button>
				</div>*/
			?>
				<div class="userwall-wp-admin-posts-wrapper">
					<?php echo do_shortcode( '[userwall_wp_post_form show_form="false"]' ); ?>
				</div>
			</td>
		</tr>
</table>
</div>
