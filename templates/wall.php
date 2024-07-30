<?php
/**
 * JS Script Template
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Userwall WP Wall Template.
?>
<?php do_action( 'userwall_wp_header' ); ?>
<!-- Loop through userwall -->
<# _.each(data, function(thread) { #>
	<div class="userwall-wp-thread" data-postid="{{ thread.post_id }}" data-user_id="{{ thread.user_id }}" data-permalink="{{ thread.permalink }}">
		<div class="userwall-wp-author-section">
			<div class="userwall-wp-author-image-wrapper">
				<a href="{{ thread.author_url}}" title="{{ thread.author_name }}"><img src="{{ thread.author_avatar_url }}" class="userwall-wp-author-image" /></a>
			</div>
			<div class="userwall-wp-author-image-info">
				<a href="{{ thread.author_url}}" title="{{ thread.author_name }}">{{ thread.author_name}}</a> {{ thread.action }}
				<div class="userwall-wp-wall-time" data-time-post="{{ thread.post_timestamp }}"></div>
			</div>
		</div>
		<?php if ( is_user_logged_in() ) : ?> 
		<div class="userwall-wp-ellipsis" aria-hidden="true">&#8942;</div>
		<?php endif; ?>
		<div class="userwall-wp-post-body">
			<?php do_action( 'userwall_wp_before_post_title' ); ?>
			<# if ( thread.post_title ) { #>
			<div class="userwall-wp-thread-title-wrapper">
				<h2 class="userwall-wp-thread-title">{{ thread.post_title }}</h2>
			</div>
			<# } #>
			<?php do_action( 'userwall_wp_after_post_title' ); ?>
			<?php do_action( 'userwall_wp_before_post_content' ); ?>
			<div class="userwall-wp-thread-content">
				{{{ thread.post_content }}}
			</div>
			<?php do_action( 'userwall_wp_after_post_content' ); ?>
			<div class="userwall-wp-thread-media"></div>
		</div>
		<# if ( thread.user_id == userwallWPObject.user_id ) { #>
		<!-- Edit Form (hidden by default) -->
		<div class="edit-form userwall-edit-post-section" style="display: none;">
			<# if ( userwallWPObject.settings.allow_titles ) { #>
			<div class="userwall-wp-post-title-section">
				<input type="text" class="userwall-wp-post-title-edit-input" value="{{ thread.post_title }}" />
			</div>
			<# } #>
			<div class="userwall-edit-post-section-editor">
				<div id="quill-editor-edit-{{ thread.post_id }}" class="post-quill-editor-edit"></div>
			</div>
			<div class="userwall-edit-post-section-actions">
				<button class="save-button"><?php esc_html_e( 'Save Changes', 'userwall-wp' ); ?></button>
				<button class="cancel-button"><?php esc_html_e( 'Cancel Changes', 'userwall-wp' ); ?></button>
			</div>
		</div>
		<# } #>
		<?php if ( is_user_logged_in() ) : ?>
		<!-- Action Area -->
		<div class="userwall-wp-thread-actions" style="display: none;">
			<# if ( thread.user_id == userwallWPObject.user_id ) { #>
			<!-- Edit action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Edit', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Edit', 'userwall-wp' ); ?>"><?php esc_html_e( 'Edit', 'userwall-wp' ); ?></span>

			<!-- Delete action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Delete', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Delete', 'userwall-wp' ); ?>"><?php esc_html_e( 'Delete', 'userwall-wp' ); ?></span>
			<# } else { #>
			<!-- Block action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Block', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Block', 'userwall-wp' ); ?>"><?php esc_html_e( 'Block', 'userwall-wp' ); ?></span>

			<!-- Report action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Report', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Report', 'userwall-wp' ); ?>"><?php esc_html_e( 'Report', 'userwall-wp' ); ?></span>

			<!-- Embed post action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Embed Post', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Embed Post', 'userwall-wp' ); ?>"><?php esc_html_e( 'Embed Post', 'userwall-wp' ); ?></span>

			<!-- Save action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Save', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Save', 'userwall-wp' ); ?>"><?php esc_html_e( 'Save', 'userwall-wp' ); ?></span>

			<!-- Follow action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Follow', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Follow', 'userwall-wp' ); ?>"><?php esc_html_e( 'Follow', 'userwall-wp' ); ?></span>
			<?php do_action( 'userwall_wp_after_post_action' ); ?>
			<# } #>
		</div>
		<?php endif; ?>
		<?php userwall_wp_get_interaction_tmpl( 'thread' ); ?>
		<?php if ( is_user_logged_in() ) : ?>
		<!-- comment box -->
		<div class="comment-edit-form">
			<div class="userwall-wp-reply-editor">
				<div id="quill-comment-editor-edit-{{ thread.post_id }}" class="post-quill-editor-edit"></div>
			</div>
			<button class="comment-submit-button"><?php esc_html_e( 'Share Comment', 'userwall-wp' ); ?></button>
		</div>
		<?php endif; ?>

		<!-- Comment Section -->
		<div class="userwall-wp-comment-section"></div>
	</div>
<# }); #>
<?php do_action( 'userwall_wp_footer' ); ?>
