<style>
    .threads-wp-admin-posts-wrapper {
        max-width: 600px
    }
    .comment-edit-form,
    .threads-wp-activity-section {
        display: none;
    }
    .filter-container {
        display: flex;
        align-items: center;
    }
    .filter-label {
        margin-right: 10px;
    }
</style>
<div class="wrap">
    <h2><?php esc_html_e('Add Post', 'threads-wp' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="new_post"><?php esc_html_e('Add Post Details', 'threads-wp' ); ?></label>
            </th>
            <td>
                <div class="threads-wp-admin-posts-wrapper">
                    <?php echo do_shortcode( '[threads_wp_post_form show_threads="false"]'); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="posts"><?php esc_html_e('Posts', 'threads-wp' ); ?></label>
            </th>
            <td>
            <div class="filter-container">
                    <label class="filter-label" for="date-from">Date From:</label>
                    <input type="text" id="date-from" class="date-picker small-text">
                    
                    <label class="filter-label" for="date-end">Date End:</label>
                    <input type="text" id="date-end" class="date-picker small-text">
                    
                    <label class="filter-label" for="search">Search:</label>
                    <input type="text" id="search">
                    
                    <label class="filter-label" for="user">User:</label>
                    <select id="user">
                        <option value="user1">User 1</option>
                        <option value="user2">User 2</option>
                        <!-- Add more user options as needed -->
                    </select>
                    
                    <label class="filter-label" for="sort-by">Sort By:</label>
                    <select id="sort-by">
                        <option value="created-desc">Created (DESC)</option>
                        <option value="created-asc">Created (ASC)</option>
                    </select>
                    
                    <button id="apply-filter">Apply Filter</button>
                </div>
                <div class="threads-wp-admin-posts-wrapper">
                    <?php echo do_shortcode( '[threads_wp_post_form show_form="false"]'); ?>
                </div>
            </td>
        </tr>
</table>
</div>