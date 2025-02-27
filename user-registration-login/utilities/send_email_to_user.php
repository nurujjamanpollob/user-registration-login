<?php


/**
 * Send password reset email to the user
 * @param $user WP_User
 */
function send_password_reset_email(WP_User $user) {
    // create a special link to set new password
    $key = get_password_reset_key($user);

    // send email to user
    $site_url = get_site_url();
    $email = $user->user_email;
    $subject = 'Password Recovery';
    $message = "Hi, \n\n";
    $message .= "You have requested to reset your password. Please click the link below to reset your password: \n\n";
    $message .= "$site_url/wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login) . "\n\n";

    // send email
    wp_mail($email, $subject, $message);

}