<?php
/**
 * This file is the template for the single post page.
 *
 * @package Userwall_WP
 */

?>
<div id="userwall-wp-container" data-thread data-post_id="<?php echo absint( $post_id ); ?>" data-thread-wrapper>
	<div class="userwall-wp-inner-thread"></div>
	<div class="loading-indicator" style="display: none;">Loading...</div>
	<div class="loading" id="loading"><div class="loading-spinner"></div></div>
</div>
