<?php

function us_show_signup_form() {
    if (is_user_logged_in()) {
        return'This User already exists!';        
    }else{
        $returndata = '';
        $returndata .= '<form id="us_signup_form" method="post">';
        $returndata .= '<label for="username">Username:</label>';
        $returndata .= '<input type="text" name="username" id="username" required>';
        $returndata .= '<label for="email">Email:</label>';
        $returndata .= '<input type="email" name="email" id="email" required>';
        $returndata .= '<label for="password">Password:</label>';
        $returndata .= '<input type="password" name="password" id="password" required>';
        $returndata .= '<input type="submit" name="us_signup_submit" value="Register">';
        $returndata .= '</form>';
        return $returndata;
    }
}
add_shortcode('user_signup_form', 'us_show_signup_form');

function us_handle_signup() {
    if (isset($_POST['us_signup_submit'])) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $user_id = wp_create_user($username, $password, $email);

        if (!is_wp_error($user_id)) {
            // Update user status to pending
            update_user_meta($user_id, 'account_status', 'pending');
            
            // Send email to admin for approval
            $admin_email = get_option('admin_email');
            $subject = 'New User Registration Pending Approval';
            $message = "A new user has registered and is pending approval.\n\nUsername: $username\nEmail: $email";
            wp_mail($admin_email, $subject, $message);
            
            // Notify user about approval process
            wp_mail($email, 'Registration Successful', 'Your registration is successful. Please wait for admin approval.');
            
            echo 'Registration successful! Please wait for admin approval.';
        } else {
            echo 'Error: ' . $user_id->get_error_message();
        }
    }
}

add_action('init', 'us_handle_signup');