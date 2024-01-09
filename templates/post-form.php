<div class="threads-wp-form-wrapper">
    <form id="threads-wp-post-form" enctype='multipart/form-data'>
    <ul class="threads-tabs">
        <?php if ( ! empty(  $post_tabs ) && count( $post_tabs ) > 1 ) : ?>
            <?php foreach ( $post_tabs as $tab => $label ) : ?>
            <li class="threads-tab" data-tab="<?php echo esc_attr( $tab ); ?>"><?php echo esc_html( $label ); ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <?php if ( ! empty( $post_tabs ) ) : ?>
        <div class="threads-tab-content-wrapper">
        <?php foreach ( $post_tabs as $tab  => $label ) : ?>
        <?php switch( $tab ) {
            case 'post':
                ?>
                <div class="threads-tab-content" data-tab="post">
                    <!-- Post content input -->
                    <div id="quill-editor-9" class="post-quill-editor"></div>
                    <div id="toolbar">
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
                </div>
                <div class="rich-preview-container">
                    <!-- Rich preview will be displayed here -->
                </div>
                <?php
                break;
            case 'poll':
                ?>
                 <div class="threads-tab-content" data-tab="poll">
                    <!-- Poll content input -->
                    <textarea placeholder="Ask a question..."></textarea>
                    <!-- Poll options -->
                    <input type="text" placeholder="Option 1">
                    <input type="text" placeholder="Option 2">
                    <button class="add-option">Add Option</button>
                </div>
                <?php
                break;
            default:
                do_action( 'threads_wp_tab_content', $tab );
        }
        ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <button class="submit-button">Submit</button>
    </form>
</div>

<div id="reddit-container" data-thread="posts" data-thread-wrapper data-post_type="all" data-per_page="30">
    <div class="threads-wp-reddit-thread"></div>
    <div class="loading-indicator" style="display: none;">Loading...</div>
</div>
