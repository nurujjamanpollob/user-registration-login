<?php


add_shortcode('login_form', 'login_form');
add_action('init', 'login_user');

function login_form()
{


    require_once plugin_dir_path(__FILE__) . '../utilities/privilage_check.php';

    if (user_has_edit_page_and_post_privileges()) {
        return login_fields(true);
    } else {

        if (!is_user_logged_in()) {
            return login_fields();
        } else {
            return 'You are already logged in';
        }
    }
}

function login_fields($preview = false)
{
    ob_start();

    ?>

    <?php

    // show reg errors
    register_messages();


    ?>

    <form id="ureglogin_login_form" class="form" action="" method="POST">
        <fieldset style="border: 0">

            <?php if ($preview && is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Previewing the login form, this text will be hidden when you left editing.') ?></p>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_username">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                    <input type="text" id="ureglogin_username" name="ureglogin_username" value=""/>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_password">
                        <div class="text"><?php _e('Password') ?></div>
                    </label>
                    <input type="password" id="ureglogin_password" name="ureglogin_password" value=""/>
                </div>

                <!-- include recaptcha if the recaptcha test is passed -->
                <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                    <div class="input-container">
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                    </div>

                <?php } ?>

                <div style="display: flex;" class="input-container">
                    <input style="width: auto; margin-right: 10px;" type="checkbox" id="ureglogin_remember_me"
                           name="ureglogin_remember_me" value="1" materialize="true"
                           aria-labelledby="label-fname"/>
                    <p><?php _e('Remember Me'); ?></p>
                </div>

                <p>
                    <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('login-csrf'); ?>"/>
                    <button type="submit" name="ureglogin_submit" class="submit-button"><?php _e('Login'); ?></button>
                </p>

            <?php } ?>

            <?php if ($preview && !is_page_in_edit_mode($_GET)) { ?>
                <div class="input-container">
                    <p><?php _e('Cannot log in because you are already logged in!') ?></p>
                </div>
            <?php } ?>

            <?php if (!$preview) { ?>


                <div class="input-container">
                    <label class="label" for="ureglogin_username">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                    <input type="text" id="ureglogin_username" name="ureglogin_username" value=""/>
                </div>

                <div class="input-container">
                    <label class="label" for="ureglogin_password">
                        <div class="text"><?php _e('Password') ?></div>
                    </label>
                    <input type="password" id="ureglogin_password" name="ureglogin_password" value=""/>
                </div>

                <!-- include recaptcha if the recaptcha test is passed -->
                <?php if (get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) { ?>
                    <div class="input-container">
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"></div>
                    </div>

                <?php } ?>

                <div style="display: flex;" class="input-container">
                    <input style="width: auto; margin-right: 10px;" type="checkbox" id="ureglogin_remember_me"
                           name="ureglogin_remember_me" value="1" materialize="true"
                           aria-labelledby="label-fname"/>
                    <p><?php _e('Remember Me'); ?></p>
                </div>

                <p>
                    <input type="hidden" name="_csrf" value="<?php echo wp_create_nonce('login-csrf'); ?>"/>
                    <button type="submit" name="ureglogin_submit" class="submit-button"><?php _e('Login'); ?></button>
                </p>
            <?php } ?>

        </fieldset>
    </form>
    <?php
    return ob_get_clean();
}

// handle login form submission
function login_user()
{
    if (isset($_POST['ureglogin_username']) && isset($_POST['ureglogin_password']) && isset($_POST['_csrf']) && !is_user_logged_in()) {

        // verify nonce
        if (!wp_verify_nonce($_POST['_csrf'], 'login-csrf')) {
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


        $username = sanitize_user($_POST['ureglogin_username']);
        $password = $_POST['ureglogin_password'];

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            registration_login_errors()->add('login_failed', 'Username or password is incorrect');
            return;
        }

        $errors = registration_login_errors()->get_error_messages();

        if (empty($errors)) {


            // if remember me is checked
            $is_remember_me_checked = $_POST['ureglogin_remember_me'] === '1';

            if ($is_remember_me_checked) {
                wp_set_auth_cookie($user->ID, true);
            } else {
                wp_set_auth_cookie($user->ID, false);
            }

            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);
            wp_redirect(home_url());
            exit;
        }

    }
}