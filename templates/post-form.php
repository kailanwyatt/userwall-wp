<?php if ( is_user_logged_in() && $show_form ) : ?>
<div class="threads-wp-form-wrapper">
    <form id="threads-wp-post-form" enctype='multipart/form-data'>
        <div class="threads-tab-content-wrapper">
            <div class="threads-tab-content" data-tab="post">
                <!-- Post content input -->
                <div class="threads-wp-post-section">
                    <div id="quill-editor-post-form" class="post-quill-editor"></div>
                    <div id="thread-wp-post-toolbar">
                        <!-- Add font size dropdown -->
                        <select class="ql-size">
                            <option value="small"></option>
                            <!-- Note a missing, thus falsy value, is used to reset to default -->
                            <option selected></option>
                            <option value="large"></option>
                            <option value="huge"></option>
                        </select>
                        <!-- Add a bold button -->
                        <button class="ql-bold"></button>
                        <!-- Add subscript and superscript buttons -->
                        <button class="ql-script" value="sub"></button>
                        <button class="ql-script" value="super"></button>
                    </div>
                    <div class="threads-wp-post-types">
                        <ul>
                            <li><a href="#" class="threads-wp-post-type" data-type="image">Image</a></li>
                            <li><a href="#" class="threads-wp-post-type" data-type="polls">Polls</a></li>
                            <li><a href="#" class="threads-wp-post-type" data-type="event">Event</a></li>
                            <li><a href="#" class="threads-wp-post-type" data-type="article">Article</a></li>
                            <li><a href="#" class="threads-wp-post-type" data-type="file">File</a></li>
                            <li><a href="#" class="threads-wp-post-type" data-type="link">Link</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="rich-preview-container">
                <!-- Rich preview will be displayed here -->
            </div>
            <?php do_action( 'threads_wp_after_post_form' ); ?>
        </div>
        <div class="threads-wp-post-submission-wrapper">
            <button class="submit-button">Submit</button>
        </div>
    </form>
</div>
<?php endif; ?>
<?php if ( $show_threads ) : ?>
<div id="reddit-container" data-thread="<?php echo absint( $type ); ?>" data-object-id="<?php absint( $object_id ); ?>" data-thread-wrapper data-post_type="<?php echo absint( $type ); ?>" data-per_page="<?php echo absint( $per_page ); ?>" data-page="1">
    <div class="threads-wp-reddit-thread"></div>
    <div class="loading-indicator" style="display: none;">Loading...</div>
</div>
<?php endif; ?>