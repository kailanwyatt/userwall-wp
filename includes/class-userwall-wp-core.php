<?php
class UserWall_WP_Post_Core {

    public function hooks() {
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_filter( 'userwall_wp_profile_tabs', array( $this, 'userwall_wp_profile_tabs'), 10, 1 );
        add_action('template_redirect', array($this, 'template_loader'));
        add_action('userwall_profile_content_main', array($this, 'userwall_profile_content'), 10, 2);
    }

    public function userwall_wp_profile_tabs( $tabs = array() ) {

    }

    public function userwall_profile_content( $profile_id = 0 ) {
        echo do_shortcode('[userwall_wp_post_form type="user-posts" per_page="5" object_id="' . $profile_id . '"]');
    }

    // Method to add rewrite rules
    public function add_rewrite_rules() {
        //add_rewrite_rule('^u/([^/]*)/thread/([0-9]+)?', 'index.php?pagename=u&username=$matches[1]&thread_id=$matches[2]', 'top');

        //add_rewrite_rule('^u/([^/]*)/userwall/?$', 'index.php?pagename=u&username=$matches[1]&all_userwall=1', 'top');

        add_rewrite_rule('^u/([^/]*)/([^/]*)/?$', 'index.php?pagename=u&username=$matches[1]&user_profile=1&profile_tab=[2]', 'top');

        add_rewrite_rule('^u/([^/]*)/?$', 'index.php?pagename=u&username=$matches[1]&user_profile=1', 'top');
    }

    public function register_query_vars($vars) {
        $vars[] = 'username';
        $vars[] = 'thread_id';
        $vars[] = 'user_profile';
        $vars[] = 'profile_tab';
        $vars[] = 'profile_id';
        $vars[] = 'profile';
        return $vars;
    }

    // Method to load custom template
    public function template_loader() {
        global $uwwp_profile_info;
        $username = get_query_var('username');
        $thread_id = get_query_var('thread_id');
        $username = get_query_var('username');
        $all_userwall = get_query_var('all_userwall');
        $user_profile = get_query_var('user_profile');
        $profile_tab = get_query_var('profile_tab');

        if ($username) {
            $user = get_user_by('login', $username);
            if ( ! $user ) {
                // User does not exist, redirect to 404 page
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit;
            } else {
                $uwwp_profile_info = new UserWall_WP_Profile( $user->ID );
                set_query_var('profile', $uwwp_profile_info );
                set_query_var('profile_id', $user->ID );
            }
            return;
        }
    }
}

$thread_wp_core = new UserWall_WP_Post_Core();
$thread_wp_core->hooks();