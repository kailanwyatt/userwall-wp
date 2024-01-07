<?php

// Define the activation function
function threads_wp_activate() {
    global $wpdb;

    // Define the table names with the "threads_" prefix
    $table_posts = $wpdb->prefix . 'threads_posts';
    $table_comments = $wpdb->prefix . 'threads_comments';
    $table_likes = $wpdb->prefix . 'threads_likes';
    $table_bookmarks = $wpdb->prefix . 'threads_bookmarks';
    $table_groups = $wpdb->prefix . 'threads_groups';
    $table_polls = $wpdb->prefix . 'threads_polls';
    $table_poll_votes = $wpdb->prefix . 'threads_poll_votes';
    $table_media = $wpdb->prefix . 'threads_media';
    $table_albums = $wpdb->prefix . 'threads_albums';
    $table_reports = $wpdb->prefix . 'threads_reports';
    $table_user_reputation = $wpdb->prefix . 'threads_user_reputation';
    $table_badges = $wpdb->prefix . 'threads_badges';
    $table_hashtags = $wpdb->prefix . 'threads_hashtags';
    $table_user_settings = $wpdb->prefix . 'threads_user_settings';
    $table_notifications = $wpdb->prefix . 'threads_notifications';
    $table_search_history = $wpdb->prefix . 'threads_search_history';
    $table_user_followers = $wpdb->prefix . 'threads_user_followers';
    $table_user_following = $wpdb->prefix . 'threads_user_following';
    $table_reports = $wpdb->prefix . 'threads_reports';
    $table_user_notifications = $wpdb->prefix . 'threads_user_notifications';
    $table_poll_options = $wpdb->prefix . 'threads_poll_options';

    // SQL query to create the 'threads_plugin_posts' table
    $sql_query_posts = "CREATE TABLE IF NOT EXISTS $table_posts (
        post_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        post_content TEXT,
        post_type VARCHAR(20) NOT NULL,
        creation_date DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";
    
    // SQL query to create the 'threads_plugin_comments' table
    $sql_query_comments = "CREATE TABLE IF NOT EXISTS $table_comments (
        comment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        comment_content TEXT,
        comment_date DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id INT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";
    
    // SQL query to create the 'threads_plugin_likes' table
    $sql_query_likes = "CREATE TABLE IF NOT EXISTS $table_likes (
        like_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id INT UNSIGNED NOT NULL,
        reaction_type VARCHAR(20) NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";
    
    // SQL query to create the 'threads_plugin_bookmarks' table
    $sql_query_bookmarks = "CREATE TABLE IF NOT EXISTS $table_bookmarks (
        bookmark_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id INT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";
    
    // SQL query to create the 'threads_plugin_groups' table
    $sql_query_groups = "CREATE TABLE IF NOT EXISTS $table_groups (
        group_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        group_name VARCHAR(255) NOT NULL,
        group_description TEXT,
        creation_date DATETIME NOT NULL,
        creator_user_id BIGINT UNSIGNED NOT NULL,
        INDEX creator_user_id_index (creator_user_id),
        FOREIGN KEY (creator_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";
    
    // SQL query to create the 'threads_polls' table
    $sql_query_polls = "CREATE TABLE IF NOT EXISTS $table_polls (
        poll_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        question_text TEXT NOT NULL,
        creation_date DATETIME NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        post_id INT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX post_id_index (post_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
    )";

    // SQL query to create the 'threads_poll_votes' table
$sql_query_poll_votes = "CREATE TABLE IF NOT EXISTS $table_poll_votes (
    vote_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    poll_id INT UNSIGNED NOT NULL,
    selected_option INT NOT NULL,
    INDEX user_id_index (user_id),
    INDEX poll_id_index (poll_id),
    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
    FOREIGN KEY (poll_id) REFERENCES $table_polls(poll_id)
)";

// SQL query to create the 'threads_media' table
$sql_query_media = "CREATE TABLE IF NOT EXISTS $table_media (
    media_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_path VARCHAR(255) NOT NULL,
    description TEXT,
    post_id INT UNSIGNED NOT NULL,
    INDEX post_id_index (post_id),
    FOREIGN KEY (post_id) REFERENCES $table_posts(post_id)
)";

// SQL query to create the 'threads_reports' table
$sql_query_reports = "CREATE TABLE IF NOT EXISTS $table_reports (
    report_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_user_id BIGINT UNSIGNED NOT NULL,
    reported_content_id INT UNSIGNED NOT NULL,
    report_reason TEXT NOT NULL,
    report_date DATETIME NOT NULL,
    INDEX reporter_user_id_index (reporter_user_id),
    INDEX reported_content_id_index (reported_content_id),
    FOREIGN KEY (reporter_user_id) REFERENCES {$wpdb->prefix}users(ID),
    FOREIGN KEY (reported_content_id) REFERENCES $table_posts(post_id)
)";

