<div class="wrap">
	<h2><?php echo esc_html__( 'Add New Group', 'wp-userwall-groups-admin-table' ); ?></h2>
	<form method="post" action="">
			<?php
			// Nonce field for security
			wp_nonce_field( 'userwall_create_group', 'userwall_group_nonce' );
			?>

			<table class="form-table">
				<tr>
					<th scope="row"><label for="group-name"><?php echo esc_html__( 'Group Name', 'wp-userwall-groups-admin-table' ); ?></label></th>
					<td><input name="group_name" type="text" id="group-name" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="group-slug"><?php echo esc_html__( 'Group Slug', 'wp-userwall-groups-admin-table' ); ?></label></th>
					<td><input name="group_slug" type="text" id="group-slug" class="regular-text" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="group-description"><?php echo esc_html__( 'Description', 'wp-userwall-groups-admin-table' ); ?></label></th>
					<td><textarea name="group_description" id="group-description" rows="5" cols="30" class="large-text"></textarea></td>
				</tr>
				<tr>
					<th scope="row"><label for="group-avatar"><?php echo esc_html__( 'Group Avatar URL', 'wp-userwall-groups-admin-table' ); ?></label></th>
					<td><input name="group_avatar" type="text" id="group-avatar" class="regular-text"></td>
				</tr>
			</table>

			<?php submit_button( 'Create Group' ); ?>
		</form>
</div>
