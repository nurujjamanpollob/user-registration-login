<?php

/*
 * Plugin Name:       User Registration & Login
 * Plugin URI:        https://eazewebit.com
 * Description:       This plugin allows you to show WordPress user registration form, login form and user profile in the frontend of your website.
 * Version:           1.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Eaze Web IT
 * Author URI:        https://eazewebit.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://eazewebit.com/user-registration-login-wordpress-plugin/
 * Text Domain:       user-registration-login
 */

include(plugin_dir_path(__FILE__) . 'plugin_options.php');

// include the recaptcha verifier
require_once plugin_dir_path(__FILE__) . 'verifier/recaptcha_verify.php';

if (is_admin()) {
    include_once(plugin_dir_path(__FILE__) . '/pages/setting_page.php');
    include_once(plugin_dir_path(__FILE__) . '/pages/show_shortcodes.php');
    include_once(plugin_dir_path(__FILE__) . '/pages/recaptcha_test.php');

}

// listen on plugin activation
register_activation_hook(__FILE__, 'user_registration_login_on_plugin_activated');


// method that runs on plugin activation
function user_registration_login_on_plugin_activated()
{
    // add options to the database
    add_option(RECAPTCHA_SITE_KEY_OPTION_NAME, RECAPTCHA_SITE_KEY, '', true);
    add_option(RECAPTCHA_SECRET_KEY_OPTION_NAME, RECAPTCHA_SECRET_KEY, '', true);
    add_option(USER_ROLE_OPTION_NAME, 'subscriber');
    add_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME, false);
    add_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME, true, '', true);

    // set transient to redirect to settings page
    set_transient('registration_login_activation_redirect', true, 30);


}


/**
 * Initialize and register the CSS and js files
 */
function register_plugin_assets()
{

    if (get_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME)) {
        wp_register_style('registration-login-css', plugin_dir_url(__FILE__) . 'assets/css/user_registration_login_form_styles.css');
        wp_register_script('registration-login-js', plugin_dir_url(__FILE__) . 'assets/js/user_registration_login.js');
        wp_register_script('minimal-materialize-dialog', plugin_dir_url(__FILE__) . 'assets/js/dialog.js');
        wp_enqueue_style('registration-login-css');
        wp_enqueue_script('registration-login-js');
        wp_enqueue_script('minimal-materialize-dialog');
        wp_enqueue_style('dm-sans', 'https://fonts.googleapis.com/css?family=DM Sans');
    }

    wp_register_script("recaptcha", "https://www.google.com/recaptcha/api.js?explicit&hl=" . get_locale());
    wp_enqueue_script("recaptcha");

}

add_action('wp_enqueue_scripts', 'register_plugin_assets');

/**
 * Add shortcode for a registration form
 */
add_shortcode('register_form', 'registration_form');

/**
 * This method creates the user registration form
 */
function registration_form()
{

    // if user isn't logged in
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

        <fieldset style="border: 0">

            <div class="input-container">
                <label class="label" for="username">
                    <div class="text"><?php _e('Username') ?></div>
                </label>
                <input type="text" id="username" name="username" value="" materialize="true"
                       aria-labelledby="label-fname"/>
            </div>

            <div class="input-container">
                <label class="label" for="email">
                    <div class="text"><?php _e('Email') ?></div>
                </label>

                <input type="text" id="email" name="email" value="" materialize="true" aria-labelledby="label-fname"/>
            </div>

            <div class="input-container">
                <div class="g-recaptcha" data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
            </div>

            <p>
                <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('registration-csrf'); ?>"/>

                <button type="submit" name="submit" class="submit-button"><?php _e('Create Account'); ?></button>

            </p>


        </fieldset>

    </form>

    <?php
    return ob_get_clean();

}

// handle registration form submission
function add_new_user()
{
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['_csrf']) && isset($_POST['g-recaptcha-response'])) {

        // verify nonce
        if (!wp_verify_nonce($_POST['_csrf'], 'registration-csrf')) {
            die('Security check failed');
        }

        // verify recaptcha
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_verify = new RecaptchaVerify();
        $is_test_successful = $recaptcha_verify->verifyResponse($recaptcha_response);

        if (!$is_test_successful) {
            registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');
        }


        // sanitize user input
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = wp_generate_password();

        // This is required to create a user
        require_once(ABSPATH . WPINC . '/registration.php');

        // validate user input
        if (username_exists($username)) {

            registration_login_errors()->add('username_exists', 'Username already exists');
        }

        if (!validate_username($username)) {
            registration_login_errors()->add('username_invalid', 'Invalid username');
        }

        if (!is_email($email)) {
            registration_login_errors()->add('email_invalid', 'Invalid email');
        }

        // is email exists
        if (email_exists($email)) {
            registration_login_errors()->add('email_exists', 'Email already exists. Cannot register again.');
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
                'role' => get_option(USER_ROLE_OPTION_NAME)
            );

            $user_id = wp_insert_user($default_new_user);

            if ($user_id && !is_wp_error($user_id)) {
                // send email to user
                //wp_new_user_notification($user_id, null, 'user');

                // send email to admin and user if the option is enabled
                if (get_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME)) {
                    wp_new_user_notification($user_id, null, 'both');
                } else {
                    wp_new_user_notification($user_id, null, 'user');
                }

                require_once plugin_dir_path(__FILE__) . 'outputs/user_created_successfully_page_output.php';
                user_created_successfully_page_output($username, $email);
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
    static $user_reg_login_errors; // global variable

    return $user_reg_login_errors ?? ($user_reg_login_errors = new WP_Error(null, null, null));
}

