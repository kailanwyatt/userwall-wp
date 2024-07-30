<?php
/**
 * JS Script Template
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Userwall WP Wall Comments Template.
?>
<!-- Comment Section -->
<# _.each(data, function(comment) { #>
	<div class="userwall-wp-comment" data-commentid="{{ comment.comment_id }}">
		<div class="userwall-wp-author-section">
			<div class="userwall-wp-author-image-wrapper">
				<a href="{{ comment.author_url}}" title="{{ comment.author_name }}"><img src="{{ comment.author_avatar_url }}" class="userwall-wp-author-image" /></a>
			</div>
			<div class="userwall-wp-author-image-info">
				<a href="{{ comment.author_url}}" title="{{ comment.author_name }}">{{ comment.author_name}}</a>
				<div class="userwall-wp-wall-time" data-time-post="{{ comment.comment_timestamp }}"></div>
			</div>
		</div>
		<?php if ( is_user_logged_in() ) : ?> 
		<div class="userwall-wp-ellipsis" aria-hidden="true">&#8942;</div>
		<?php endif; ?>
		<!-- Comment content and author -->
		<div class="userwall-wp-comment-content">
			{{{ comment.comment_content }}}
		</div>

		<# if ( comment.user_id == userwallWPObject.user_id ) { #>
		<!-- Edit Form (hidden by default) -->
		<div class="comment-thread-edit-form" style="display: none;">
			<div class="comment-thread-edit-form-editor">
				<div id="quill-editor-edit-{{ comment.post_id }}-{{ comment.comment_id }}" class="post-quill-editor-edit"></div>
			</div>
			<div class="comment-thread-edit-form-actions">
				<button class="save-button">Save Changes</button>
				<button class="cancel-button">Cancel Changes</button>
			</div>
		</div>
		<# } #>
		<div class="userwall-wp-comment-author">
			{{ comment.comment_author }}
		</div>

		<?php if ( is_user_logged_in() ) : ?>
		<!-- Action Area -->
		<div class="userwall-wp-thread-actions" style="display: none;">
			<# if ( comment.user_id == userwallWPObject.user_id ) { #>
			<!-- Edit action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Edit', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Edit', 'userwall-wp' ); ?>"><?php esc_html_e( 'Edit', 'userwall-wp' ); ?></span>

			<!-- Delete action -->
			<span class="userwall-wp-action" data-action="<?php esc_html_e( 'Delete Comment', 'userwall-wp' ); ?>" aria-label="<?php esc_html_e( 'Delete Comment', 'userwall-wp' ); ?>"><?php esc_html_e( 'Delete', 'userwall-wp' ); ?></span>
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
			<# } #>
		</div>
		<?php endif; ?>
		<?php userwall_wp_get_interaction_tmpl( 'comment' ); ?>
		<?php if ( is_user_logged_in() ) : ?>
		<!-- Reply button -->
		<div class="userwall-wp-reply">
			<button class="userwall-wp-reply-button" aria-label="<?php esc_html_e( 'Reply', 'userwall-wp' ); ?>">Reply</button>
			<div class="userwall-wp-reply-form" style="display: none;">
				<!-- Rich text editor container -->
				<div class="userwall-wp-reply-form-section">
					<div id="quill-editor-{{ comment.post_id }}-{{ comment.comment_id }}" data-comment-reply="quill-editor-{{ comment.post_id }}-{{ comment.comment_id }}"></div>
				</div>
				<div class="userwall-reply-actions">
					<!-- Reply and Cancel buttons -->
					<button class="userwall-wp-reply-submit" aria-label="<?php esc_html_e( 'Submit Reply', 'userwall-wp' ); ?>">Submit Reply</button>
					<button class="userwall-wp-reply-cancel" aria-label="<?php esc_html_e( 'Cancel Reply', 'userwall-wp' ); ?>">Cancel Reply</button>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- Comment Section -->
		<div class="userwall-wp-comment-reply-section"></div>
	</div>
<# }); #>