// SQL query to create the 'threads_user_reputation' table
$sql_query_user_reputation = "CREATE TABLE IF NOT EXISTS $table_user_reputation (
    reputation_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    reputation_score INT NOT NULL,
    INDEX user_id_index (user_id),
    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
)";

    // SQL query to create the 'wp_threads_hashtags' table
    $sql_query_hashtags = "CREATE TABLE IF NOT EXISTS $table_hashtags (
        hashtag_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        hashtag_text VARCHAR(255) NOT NULL,
        INDEX hashtag_text_index (hashtag_text)
    )";

    // SQL query to create the 'wp_threads_user_settings' table
    $sql_query_user_settings = "CREATE TABLE IF NOT EXISTS $table_user_settings (
        setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_preferences TEXT,
        privacy_settings TEXT,
        display_options TEXT,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_notifications' table
    $sql_query_notifications = "CREATE TABLE IF NOT EXISTS $table_notifications (
        notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        notification_type VARCHAR(255) NOT NULL,
        sender_user_id BIGINT UNSIGNED NOT NULL,
        receiver_user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT,
        timestamp DATETIME NOT NULL,
        INDEX sender_user_id_index (sender_user_id),
        INDEX receiver_user_id_index (receiver_user_id),
        FOREIGN KEY (sender_user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (receiver_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_search_history' table
    $sql_query_search_history = "CREATE TABLE IF NOT EXISTS $table_search_history (
        search_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        search_query VARCHAR(255) NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    $sql_query_user_followers = "CREATE TABLE IF NOT EXISTS $table_user_followers (
        follower_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        follower_user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX follower_user_id_index (follower_user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (follower_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_user_following' table
    $sql_query_user_following = "CREATE TABLE IF NOT EXISTS $table_user_following (
        following_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        following_user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX following_user_id_index (following_user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (following_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_user_notifications' table
    $sql_query_user_notifications = "CREATE TABLE IF NOT EXISTS $table_user_notifications (
        notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_poll_options' table
    $sql_query_poll_options = "CREATE TABLE IF NOT EXISTS $table_poll_options (
        option_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        poll_id INT UNSIGNED NOT NULL,
        option_text TEXT NOT NULL,
        INDEX poll_id_index (poll_id),
        FOREIGN KEY (poll_id) REFERENCES wp_threads_polls(poll_id)
    )";


    // SQL query to create the 'threads_reports' table
    $sql_query_reports = "CREATE TABLE IF NOT EXISTS $table_reports (
        report_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        reporter_user_id BIGINT UNSIGNED NOT NULL,
        reported_content_id INT UNSIGNED NOT NULL,
        report_reason TEXT NOT NULL,
        report_date DATETIME NOT NULL,
        INDEX reporter_user_id_index (reporter_user_id),
        INDEX reported_content_id_index (reported_content_id),
        FOREIGN KEY (reporter_user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (reported_content_id) REFERENCES $table_posts(post_id)
    )";

    // SQL query to create the 'wp_threads_hashtags' table
    $sql_query_hashtags = "CREATE TABLE IF NOT EXISTS $table_hashtags (
        hashtag_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        hashtag_text VARCHAR(255) NOT NULL,
        INDEX hashtag_text_index (hashtag_text)
    )";

    // SQL query to create the 'wp_threads_user_settings' table
    $sql_query_user_settings = "CREATE TABLE IF NOT EXISTS $table_user_settings (
        setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_preferences TEXT,
        privacy_settings TEXT,
        display_options TEXT,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_notifications' table
    $sql_query_notifications = "CREATE TABLE IF NOT EXISTS $table_notifications (
        notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        notification_type VARCHAR(255) NOT NULL,
        sender_user_id BIGINT UNSIGNED NOT NULL,
        receiver_user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT,
        timestamp DATETIME NOT NULL,
        INDEX sender_user_id_index (sender_user_id),
        INDEX receiver_user_id_index (receiver_user_id),
        FOREIGN KEY (sender_user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (receiver_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_search_history' table
    $sql_query_search_history = "CREATE TABLE IF NOT EXISTS $table_search_history (
        search_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        search_query VARCHAR(255) NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    $sql_query_user_followers = "CREATE TABLE IF NOT EXISTS $table_user_followers (
        follower_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        follower_user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX follower_user_id_index (follower_user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (follower_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_user_following' table
    $sql_query_user_following = "CREATE TABLE IF NOT EXISTS $table_user_following (
        following_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        following_user_id BIGINT UNSIGNED NOT NULL,
        INDEX user_id_index (user_id),
        INDEX following_user_id_index (following_user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID),
        FOREIGN KEY (following_user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_user_notifications' table
    $sql_query_user_notifications = "CREATE TABLE IF NOT EXISTS $table_user_notifications (
        notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        notification_content TEXT NOT NULL,
        timestamp DATETIME NOT NULL,
        INDEX user_id_index (user_id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    )";

    // SQL query to create the 'wp_threads_poll_options' table
    $sql_query_poll_options = "CREATE TABLE IF NOT EXISTS $table_poll_options (
        option_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        poll_id INT UNSIGNED NOT NULL,
        option_text TEXT NOT NULL,
        INDEX poll_id_index (poll_id),
        FOREIGN KEY (poll_id) REFERENCES wp_threads_polls(poll_id)
    )";

    $sql_query_badges = "CREATE TABLE IF NOT EXISTS $table_badges (
        badge_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        badge_name VARCHAR(255) NOT NULL,
        badge_description TEXT,
        badge_image_url VARCHAR(255) NOT NULL,
        INDEX badge_name_index (badge_name)
    )";

    // Array of SQL queries for the first 5 tables
    $sql_queries = array(
        $sql_query_posts,
        $sql_query_comments,
        $sql_query_likes,
        $sql_query_bookmarks,
        $sql_query_groups,
        $sql_query_polls,  
        $sql_query_poll_votes,
        $sql_query_media,
        $sql_query_user_reputation,
        $sql_query_badges,
        $sql_query_hashtags,
        $sql_query_user_settings,
        $sql_query_notifications,
        $sql_query_search_history,
        $sql_query_user_followers,
        $sql_query_user_following,
        $sql_query_reports,
        $sql_query_user_notifications,
        $sql_query_poll_options
    );

    // Include the WordPress database upgrade file
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Execute the SQL queries to create the tables
    foreach ($sql_queries as $sql_query) {
        dbDelta($sql_query);
    }
}
