<script type="text/html" id="tmpl-thread-shared-post">
</script>
<script type="text/html" id="tmpl-userwall-wp-feed-template">
    <?php do_action( 'wp_userwall_header' ); ?>
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
            <?php do_action( 'userwall_wp_before_post_content' ); ?>
            <div class="userwall-wp-thread-content">
                {{{ thread.post_content }}}
            </div>
            <?php do_action( 'userwall_wp_after_post_content' ); ?>
            <div class="userwall-wp-thread-media"></div>
            <# if ( thread.user_id == userwallWPObject.user_id ) { #>
            <!-- Edit Form (hidden by default) -->
            <div class="edit-form userwall-edit-post-section" style="display: none;">
                <div id="quill-editor-edit-{{ thread.post_id }}" class="post-quill-editor-edit"></div>
                <button class="save-button"><?php esc_html_e('Save Changes', 'userwall-wp'); ?></button>
                <button class="cancel-button"><?php esc_html_e('Cancel Changes', 'userwall-wp'); ?></button>
            </div>
            <# } #>
            <?php if ( is_user_logged_in() ) : ?>
            <!-- Action Area -->
            <div class="userwall-wp-thread-actions" style="display: none;">
                <# if ( thread.user_id == userwallWPObject.user_id ) { #>
                <!-- Edit action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Edit', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Edit', 'userwall-wp'); ?>"><?php esc_html_e('Edit', 'userwall-wp'); ?></span>

                <!-- Delete action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Delete', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Delete', 'userwall-wp'); ?>"><?php esc_html_e('Delete', 'userwall-wp'); ?></span>
                <# } else { #>
                <!-- Block action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Block', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Block', 'userwall-wp'); ?>"><?php esc_html_e('Block', 'userwall-wp'); ?></span>

                <!-- Report action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Report', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Report', 'userwall-wp'); ?>"><?php esc_html_e('Report', 'userwall-wp'); ?></span>

                <!-- Embed post action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Embed Post', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Embed Post', 'userwall-wp'); ?>"><?php esc_html_e('Embed Post', 'userwall-wp'); ?></span>

                <!-- Save action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Save', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Save', 'userwall-wp'); ?>"><?php esc_html_e('Save', 'userwall-wp'); ?></span>

                <!-- Follow action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Follow', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Follow', 'userwall-wp'); ?>"><?php esc_html_e('Follow', 'userwall-wp'); ?></span>
                <?php do_action( 'userwall_wp_after_post_action' ); ?>
                <# } #>
            </div>
            <?php endif; ?>
            <?php /*
            <!-- Reaction Area -->
            <div class="userwall-wp-reactions">
                <!-- Display reaction count -->
                <!-- Reactions count and emoji picker trigger -->
                <span class="userwall-wp-reaction-count" aria-label="<?php esc_html_e('Reactions count', 'userwall-wp'); ?>">{{ thread.reaction_count }}</span>
                <button class="emoji-picker-trigger" aria-label="<?php esc_html_e('Emoji Picker', 'userwall-wp'); ?>">😀</button>

                <!-- Reaction buttons -->
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Like', 'userwall-wp'); ?>"><?php esc_html_e('Like', 'userwall-wp'); ?></button>
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Love', 'userwall-wp'); ?>"><?php esc_html_e('Love', 'userwall-wp'); ?>"</button>
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Haha', 'userwall-wp'); ?>"><?php esc_html_e('Haha', 'userwall-wp'); ?></button>
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Wow', 'userwall-wp'); ?>"><?php esc_html_e('Wow', 'userwall-wp'); ?></button>
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Sad', 'userwall-wp'); ?>"><?php esc_html_e('Sad', 'userwall-wp'); ?></button>
                <button class="userwall-wp-reaction-button" aria-label="<?php esc_html_e('Angry', 'userwall-wp'); ?>"><?php esc_html_e('Sad', 'userwall-wp'); ?></button>
            </div>
            */ ?>

            <div class="userwall-wp-activity-section">
                <div class="userwall-wp-reaction-count userwall-wp-activity-block" aria-label="<?php esc_html_e('Reactions count', 'userwall-wp'); ?>">
                    <span class="span-count">{{ thread.reactions_count }}</span>
                    <span class="userwall-wp-comment-img">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                        </svg>
                    </span>
                    <span><?php esc_html_e('Like', 'userwall-wp'); ?></span>
                </div>

                <!-- Comment Count -->
                <div class="userwall-wp-comment-count userwall-wp-activity-block" aria-label="<?php esc_html_e('Comment Count', 'userwall-wp'); ?>">
                    <span class="userwall-wp-comment-img">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    </span>
                    <span class="userwall-wp-comment-span">{{ thread.comments_count }} comments</span>
                </div>

                <div class="userwall-wp-share userwall-wp-activity-block" aria-label="<?php esc_html_e('Share', 'userwall-wp'); ?>">
                    <span class="userwall-wp-comment-img">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                    </span>
                    <span><?php esc_html_e('Share', 'userwall-wp'); ?></span>
                </div>
            </div>
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
    <?php do_action('userwall_wp_footer' ); ?>