// display error messages
function register_messages()
{

    if ($codes = registration_login_errors()->get_error_codes()) {
        // echo '<div class="error_div">';

        // create a string of error messages
        $error_messages = '<div style="display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: center;" class="error_div">';

        // add image to error message
        $error_messages .= '<img src="' . plugin_dir_url(__FILE__) . 'assets/img/error_404.webp" style="width: 100%; height: 40%; margin-bottom: 20px;"/>';
        // Loop error codes and display errors
        foreach ($codes as $code) {
            $message = registration_login_errors()->get_error_message($code);
            // echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
            $error_messages .= '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span>';
        }
        echo '</div>';
        $error_messages .= '</div>';

        echo "<script>createAndShowDialog('', '$error_messages', null, [{text: 'Cancel', onClick: () => {closeDialog();}}]);</script>";

    }

}

// redirect to settings page after activation
function registration_login_redirect_to_settings_page()
{


    if (!get_transient('registration_login_activation_redirect')) {
        return;
    }

    delete_transient('registration_login_activation_redirect');
    if (isset($_GET['activate-multi'])) {
        return;
    }
    wp_safe_redirect(admin_url('admin.php?page=' . DASHBOARD_PAGE_SLUG));
}


//add action when admin_init
add_action('admin_init', 'registration_login_redirect_to_settings_page');


// add settings page link to plugin page
function registration_login_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=' . REGISTRATION_LOGIN_MENU_SETTINGS_SLUG . '">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'registration_login_settings_link');

// add shortcodes page link to plugin page
function registration_login_shortcodes_link($links)
{
    $shortcodes_link = '<a href="admin.php?page=' . SHORTCODES_PAGE_SLUG . '">' . __('Shortcodes') . '</a>';
    array_unshift($links, $shortcodes_link);
    return $links;
}

add_filter("plugin_action_links_$plugin", 'registration_login_shortcodes_link');

// create login form shortcode
add_shortcode('login_form', 'login_form');

function login_form()
{
    if (!is_user_logged_in()) {
        ob_start();

        // show login errors
        register_messages();

        ?>
        <form id="login_form" class="form" action="" method="POST">
            <fieldset style="border: 0">
                <div class="input-container">
                    <input type="text" id="username" name="username" value="" materialize="true"
                           aria-labelledby="label-fname"/>
                    <label class="label" for="username">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                </div>

                <div class="input-container">
                    <input type="password" id="password" name="password" value="" materialize="true"
                           aria-labelledby="label-fname"/>
                    <label class="label" for="password">
                        <div class="text"><?php _e('Password') ?></div>
                    </label>
                </div>

                <div class="input-container">
                    <div class="g-recaptcha" data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                </div>

                <p>
                    <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('login-csrf'); ?>"/>
                    <button type="submit" name="submit" class="submit-button"><?php _e('Login'); ?></button>
                </p>
            </fieldset>
        </form>
        <?php
        return ob_get_clean();
    } else {
        return 'You are already logged in';
    }
}

// handle login form submission
function login_user()
{
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['_csrf']) && isset($_POST['g-recaptcha-response'])) {

        // verify nonce
        if (!wp_verify_nonce($_POST['_csrf'], 'login-csrf')) {
            die('Security check failed');
        }

        // verify recaptcha
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_verify = new RecaptchaVerify();
        $is_test_successful = $recaptcha_verify->verifyResponse($recaptcha_response);

        if (!$is_test_successful) {
            registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');
        }

        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            registration_login_errors()->add('login_failed', 'Login failed');
        }

        $errors = registration_login_errors()->get_error_messages();

        if (empty($errors)) {
            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID);
            do_action('wp_login', $user->user_login, $user);
            wp_redirect(home_url());
            exit;
        }

    }
}

add_action('init', 'login_user');


