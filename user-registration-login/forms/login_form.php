<?php


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
                    <label class="label" for="username">
                        <div class="text"><?php _e('Username/Email') ?></div>
                    </label>
                    <input type="text" id="username" name="username" value="" materialize="true"
                           aria-labelledby="label-fname"/>
                </div>

                <div class="input-container">
                    <label class="label" for="password">
                        <div class="text"><?php _e('Password') ?></div>
                    </label>
                    <input type="password" id="password" name="password" value="" materialize="true"
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
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['_csrf'])) {

        // verify nonce
        if (!wp_verify_nonce($_POST['_csrf'], 'login-csrf')) {
            die('Security check failed');
        }

        // get the recaptcha option value
        getTheRecaptchaOptionValue();

        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            registration_login_errors()->add('login_failed', 'Username or password is incorrect');
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