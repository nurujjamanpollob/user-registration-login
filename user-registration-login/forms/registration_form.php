<?php


/**
 * Add shortcode for a registration form
 */
add_shortcode('register_form', 'registration_form');
// add action hook
add_action('init', 'add_new_user');

// add wp_ajax rest api to dynamically fetch nonce at form submission
add_action('wp_ajax_nopriv_generate_registration_csrf_token', 'generate_registration_csrf_token_ajax');
add_action('wp_ajax_generate_registration_csrf_token', 'generate_registration_csrf_token_ajax');
function generate_registration_csrf_token_ajax()
{
    echo wp_create_nonce('registration-csrf'); // Generate a fresh nonce.
    wp_die(); // Properly terminate the AJAX request.
}

/**
 * This method creates the user registration form
 */
function registration_form()
{

    require_once plugin_dir_path(__FILE__) . '../utilities/privilage_check.php';

    // Only enqueue script if shortcode is rendered
    wp_enqueue_script(
        'ureglogin-registration-form',
        plugin_dir_url(__FILE__) . '../assets/js/registration-form.js',
        array('jquery'),
        null,
        true
    );
    wp_localize_script('ureglogin-registration-form', 'registrationFormAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    if (user_has_edit_page_and_post_privileges()) {
        return registration_fields(true);
    } else {

        // if user logged not logged in
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

}

function registration_fields($previewing = false): string
{

    ob_start(); ?>

    <?php

    // show reg errors
    register_messages();


    ?>


    <form id="urlreglogin_registration_form" class="form" action="" method="POST">

        <fieldset style="border: 0">

            <?php if ($previewing && is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Previewing the registration form, this text will be hidden when you left editing.') ?></p>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_username">
                        <div class="text"><?php _e('Username') ?></div>
                    </label>
                    <input type="text" id="ureglogin_username" name="ureglogin_username" value=""/>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_email">
                        <div class="text"><?php _e('Email') ?></div>
                    </label>

                    <input type="text" id="ureglogin_email" name="ureglogin_email" value="" />
                </div>

                <!-- include recaptcha if the recaptcha test is passed -->
                <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                    <div class="input-container">
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                    </div>
                <?php } ?>

                <p>
                    <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('registration-csrf'); ?>"/>

                    <button type="ureglogin_submit" name="ureglogin_submit" class="submit-button"><?php _e('Create Account'); ?></button>

                </p>

            <?php } ?>

            <?php if ($previewing && !is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Cannot register for an user account because you are already logged in!') ?></p>
                </div>
            <?php } ?>

    <?php if (!$previewing) { ?>

            <div class="input-container">
                <label class="label" for="ureglogin_username">
                    <div class="text"><?php _e('Username') ?></div>
                </label>
                <input type="text" id="ureglogin_username" name="ureglogin_username" value=""/>
            </div>

            <div class="input-container">
                <label class="label" for="ureglogin_email">
                    <div class="text"><?php _e('Email') ?></div>
                </label>

                <input type="text" id="ureglogin_email" name="ureglogin_email" value="" />
            </div>

            <!-- include recaptcha if the recaptcha test is passed -->
            <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                <div class="input-container">
                    <div class="g-recaptcha"
                         data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                </div>
            <?php } ?>

            <p>
                <input type="hidden" name="_csrf" value=""/>

                <button type="ureglogin_submit" name="ureglogin_register_submit" class="submit-button"><?php _e('Create Account'); ?></button>

            </p>

    <?php } ?>


        </fieldset>

    </form>

    <?php
    return ob_get_clean();

}

