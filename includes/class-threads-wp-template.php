<?php
class Threads_Template {
    // Get a template from the plugin's templates folder
    public static function get_template($template_name) {
        // Check if the template file exists in the theme folder
        $theme_template = locate_template('threads-wp/' . $template_name);

        // If the template file exists in the theme folder, use it
        if ($theme_template) {
            return $theme_template;
        }

        // If not found in the theme, use the plugin's default template
        return THREADS_WP_PLUGIN_DIR . 'templates/' . $template_name;
    }
}