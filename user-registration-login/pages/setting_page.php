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

    // add sub menu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Recaptcha Test', 'Recaptcha Test', 'manage_options', RECAPTCHA_TEST_PAGE_SLUG, 'recaptcha_test');

    //call register settings function
    add_action( 'admin_init', 'register_user_login_settings' );
}

function register_user_login_settings() {
    //register our settings
    register_setting( 'user-login-settings-group', 'recaptcha_site_key' );
    register_setting( 'user-login-settings-group', 'recaptcha_secret_key' );
    register_setting( 'user-login-settings-group', USER_ROLE_OPTION_NAME);
    register_setting( 'user-login-settings-group', SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME);
    register_setting( 'user-login-settings-group', LOAD_PLUGIN_CSS_JS_OPTION_NAME);
    register_setting( 'user-login-settings-group', BLACKLISTED_USERNAMES_OPTION_NAME);
    register_setting( 'user-login-settings-group', BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME);
    register_setting( 'user-login-settings-group', VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME);
}


function registration_login_page()
{
    ?>
    <div class="wrap">
        <h2>User Registration & Login Settings</h2>

        <p>Use the settings below to customize plugin behaviour. The registration or login form do not work properly, contact us here at: <a href="https://eazewebit.com">eazewebit.com</a> </p>
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

                <tr valign="top">
                    <th scope="row">User Role</th>
                    <td>
                        <select name="<?php echo USER_ROLE_OPTION_NAME; ?>">
                            <?php
                            $roles = get_editable_roles();
                            foreach ($roles as $role => $roleDetails) {
                                ?>
                                <option value="<?php echo $role; ?>" <?php echo get_option(USER_ROLE_OPTION_NAME) === $role ? 'selected' : ''; ?>><?php echo $roleDetails['name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Send Registration Email to Admin</th>
                    <td>
                        <input type="checkbox" name="<?php echo SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME; ?>" value="1" <?php echo get_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Load Plugin CSS and JS For Form Styles</th>
                    <td>
                        <input type="checkbox" name="<?php echo LOAD_PLUGIN_CSS_JS_OPTION_NAME; ?>" value="1" <?php echo get_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Blacklisted Usernames (Seperated by new line)</th>
                    <td>
                        <textarea name="<?php echo BLACKLISTED_USERNAMES_OPTION_NAME; ?>" rows="5" cols="50"><?php echo get_option(BLACKLISTED_USERNAMES_OPTION_NAME); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Blacklisted Email Domains (Seperated by new line)</th>
                    <td>
                        <textarea name="<?php echo BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME; ?>" rows="5" cols="50"><?php echo get_option(BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Verify for disposable email domain to reduce spam?</th>
                    <td>
                        <input type="checkbox" name="<?php echo VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME; ?>" value="1" <?php echo get_option(VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>


            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


