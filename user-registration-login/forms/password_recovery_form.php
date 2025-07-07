<?php

// add shortcode for password recovery form
add_shortcode('password_recovery_form', 'password_recovery_form');

// add ajax rest api to dynamically fetch and set the CSRF token on form submission
// for password recovery form
add_action('wp_ajax_nopriv_generate_password_recovery_csrf_token', 'generate_password_recovery_csrf_token_ajax');
add_action('wp_ajax_generate_password_recovery_csrf_token', 'generate_password_recovery_csrf_token_ajax');

function generate_password_recovery_csrf_token_ajax()
{
    echo wp_create_nonce('password-recovery-csrf'); // Generate a fresh nonce.
    wp_die(); // Properly terminate the AJAX request.
}


/**
 * This method creates the password recovery form
 */

function password_recovery_form(): string
{
    require_once plugin_dir_path(__FILE__) . '../utilities/privilage_check.php';

    // doing this because we don't want to load the script everywhere, only on the login form page
    // ajax nonce generation, problem with caching plugins, so we need to generate a new nonce on each request when a new login form is submitted!
    wp_enqueue_script('ureglogin-password-recovery-form', plugin_dir_url(__FILE__) . '../assets/js/password_recovery_form.js', array('jquery'), null, true);
    wp_localize_script('ureglogin-password-recovery-form', 'pwdRecovery', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));


    if (user_has_edit_page_and_post_privileges()) {
        return password_recovery_fields(true);
    } else {
        // if user logged not logged in
        if (!is_user_logged_in()) {
            return password_recovery_fields();
        } else {
            return 'Cannot recover password while logged in';
        }
    }
}

function password_recovery_fields($previewing = false): string
{


    ob_start(); ?>

    <?php

    // show reg errors
    register_messages();


    ?>


    <form id="ureglogin_password_recovery_form" class="form" action="" method="POST">

        <fieldset style="border: 0">

            <?php if ($previewing && is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Previewing the password recovery form, this text will be hidden when you left editing.') ?></p>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_username-password-reset">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                    <input type="text" id="ureglogin_username-password-reset" name="ureglogin_username-password-reset"
                           value=""/>
                </div>

                <!-- include recaptcha if the recaptcha test is passed -->
                <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                    <div class="input-container">
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                    </div>
                <?php } ?>

                <p>
                    <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('password-recovery-csrf'); ?>"/>
                    <button type="submit" name="submit" class="submit-button"><?php _e('Recover Password'); ?></button>
                </p>

            <?php } ?>

            <?php if ($previewing && !is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Cannot recover password because you are already logged in!') ?></p>
                </div>
            <?php } ?>

            <?php if (!$previewing) { ?>
                <div class="input-container">
                    <label class="label" for="ureglogin_username-password-reset">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                    <input type="text" id="ureglogin_username-password-reset" name="ureglogin_username-password-reset"
                           value=""/>
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
                    <button type="submit" name="ureglogin_passcode_recovery_submit" class="submit-button"><?php _e('Recover Password'); ?></button>
                </p>

            <?php } ?>

        </fieldset>
    </form>

    <?php
    return ob_get_clean();

}

/**
 * Handle form submission
 */
function password_recovery_submission()
{

    // check if the form is submitted
    if (isset($_POST['ureglogin_username-password-reset']) && isset($_POST['_csrf']) && !is_user_logged_in()) {

        // check if the nonce is valid
        if (wp_verify_nonce($_POST['_csrf'], 'password-recovery-csrf')) {

            require_once plugin_dir_path(__FILE__) . '../outputs/new_password_set_unsuccessful.php';

            // get the username or email
            $username_or_email = sanitize_text_field($_POST['ureglogin_username-password-reset']);

            require_once plugin_dir_path(__FILE__) . '../utilities/recaptcha_verify.php';

            // get recaptcha response
            $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;

            // verify recaptcha
            $recaptcha_verified = test_recaptcha_submission_with_site_options($recaptcha_response);

            if (!$recaptcha_verified) {
                registration_login_errors()->add('recaptcha_failed', 'Recaptcha verification failed');

                return;
            }


            // check if the user exists
            $user = get_user_by('login', $username_or_email);

            if (!$user) {
                $user = get_user_by('email', $username_or_email);
            }

            if ($user) {

                require_once plugin_dir_path(__FILE__) . '../utilities/send_email_to_user.php';
                require_once plugin_dir_path(__FILE__) . '../outputs/password_reset_request_submit_success_page_output.php';

                // send email to user
                send_password_reset_email($user);

                // output success message
                password_reset_request_submit_success_page_output($user->user_email);

            } else {

                new_password_set_unsuccessful_page_output('Cannot find user with that username or email');

            }
        } else {
            die('Security check failed');
        }
    }
}

// add action to handle password recovery form submission
add_action('init', 'password_recovery_submission');
