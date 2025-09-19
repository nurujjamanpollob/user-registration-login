<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function menu_dashboard_content()
{
    ?>
    <div class="wrap">
        <h2>User Registration & Login</h2>
        <p>Welcome to the User Registration & Login plugin. Thanks for using this free plugin by Eaze Web IT. If you need any support, visit to our website: <a target="_blank" href="https://eazewebit.com">eazewebit.com</a> </p>

        This plugin requires google captcha api keys.
        Please go to the settings page to add the keys.
        To create the keys,
        visit <a href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a>
        to create the keys,
        and add them to the settings page. <br><br>

        The Setting page link is <a href="<?php echo admin_url('admin.php?page=user-registration-login-settings'); ?>">here</a>
        <br>
        The Shortcodes page link is <a href="<?php echo admin_url('admin.php?page=user-registration-login-shortcodes'); ?>">here</a>

        Please do note that,
        recaptcha key do not work unless you add api keys
        and perform a successful test here: <a href="<?php echo admin_url('admin.php?page=user-registration-login-recaptcha-test'); ?>">Recaptcha Test</a>
    </div>
    <?php
}