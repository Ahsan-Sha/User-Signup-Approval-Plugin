<?php

function us_add_admin_menu() {
    add_menu_page('User Approvals', 'User Approvals', 'manage_options', 'user-approvals', 'us_user_approvals_page');
}

add_action('admin_menu', 'us_add_admin_menu');

function us_user_approvals_page() {
    $args = array(
        'meta_key' => 'account_status',
        'meta_value' => 'pending',
    );

    $pending_users = get_users($args);

    ?>
    <div class="wrap">
        <h1>User Approvals</h1>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column column-columnname" scope="col">Username</th>
                    <th class="manage-column column-columnname" scope="col">Email</th>
                    <th class="manage-column column-columnname" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_users as $user) : ?>
                    <tr>
                        <td><?php echo $user->user_login; ?></td>
                        <td><?php echo $user->user_email; ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>">
                                <input type="submit" name="approve_user" value="Approve" class="button button-primary">
                            </form>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="user_id" value="<?php echo $user->ID; ?>">
                                <input type="submit" name="deny_user" value="Deny" class="button button-secondary">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function us_handle_user_approval() {
    if (isset($_POST['approve_user'])) {
        $user_id = intval($_POST['user_id']);
        update_user_meta($user_id, 'account_status', 'approved');
        
        // Notify user about approval
        $user = get_userdata($user_id);
        wp_mail($user->user_email, 'Account Approved', 'Your account has been approved. You can now log in.');
    }

    if (isset($_POST['deny_user'])) {
        $user_id = intval($_POST['user_id']);
        wp_delete_user($user_id);
    }
}

add_action('admin_init', 'us_handle_user_approval');