// handle registration form submission
function add_new_user()
    {
        if (isset($_POST['ureglogin_username']) && isset($_POST['ureglogin_email']) && isset($_POST['_csrf']) && !is_user_logged_in()) {

            // verify nonce
            if (!wp_verify_nonce($_POST['_csrf'], 'registration-csrf')) {
                die('Security check failed');
            }

            require_once plugin_dir_path(__FILE__) . '../utilities/recaptcha_verify.php';

            // get recaptcha response
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;

            // verify recaptcha
            $recaptcha_verified = test_recaptcha_submission_with_site_options($recaptcha_response);

            if (!$recaptcha_verified) {
                registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');

                return;
            }


            // sanitize user input
            $username = sanitize_user($_POST['ureglogin_username'], true);
            $email = sanitize_email($_POST['ureglogin_email']);

            if (!validate_username($username)) {
                registration_login_errors()->add('username_invalid', 'Invalid username');
            }

            if (!is_email($email)) {
                registration_login_errors()->add('email_invalid', 'Invalid email');

                return;
            }


            require_once plugin_dir_path(__FILE__) . '../verifier/verify_blocklisted_username_emails.php';
            $blacklist_verifier = new VerifyBlocklistedUsernameEmails();

            // blacklist test only executed if the setting is enabled and whitelist is disabled
            if (get_option(ENABLE_BLACKLIST_CHECK_OPTION_NAME) === '1' && get_option(ENABLE_WHITELIST_CHECK_OPTION_NAME) !== '1') {

                // check if email domain is blacklisted
                $isBlacklisted = $blacklist_verifier->isEmailBlocklisted($email);

                if ($isBlacklisted) {
                    registration_login_errors()->add('blacklisted', 'Cannot use the username or email provided');
                    return;
                }

            }

            // check if the username is blacklisted
            $isBlacklisted = $blacklist_verifier->isUsernameBlocklisted($username);

            if ($isBlacklisted) {
                registration_login_errors()->add('blacklisted', 'Cannot use the username or email provided');
                return;
            }

            // test if the setting is enabled for disposable email domains
            if (get_option(VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME) === '1') {

                $isBlacklisted = $blacklist_verifier->isDisposableEmail($email);

                if ($isBlacklisted) {
                    registration_login_errors()->add('blacklisted', 'Cannot use this username or email');
                    return;
                }

            }

            // validate user input
            if (username_exists($username)) {

                registration_login_errors()->add('username_exists', 'Username already exists');
            }

            // This is required to create a user
            require_once(ABSPATH . WPINC . '/registration.php');

            // is email exists
            if (email_exists($email)) {
                registration_login_errors()->add('email_exists', 'Email already exists. Cannot register again.');
            }

            $errors = registration_login_errors()->get_error_messages();

            // if no errors, then create user
            if (empty($errors)) {

                // check if the whitelist feature is enabled
                if (get_option(ENABLE_WHITELIST_CHECK_OPTION_NAME) === '1') {
                    // check if the email domain is whitelisted
                    require_once plugin_dir_path(__FILE__) . '../verifier/verify_whitelisted_email_domains.php';

                    $whitelist_verifier = new VerifyWhitelistedEmailDomains();

                    $isWhitelisted = $whitelist_verifier->isEmailWhitelisted($email);

                    if (!$isWhitelisted) {
                        registration_login_errors()->add('email_not_whitelisted', 'Cannot use this username or email');
                        return;
                    }

                }

                $password = wp_generate_password();

                // insert user
                $default_new_user = array(
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_email' => $email,
                    'user_registered' => date('Y-m-d H:i:s'),
                    'role' => get_option(USER_ROLE_OPTION_NAME)
                );

                $user_id = wp_insert_user($default_new_user);

                if ($user_id && !is_wp_error($user_id)) {

                    // send email to admin and user if the option is enabled
                    if (get_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME)) {
                        wp_new_user_notification($user_id, null, 'both');
                    } else {
                        wp_new_user_notification($user_id, null, 'user');
                    }

                    require_once plugin_dir_path(__FILE__) . '../outputs/user_created_successfully_page_output.php';
                    user_created_successfully_page_output($username, $email);
                    exit;
                } else {
                    require_once plugin_dir_path(__FILE__) . '../outputs/user_creation_failed_page_output.php';
                    user_creation_failed_page_output($username, $email);
                }

            }

        }

}


