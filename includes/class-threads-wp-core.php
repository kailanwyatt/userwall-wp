<?php
class Threads_WP_Post_Core {

    public function hooks() {
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'template_loader'));
    }

    // Method to add rewrite rules
    public function add_rewrite_rules() {
        add_rewrite_rule('^u/([^/]*)/thread/([0-9]+)?', 'index.php?username=$matches[1]&thread_id=$matches[2]', 'top');

        add_rewrite_rule('^u/([^/]*)/threads/?$', 'index.php?username=$matches[1]&all_threads=1', 'top');

        add_rewrite_rule('^u/([^/]*)/?$', 'index.php?username=$matches[1]&user_profile=1', 'top');
    }

    public function register_query_vars($vars) {
        $vars[] = 'username';
        $vars[] = 'thread_id';
        $vars[] = 'all_threads';
        $vars[] = 'user_profile';
        return $vars;
    }

    // Method to load custom template
    public function template_loader() {
        $username = get_query_var('username');
        $thread_id = get_query_var('thread_id');
        $username = get_query_var('username');
        $all_threads = get_query_var('all_threads');
        $user_profile = get_query_var('user_profile');

        if ($username) {
            $user = get_user_by('login', $username);
            if ( ! $user ) {
                // User does not exist, redirect to 404 page
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit;
            }
        }

        if ( $username && $thread_id ) {            
            if ($user) {
                // User exists, load custom template file
                include(get_template_directory() . '/custom-template.php');
                exit;
            }
        }

        if ($username && $all_threads) {
            // Load template for all threads of a user
            include(get_template_directory() . '/all-threads-template.php');
            exit;
        }

        if ($username && $user_profile) {
            // Load template for user profile
            include(get_template_directory() . '/user-profile-template.php');
            exit;
        }
    }
}

$thread_wp_core = new Threads_WP_Post_Core();
$thread_wp_core->hooks();