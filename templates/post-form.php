<?php if ( is_user_logged_in() && $show_form ) : ?>
<div class="userwall-wp-form-wrapper">
	<form id="userwall-wp-post-form" enctype='multipart/form-data'>
		<div class="userwall-tab-content-wrapper">
			<?php if ( count( $post_types ) > 1 ) : ?>
				
			<?php endif; ?>
			<div class="userwall-tab-content" data-tab="post">
				<!-- Post content input -->
				<div class="userwall-wp-post-section">
					<div id="quill-editor-post-form" class="post-quill-editor"></div>
				</div>
				<div class="userwall-wp-after-post-section">
				<?php if ( ! empty( $content_types ) ) : ?>
					<div class="userwall-wp-post-types">
						<ul>
							<?php foreach ( $content_types as $id => $type ) : ?>
							<li><a href="#" class="userwall-wp-post-type" data-type="<?php echo esc_attr( $id ); ?>" title="<?php echo esc_attr( $type['title'] ); ?>"><?php echo userwall_get_icon( $type['icon'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>
					<?php if ( $max_characters ) : ?>
					<div id="userwall-wp-charcount" class="userwall-wp-charcount-wrapper"><div class="userwall-wp-charcount-lng">Characters: 0/<?php echo esc_html( absint( $max_characters ) ); ?></div></div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="rich-preview-container">
				<!-- Rich preview will be displayed here -->
			</div>
			<?php do_action( 'userwall_wp_after_post_form' ); ?>
		</div>
		<div class="userwall-wp-post-submission-wrapper">
			<button class="submit-button">Submit</button>
		</div>
	</form>
</div>
<?php endif; ?>
<?php if ( $show_userwall ) : ?>
<div id="userwall-wp-container" data-thread="<?php echo absint( $type ); ?>" data-object-id="<?php absint( $object_id ); ?>" data-thread-wrapper data-post_type="<?php echo absint( $type ); ?>" data-per_page="<?php echo absint( $per_page ); ?>" data-page="1">
	<div class="userwall-wp-inner-thread"></div>
	<div class="loading-indicator" style="display: none;">Loading...</div>
	<div class="loading" id="loading"><div class="loading-spinner"></div></div>
</div>
<?php endif; ?>
