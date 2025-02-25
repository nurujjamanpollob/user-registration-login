<?php


/**
 * Add shortcode for a registration form
 */
add_shortcode('register_form', 'registration_form');
// add action hook
add_action('init', 'add_new_user');

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

                <input type="text" id="email" name="email" value="" materialize="true"
                       aria-labelledby="label-fname"/>
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
        if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['_csrf'])) {

            // verify nonce
            if (!wp_verify_nonce($_POST['_csrf'], 'registration-csrf')) {
                die('Security check failed');
            }

            // get the recaptcha option value
            getTheRecaptchaOptionValue();

            // sanitize user input
            $username = sanitize_user($_POST['username'], true);
            $email = sanitize_email($_POST['email']);

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

                // check if username or email is blacklisted
                require_once plugin_dir_path(__FILE__) . '../verifier/verify_blocklisted_username_emails.php';

                $blacklist_verifier = new VerifyBlocklistedUsernameEmails();
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

            $password = wp_generate_password();

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

/**
 * @return void
 */
function getTheRecaptchaOptionValue(): void
{
    $recaptcha_verified = get_option(RECAPTCHA_VERIFIED_OPTION_NAME);

    if ($recaptcha_verified) {


        if(!isset($_POST['g-recaptcha-response'])) {
            registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');

            return;
        }
        // verify recaptcha

        $recaptcha_response = $_POST['g-recaptcha-response'];


        // test if recaptcha_response is empty
        if (empty($recaptcha_response)) {
            registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');
        } else {

            $recaptcha_verify = new RecaptchaVerify();
            $is_test_successful = $recaptcha_verify->verifyResponse($recaptcha_response);

            if (!$is_test_successful) {
                registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');
            }
        }
    }
}