</script>

<script type="text/html" id="tmpl-userwall-wp-thread-comment-template">
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
                <div id="quill-editor-edit-{{ comment.post_id }}-{{ comment.comment_id }}" class="post-quill-editor-edit"></div>
                <button class="save-button">Save Changes</button>
                <button class="cancel-button">Cancel Changes</button>
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
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Edit Comment', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Edit', 'userwall-wp'); ?>"><?php esc_html_e('Edit', 'userwall-wp'); ?></span>

                <!-- Delete action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Delete Comment', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Delete Comment', 'userwall-wp'); ?>"><?php esc_html_e('Delete', 'userwall-wp'); ?></span>
                <# } else { #>
                <!-- Block action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Block', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Block', 'userwall-wp'); ?>"><?php esc_html_e('Block', 'userwall-wp'); ?></span>

                <!-- Report action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Report', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Report', 'userwall-wp'); ?>"><?php esc_html_e('Report', 'userwall-wp'); ?></span>

                <!-- Embed post action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Embed Post', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Embed Post', 'userwall-wp'); ?>"><?php esc_html_e('Embed Post', 'userwall-wp'); ?></span>

                <!-- Save action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Save', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Save', 'userwall-wp'); ?>"><?php esc_html_e('Save', 'userwall-wp'); ?></span>

                <!-- Follow action -->
                <span class="userwall-wp-action" data-action="<?php esc_html_e('Follow', 'userwall-wp'); ?>" aria-label="<?php esc_html_e('Follow', 'userwall-wp'); ?>"><?php esc_html_e('Follow', 'userwall-wp'); ?></span>
                <# } #>
            </div>
            <?php endif; ?>
            <?php if ( is_user_logged_in() ) : ?>
            <!-- Reply button -->
            <div class="userwall-wp-reply">
                <button class="userwall-wp-reply-button" aria-label="<?php esc_html_e('Reply', 'userwall-wp'); ?>">Reply</button>
                <div class="userwall-wp-reply-form" style="display: none;">
                    <!-- Rich text editor container -->
                    <div class="userwall-wp-reply-form-section">
                        <div id="quill-editor-{{ comment.post_id }}-{{ comment.comment_id }}" data-comment-reply="quill-editor-{{ comment.post_id }}-{{ comment.comment_id }}"></div>
                    </div>
                    <div class="userwall-reply-actions">
                        <!-- Reply and Cancel buttons -->
                        <button class="userwall-wp-reply-submit" aria-label="<?php esc_html_e('Submit Reply', 'userwall-wp'); ?>">Submit Reply</button>
                        <button class="userwall-wp-reply-cancel" aria-label="<?php esc_html_e('Cancel Reply', 'userwall-wp'); ?>">Cancel Reply</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- Comment Section -->
            <div class="userwall-wp-comment-reply-section"></div>
        </div>
    <# }); #>
</script>
