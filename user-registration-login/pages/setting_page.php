<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// add top level menu
add_action('admin_menu', 'registration_login_menu');

// include once the menu_dashboard_content.php file
require_once 'menu_dashboard_content.php';

function registration_login_menu()
{
    //add_submenu_page('options-general.php', 'User Registration & Login', 'User Registration & Login', 'manage_options', REGISTRATION_LOGIN_MENU_SLUG, 'registration_login_page');

    // add menu page
    add_menu_page('User Registration & Login', 'User Login & Registration', 'manage_options', DASHBOARD_PAGE_SLUG, 'menu_dashboard_content');

    // add sub menu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Settings', 'Settings', 'manage_options', REGISTRATION_LOGIN_MENU_SETTINGS_SLUG, 'registration_login_page');

    //add sub menu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Shortcodes', 'Shortcodes', 'manage_options', SHORTCODES_PAGE_SLUG, 'showShortCodes');

    //call register settings function
    add_action( 'admin_init', 'register_user_login_settings' );
}

function register_user_login_settings() {
    //register our settings
    register_setting( 'user-login-settings-group', 'recaptcha_site_key' );
    register_setting( 'user-login-settings-group', 'recaptcha_secret_key' );
}


function registration_login_page()
{
    ?>
    <div class="wrap">
        <h2>User Registration & Login Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'user-login-settings-group' ); ?>
            <?php do_settings_sections( 'user-login-settings-group' ); ?>



            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Recaptcha Site Key</th>
                    <td><input type="text" name="recaptcha_site_key" value="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Recaptcha Secret Key</th>
                    <td><input type="text" name="recaptcha_secret_key" value="<?php echo get_option(RECAPTCHA_SECRET_KEY_OPTION_NAME); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


