<script type="text/html" id="tmpl-thread-wp-feed-template">
    <!-- Loop through threads -->
    <# _.each(data, function(thread) { #>
        <div class="threads-wp-thread" data-postid="{{ thread.post_id }}" data-user_id="{{ thread.user_id }}">
            <div class="threads-wp-ellipsis" aria-hidden="true">&#8942;</div>
            <!-- Thread content -->
            <div class="threads-wp-thread-content">
                {{{ thread.post_content }}}
            </div>
            <div class="threads-wp-thread-author">
                {{ thread.user_id }}
            </div>
            
            <!-- Action Area -->
            <div class="threads-wp-thread-actions" style="display: none;">
                <!-- Edit action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Edit', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Edit', 'threads-wp'); ?>"><?php esc_html_e('Edit', 'threads-wp'); ?></span>

                <!-- Delete action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Delete', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Delete', 'threads-wp'); ?>"><?php esc_html_e('Delete', 'threads-wp'); ?></span>

                <!-- Block action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Block', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Block', 'threads-wp'); ?>"><?php esc_html_e('Block', 'threads-wp'); ?></span>

                <!-- Report action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Report', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Report', 'threads-wp'); ?>"><?php esc_html_e('Report', 'threads-wp'); ?></span>

                <!-- Embed post action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Embed Post', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Embed Post', 'threads-wp'); ?>"><?php esc_html_e('Embed Post', 'threads-wp'); ?></span>

                <!-- Save action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Save', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Save', 'threads-wp'); ?>"><?php esc_html_e('Save', 'threads-wp'); ?></span>

                <!-- Follow action -->
                <span class="threads-wp-action" data-action="<?php esc_html_e('Follow', 'threads-wp'); ?>" aria-label="<?php esc_html_e('Follow', 'threads-wp'); ?>"><?php esc_html_e('Follow', 'threads-wp'); ?></span>
            </div>

            <?php /*
            <!-- Reaction Area -->
            <div class="threads-wp-reactions">
                <!-- Display reaction count -->
                <!-- Reactions count and emoji picker trigger -->
                <span class="threads-wp-reaction-count" aria-label="<?php esc_html_e('Reactions count', 'threads-wp'); ?>">{{ thread.reaction_count }}</span>
                <button class="emoji-picker-trigger" aria-label="<?php esc_html_e('Emoji Picker', 'threads-wp'); ?>">😀</button>

                <!-- Reaction buttons -->
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Like', 'threads-wp'); ?>"><?php esc_html_e('Like', 'threads-wp'); ?></button>
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Love', 'threads-wp'); ?>"><?php esc_html_e('Love', 'threads-wp'); ?>"</button>
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Haha', 'threads-wp'); ?>"><?php esc_html_e('Haha', 'threads-wp'); ?></button>
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Wow', 'threads-wp'); ?>"><?php esc_html_e('Wow', 'threads-wp'); ?></button>
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Sad', 'threads-wp'); ?>"><?php esc_html_e('Sad', 'threads-wp'); ?></button>
                <button class="threads-wp-reaction-button" aria-label="<?php esc_html_e('Angry', 'threads-wp'); ?>"><?php esc_html_e('Sad', 'threads-wp'); ?></button>
            </div>
            */ ?>

            <div class="">
                <div class="threads-wp-reaction-count" aria-label="<?php esc_html_e('Reactions count', 'threads-wp'); ?>">
                    {{ thread.reactions_count }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" fill="none" stroke-width="1.5"/>
                    </svg>
                </div>
                <!-- Comment Count -->
                <div class="threads-wp-comment-count" aria-label="<?php esc_html_e('Comment Count', 'threads-wp'); ?>">
                    {{ thread.comments_count }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" fill="none" stroke-width="1.5"/>
                        <line x1="7" y1="12" x2="17" y2="12" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="7" y1="7" x2="17" y2="7" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="7" y1="17" x2="17" y2="17" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </div>
            </div>

            <!-- LinkedIn-style comment box -->
            <div class="linkedin-style-comment-box">
                <textarea class="comment-textarea" placeholder="Write a comment..."></textarea>
                <button class="comment-submit-button">Post</button>
            </div>

            <!-- Comment Section -->
            <div class="threads-wp-comment-section"></div>
        </div>
    <# }); #>
</script>

<script type="text/html" id="tmpl-reddit-style-thread-comment-template">
    <!-- Comment Section -->
    <# _.each(thread.comments, function(comment) { #>
        <div class="threads-wp-comment">
            <!-- Comment content and author -->
            <div class="threads-wp-comment-content">
                {{ comment.comment_content }}
            </div>
            <div class="threads-wp-comment-author">
                {{ comment.comment_author }}
            </div>
            <!-- Reply button -->
            <div class="threads-wp-reply">
                <button class="threads-wp-reply-button" aria-label="<?php esc_html_e('Reply', 'threads-wp'); ?>">Reply</button>
                <div class="threads-wp-reply-form" style="display: none;">
                    <!-- Rich text editor container -->
                    <div data-comment-reply></div>
                    <!-- Reply and Cancel buttons -->
                    <button class="threads-wp-reply-submit" aria-label="<?php esc_html_e('Submit Reply', 'threads-wp'); ?>">Submit Reply</button>
                    <button class="threads-wp-reply-cancel" aria-label="<?php esc_html_e('Cancel Reply', 'threads-wp'); ?>">Cancel Reply</button>
                </div>
            </div>
        </div>
    <# }); #>
</script>
