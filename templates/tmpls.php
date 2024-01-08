<script type="text/html" id="tmpl-reddit-style-thread-template">
    <div class="threads-wp-reddit-thread">
        <!-- Loop through threads -->
        <# _.each(data.threads, function(thread) { #>
            <div class="threads-wp-thread">
                <div class="threads-wp-ellipsis">&#8942;</div>
                <!-- Thread content -->
                <div class="threads-wp-thread-content">
                    {{ thread.thread_content }}
                </div>
                <div class="threads-wp-thread-author">
                    {{ thread.thread_author }}
                </div>
                
                <!-- Action Area -->
                <div class="threads-wp-thread-actions" style="display: none;">
                    <!-- Edit action -->
                    <span class="threads-wp-action" data-action="<?php _e('Edit', 'threads-wp'); ?>"><?php _e('Edit', 'threads-wp'); ?></span>

                    <!-- Delete action -->
                    <span class="threads-wp-action" data-action="<?php _e('Delete', 'threads-wp'); ?>"><?php _e('Delete', 'threads-wp'); ?></span>

                    <!-- Block action -->
                    <span class="threads-wp-action" data-action="<?php _e('Block', 'threads-wp'); ?>"><?php _e('Block', 'threads-wp'); ?></span>

                    <!-- Report action -->
                    <span class="threads-wp-action" data-action="<?php _e('Report', 'threads-wp'); ?>"><?php _e('Report', 'threads-wp'); ?></span>

                    <!-- Embed post action -->
                    <span class="threads-wp-action" data-action="<?php _e('Embed Post', 'threads-wp'); ?>"><?php _e('Embed Post', 'threads-wp'); ?></span>

                    <!-- Save action -->
                    <span class="threads-wp-action" data-action="<?php _e('Save', 'threads-wp'); ?>"><?php _e('Save', 'threads-wp'); ?></span>

                    <!-- Follow action -->
                    <span class="threads-wp-action" data-action="<?php _e('Follow', 'threads-wp'); ?>"><?php _e('Follow', 'threads-wp'); ?></span>
                </div>


                <!-- Reaction Area -->
                <div class="threads-wp-reactions">
                    <!-- Display reaction count -->
                    <!-- Reactions count and emoji picker trigger -->
                    <span class="threads-wp-reaction-count">{{ thread.reaction_count }}</span>
                    <button class="emoji-picker-trigger">😀</button>

                    <!-- Reaction buttons -->
                    <button class="threads-wp-reaction-button">Like</button>
                    <button class="threads-wp-reaction-button">Love</button>
                    <button class="threads-wp-reaction-button">Haha</button>
                    <button class="threads-wp-reaction-button">Wow</button>
                    <button class="threads-wp-reaction-button">Sad</button>
                    <button class="threads-wp-reaction-button">Angry</button>
                </div>

                <!-- Comment Count -->
                <div class="threads-wp-comment-count">{{ thread.comment_count }} Comments</div>

                <!-- Comment Section -->
                <div class="threads-wp-comment-section">
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
                                <button class="threads-wp-reply-button">Reply</button>
                                <div class="threads-wp-reply-form" style="display: none;">
                                    <!-- Rich text editor container -->
                                    <div data-comment-reply></div>
                                    <!-- Reply and Cancel buttons -->
                                    <button class="threads-wp-reply-submit">Reply</button>
                                    <button class="threads-wp-reply-cancel">Cancel</button>
                                </div>
                            </div>
                        </div>
                    <# }); #>
                </div>
            </div>
        <# }); #>
    </div>
</script>
