<?php
/**
 * This file is the template for the addons page.
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Instantiate the addon management class.
$addons_manager = new UserWall_WP_Addons();

// Register and load addons.
$addons = array();
$addons = $addons_manager->register_addons( $addons );

// Get the list of installed addons.
$addons = $addons_manager->get_addons();
?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'userwall-wp' ); ?></th>
					<th><?php esc_html_e( 'Description', 'userwall-wp' ); ?></th>
					<th><?php esc_html_e( 'Version', 'userwall-wp' ); ?></th>
					<th><?php esc_html_e( 'Status', 'userwall-wp' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $addons ) ) : ?>
					<?php foreach ( $addons as $addon ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $addon->get_name() ); ?></strong></td>
						<td><?php echo esc_html( $addon->get_description() ); ?></td>
						<td><?php echo esc_html( $addon->get_version() ); ?></td>
						<td>
							<?php
							$addon_id    = $addon->get_id();
							$is_active   = $addons_manager->is_active( $addon->get_id() );
							$addon_ready = $addon->is_ready();
							if ( $addon_ready ) :
								?>
							<form method="post">
								<input type="hidden" name="addon_id" value="<?php echo esc_attr( $addon_id ); ?>">
								<?php wp_nonce_field( 'userwall-wp-addon-action-' . $addon_id ); ?>
								<?php if ( $is_active ) : ?>
									<input type="hidden" name="addon_action" value="deactivate">
									<button type="submit" class="button"><?php esc_html_e( 'Deactivate', 'userwall-wp' ); ?></button>
								<?php else : ?>
									<input type="hidden" name="addon_action" value="activate">
									<button type="submit" class="button"><?php esc_html_e( 'Activate', 'userwall-wp' ); ?></button>
								<?php endif; ?>
							</form>
							<?php else : ?>
								<?php esc_html_e( 'Coming Soon', 'userwall-wp' ); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
