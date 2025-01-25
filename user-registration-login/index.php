<?php

/*
 * Plugin Name:       User Registration & Login
 * Plugin URI:        https://webbylife.com
 * Description:       This plugin allows you to show wordpress user registration form, login form and user profile in the frontend of your website.
 * Version:           1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            WebbyLife Software
 * Author URI:        https://webbylife.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       user-registration-login
 */


/**
 * Add shortcode for registration form
 */

add_shortcode('register_form', 'registration_form');

/**
 * This method creates the user registration form
 */
function registration_form()
{

    // if user not logged in
    if (!is_user_logged_in()) {

        // if registration is enabled
        if (get_option('users_can_register')) {
            $output = registration_fields();
        } else {
            return 'Registration is currently disabled. Contact the site administrator for more information.';
        }

        return $output;
    } else {
        return 'You are already registered and logged in';
    }

}


function registration_fields()
{

    ob_start(); ?>

    <?php

    // show reg errors
    register_messages();


    ?>

    <form id="registration_form" class="form" action="" method="POST">

        <fieldset>

            <p>
                <label for="username"><?php _e('Username') ?></label>
                <input type="text" name="username" id="username" class="input"/>
            </p>

            <p>
                <label for="email"><?php _e('Email') ?></label>
                <input type="text" name="email" id="email" class="input"/>
            </p>

            <p>
                <label for="password"><?php _e('Password') ?></label>
                <input type="password" name="password" id="password" class="input"/>
            </p>

            <p>
                <label for="password2"><?php _e('Confirm Password') ?></label>
                <input type="password" name="password2" id="password2" class="input"/>
            </p>

            <p>
                <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('registration-csrf'); ?>"/>

                <input type="submit" name="submit" value="<?php _e('Register Your Account'); ?>"/>
            </p>


        </fieldset>

    </form>


    <?php
    return ob_get_clean();

}

// handle registration form submission
    function add_new_user()
    {
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password2'])) {

            // verify nonce
            if (!wp_verify_nonce($_POST['_csrf'], 'registration-csrf')) {
                die('Security check failed');
            }

            // sanitize user input
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = esc_attr($_POST['password']);
            $password2 = esc_attr($_POST['password2']);

            // This is required to create a user
            require_once(ABSPATH . WPINC . '/registration.php');

            // validate user input
            if (username_exists($username)) {

                registration_login_errors()->add('username_exists', 'Username already exists');
            }

            if(!validate_username($username)){
                registration_login_errors()->add('username_invalid', 'Invalid username');
            }

            // if username is null or empty
            if (empty($username)) {
                registration_login_errors()->add('username_empty', 'Please enter a username');
            }

            if (!is_email($email)) {
                registration_login_errors()->add('email_invalid', 'Invalid email');
            }

            // is email exists
            if (email_exists($email)) {
                registration_login_errors()->add('email_exists', 'Email already exists. Cannot register again.');
            }

            // if user pass empty of null, or less than 6 characters
            if (empty($password) || strlen($password) < 6) {
                registration_login_errors()->add('password_empty', 'Password must be at least 6 characters long');
            }

            if ($password != $password2) {
                registration_login_errors()->add('password_mismatch', 'Passwords do not match');
            }

            $errors = registration_login_errors()->get_error_messages();

            // if no errors, then create user
            if (empty($errors)) {

                // insert user
                $default_new_user = array(
                    'user_login' => $username,
                    'user_pass' => wp_hash_password($password),
                    'user_email' => $email,
                    'user_registered' => date('Y-m-d H:i:s'),
                    'role' => 'subscriber'
                );

                $user_id = wp_insert_user($default_new_user);

                if ($user_id && !is_wp_error($user_id)) {
                    // send email to user
                    wp_new_user_notification($user_id, null, 'user');
                    echo 'User created successfully';

                    exit;
                } else {
                    echo 'Error creating user';
                }

            }

        }
    }

    // add action hook
    add_action('init', 'add_new_user');

    // function for tracking error messages
    function registration_login_errors()
    {
        static  $user_reg_login_errors; // global variable

        return $user_reg_login_errors ?? ($user_reg_login_errors = new WP_Error(null, null, null));
    }

    // display error messages
        function register_messages()
        {

            if ($codes = registration_login_errors()->get_error_codes()) {
                echo '<div class="error_div">';
                // Loop error codes and display errors
                foreach ($codes as $code) {
                    $message = registration_login_errors()->get_error_message($code);
                    echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
                }
                echo '</div>';
            }

        }
