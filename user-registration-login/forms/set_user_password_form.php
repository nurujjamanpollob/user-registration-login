<?php

/**
 * When this page is loaded, the user is required to set a password.
 * We need to extract this following parameters from the URL:
 * login: the user's login
 * key: the key that was sent to the user's email
 * action: the action that was sent to the user's email
 *
 */

// add shortcode to display the form
add_shortcode('set_user_password_form', 'get_user_set_password_form');
require_once plugin_dir_path(__FILE__) . '../utilities/privilage_check.php';

/**
 * Display the form to set the user's password
 */

function get_user_set_password_form()
{


    $get_404_image_url = plugin_dir_url(__FILE__) . '../assets/img/error_404.webp';

    // get the login, key, and action from the URL
    $login = $_GET['login'] ?? '';
    $key = $_GET['key'] ?? '';
    $action = $_GET['action'] ?? '';



    // test if the user is logged in and can edit pages and posts
    if (user_has_edit_page_and_post_privileges()) {
        return set_user_password_form(true);
    }

    // if user logged in but cannot edit pages and posts
    if (is_user_logged_in() && !current_user_can('edit_pages')) {
        require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_unsuccessful.php';
        new_password_set_unsuccessful_page_output('Cannot set password while logged in. Logout and try again');
        exit;
    }

    // if user not logged in and login, key, and action is set and action value should be 'rp'
    if (!is_user_logged_in() && isset($_GET['login']) && isset($_GET['key']) && isset($_GET['action']) && $_GET['action'] === 'rp') {


        // lets check if the user exists and the key is valid
        $user = check_password_reset_key($key, $login);

        // if the user is not found or the key is invalid
        if (is_wp_error($user)) {
            require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_unsuccessful.php';
            new_password_set_unsuccessful_page_output('User password reset key is invalid!');
            exit;
        }


        return set_user_password_form();
    }

    // return error page
    require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_unsuccessful.php';

    new_password_set_unsuccessful_page_output('Invalid request');

    exit;

}

function set_user_password_form($preview = false)
{
    ob_start();

    register_messages();

    // get the login, key, and action from the URL
    $login_user = $_GET['login'] ?? '';
    $key = $_GET['key'] ?? '';
    $action = $_GET['action'] ?? '';

    ?>



    <form id="set_user_password_form" class="form" action="" method="POST">
        <fieldset style="border: 0">

            <?php if ($preview) { ?>
                <div class="input-container">
                    <p><?php _e('Previewing the set password form, this text will be hidden when you left editing.') ?></p>
                </div>
            <?php } ?>



            <div class="input-container">
                <label class="label" for="ureglogin_username">
                    <div class="text"><?php _e('Resetting Password For') ?></div>
                </label>
                <input style="background: antiquewhite;" type="text" id="ureglogin_username" name="ureglogin_username"
                       value="<?php echo $login_user; ?>" disabled/>
            </div>

            <div class="input-container">
                <label class="label" for="ureglogin_password">
                    <div class="text"><?php _e('Password') ?></div>
                </label>
                <input type="password" id="ureglogin_password" name="ureglogin_password" value=""/>
            </div>

            <div class="input-container">
                <label class="label" for="ureglogin_confirm_password">
                    <div class="text"><?php _e('Confirm Password') ?></div>
                </label>
                <input type="password" id="ureglogin_confirm_password" name="ureglogin_confirm_password" value="" />
            </div>

            <!-- include recaptcha if the recaptcha test is passed -->
            <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                <div class="input-container">
                    <div class="g-recaptcha"
                         data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                </div>
            <?php } ?>

            <p>
                <input type="hidden" name="ureglogin_login" value="<?php echo $login_user; ?>"/>
                <input type="hidden" name="ureglogin_key" value="<?php echo $key; ?>"/>
                <input type="hidden" name="ureglogin_action" value="<?php echo $action; ?>"/>
                <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('set-password-csrf'); ?>"/>
                <button type="submit" name="ureglogin_submit" class="submit-button"><?php _e('Set Password'); ?></button>
            </p>
        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// handle form submission
add_action('init', 'set_user_password');

function set_user_password()
{
    if (
            isset($_POST['ureglogin_password']) &&
            isset($_POST['ureglogin_confirm_password']) &&
            !is_user_logged_in() &&
            isset($_POST['ureglogin_login']) &&
            isset($_POST['ureglogin_key']) &&
            isset($_POST['ureglogin_action']) &&
            isset($_POST['_csrf'])
    ) {


        // check if page is preview
        if (isset($_GET['preview']) && $_GET['preview'] === 'true') {
            // add an error message
            registration_login_errors()->add('preview', 'Cannot set password in preview mode');
            return;
        }

        require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_unsuccessful.php';

        // check if the passwords match
        if ($_POST['ureglogin_password'] !== $_POST['ureglogin_confirm_password']) {

            registration_login_errors()->add('password_mismatch', 'Passwords do not match');

            return;
        }

        $min_password_length = get_option(PASSWORD_MINIMUM_LENGTH_OPTION_NAME);

        // check if the password is meets minimum length
        if (strlen($_POST['ureglogin_password']) < $min_password_length) {
            registration_login_errors()->add('password_length', 'Password must be at least ' . $min_password_length . ' characters long');

            return;
        }


        // verify nonce
        if (!wp_verify_nonce($_POST['_csrf'], 'set-password-csrf')) {
            die('Security check failed');
        }

        // sanitize the password
        $password = sanitize_text_field($_POST['ureglogin_password']);

        require_once plugin_dir_path(__FILE__) . '../utilities/recaptcha_verify.php';

        // get recaptcha response
        $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;

        // verify recaptcha
        $recaptcha_verified = test_recaptcha_submission_with_site_options($recaptcha_response);

        if (!$recaptcha_verified) {
            registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');

            return;
        }

        // set the user's password
        $login = $_POST['ureglogin_login'];
        $key = $_POST['ureglogin_key'];
        $action = $_POST['ureglogin_action'];
        $password = $_POST['ureglogin_password'];

        // verify action
        if ($action === 'rp') {
            // verify the key
            $user = check_password_reset_key($key, $login);

            if (is_wp_error($user)) {
                new_password_set_unsuccessful_page_output('User password reset key is invalid!');
                return;
            }

            // reset the user's password
            reset_password($user, $password);

            require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_successful_page.php';

            new_password_set_successful_page_output($user->user_email);
        } else {
            new_password_set_unsuccessful_page_output('Invalid action');
        }
    }
}












