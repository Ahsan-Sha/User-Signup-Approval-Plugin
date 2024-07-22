<?php
/*
Plugin Name: User Signup Approval
Description: Custom plugin for user signup with admin approval.
Version: 1.0
Author: SMK
*/

// Security check to prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue the custom styles
function us_enqueue_styles() {
    wp_enqueue_style('us_signup_styles', plugin_dir_url(__FILE__) . 'assets/style.css');
}
add_action('wp_enqueue_scripts', 'us_enqueue_styles');

// Include necessary files
include_once(plugin_dir_path(__FILE__) . 'includes/user-signup-form.php');
include_once(plugin_dir_path(__FILE__) . 'includes/admin-approval.php');

function us_restrict_pending_users() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $account_status = get_user_meta($user_id, 'account_status', true);

        if ($account_status == 'pending') {
            wp_logout();
            wp_redirect(home_url());
            exit;
        }
    }
}

add_action('init', 'us_restrict_pending_users');

?>