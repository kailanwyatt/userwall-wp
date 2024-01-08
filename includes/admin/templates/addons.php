<?php

// Instantiate the addon management class
$addons_manager = new Threads_WP_Addons();

// Register and load addons
$addons = array();
$addons = $addons_manager->register_addons($addons);

// Get the list of installed addons
$addons = $addons_manager->get_addons();
?>
<div class="wrap">
    <h2><?php echo get_admin_page_title(); ?></h2>
    <div>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Version</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $addons ) ) : ?>
                <?php foreach ($addons as $addon) : ?>
                    <tr>
                        <td><?php echo esc_html($addon->get_name()); ?></td>
                        <td><?php echo esc_html($addon->get_description()); ?></td>
                        <td><?php echo esc_html($addon->get_version()); ?></td>
                        <td>
                            <?php
                            $addon_id = $addon->get_id();
                            $is_active = $addons_manager->is_active( $addon->get_id() );
                            
                            // Output a form for each addon
                            ?>
                            <form method="post">
                                <input type="hidden" name="addon_id" value="<?php echo esc_attr($addon_id); ?>">
                                <?php if ($is_active) : ?>
                                    <input type="hidden" name="addon_action" value="deactivate">
                                    <button type="submit" class="button">Deactivate</button>
                                <?php else : ?>
                                    <input type="hidden" name="addon_action" value="activate">
                                    <button type="submit" class="button">Activate</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>