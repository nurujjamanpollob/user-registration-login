<?php

if (!defined('ABSPATH')) {
    exit;
}

// add top level menu
add_action('admin_menu', 'registration_login_menu');

add_action( 'wp_ajax_setting_handle', 'check_shortcode' );

// admin script enqueue
function add_material_dialog() {
    //register script from the plugin directory
    wp_register_script('minimal-materialize-dialog', plugin_dir_url(__FILE__) . '../assets/js/dialog.js');
    //enqueue the script
    wp_enqueue_script('minimal-materialize-dialog');
}

add_action( 'admin_enqueue_scripts', 'add_material_dialog', 0);


function check_shortcode() {

    // isset check
    if(
            !isset($_POST['registrationPageId']) ||
            !isset($_POST['registrationShortCodeName']) ||
            !isset($_POST['loginPageId']) ||
            !isset($_POST['loginShortCodeName']) ||
            !isset($_POST['registrationPageOverrideEnabled']) ||
            !isset($_POST['loginPageOverrideEnabled']) ||
            !isset($_POST['passwordResetPageId']) ||
            !isset($_POST['passwordResetShortCodeName']) ||
            !isset($_POST['passwordResetPageOverrideEnabled']) ||
            !isset($_POST['passwordSetPageId']) ||
            !isset($_POST['passwordSetPageShortCodeName']) ||
            !isset($_POST['passwordSetPageOverrideEnabled']) ||
            !isset($_POST['action'])
    ) {
        echo json_encode(array('registrationShortCodeCheck' => 'false', 'loginShortCodeCheck' => 'false'));
        wp_die();
    }


    $registrationPageId = $_POST['registrationPageId'];
    $registrationShortCodeName = $_POST['registrationShortCodeName'];
    $loginPageId = $_POST['loginPageId'];
    $loginShortCodeName = $_POST['loginShortCodeName'];
    $registrationPageOverrideEnabled = $_POST['registrationPageOverrideEnabled'];
    $loginPageOverrideEnabled = $_POST['loginPageOverrideEnabled'];
    $passwordResetPageId = $_POST['passwordResetPageId'];
    $passwordResetShortCodeName = $_POST['passwordResetShortCodeName'];
    $passwordResetPageOverrideEnabled = $_POST['passwordResetPageOverrideEnabled'];
    $passwordSetPageId = $_POST['passwordSetPageId'];
    $passwordSetPageShortCodeName = $_POST['passwordSetPageShortCodeName'];
    $passwordSetPageOverrideEnabled = $_POST['passwordSetPageOverrideEnabled'];

    // perform the shortcode check
    require_once plugin_dir_path(__FILE__) . '../utilities/link_direction_handler.php';

    // response json builder
    $response = array();

    if($registrationPageOverrideEnabled === 'true') {

        $registrationShortCodeCheck = LinkDirectionHandler::isPageContainsShortcode($registrationPageId, $registrationShortCodeName);
    } else {
        $registrationShortCodeCheck = true;
    }
    if($loginPageOverrideEnabled === 'true') {

        $loginShortCodeCheck = LinkDirectionHandler::isPageContainsShortcode($loginPageId, $loginShortCodeName);
    } else {
        $loginShortCodeCheck = true;
    }

    if($passwordResetPageOverrideEnabled === 'true') {
        $passwordResetShortCodeCheck = LinkDirectionHandler::isPageContainsShortcode($passwordResetPageId, $passwordResetShortCodeName);
    } else {
        $passwordResetShortCodeCheck = true;
    }

    if($passwordSetPageOverrideEnabled === 'true') {
        $passwordSetShortCodeCheck = LinkDirectionHandler::isPageContainsShortcode($passwordSetPageId, $passwordSetPageShortCodeName);
    } else {
        $passwordSetShortCodeCheck = true;
    }


    // add the registration shortcode check result to the response
    $response['registrationShortCodeCheck'] = $registrationShortCodeCheck ? 'true' : 'false';

    // add the login shortcode check result to the response
    $response['loginShortCodeCheck'] = $loginShortCodeCheck ? 'true' : 'false';

    // add the password reset shortcode check result to the response
    $response['passwordResetShortCodeCheck'] = $passwordResetShortCodeCheck ? 'true' : 'false';

    // add the password set shortcode check result to the response
    $response['passwordSetShortCodeCheck'] = $passwordSetShortCodeCheck ? 'true' : 'false';

    // send the response
    echo json_encode($response);

    wp_die();
}

// include once the menu_dashboard_content.php file
require_once 'menu_dashboard_content.php';


function get_404_image():string {
    return '<img src="' . plugin_dir_url(__FILE__) . '../assets/img/error_404.webp" style="width: 100%; height: 40%; margin-bottom: 20px;"/>';
}

function registration_login_menu()
{

    // add menu page
    add_menu_page('User Registration & Login', 'User Login & Registration', 'manage_options', DASHBOARD_PAGE_SLUG, 'menu_dashboard_content');

    // add submenu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Settings', 'Settings', 'manage_options', REGISTRATION_LOGIN_MENU_SETTINGS_SLUG, 'registration_login_page');

    //add submenu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Shortcodes', 'Shortcodes', 'manage_options', SHORTCODES_PAGE_SLUG, 'showShortCodes');

    // add submenu page
    add_submenu_page(DASHBOARD_PAGE_SLUG, 'Recaptcha Test', 'Recaptcha Test', 'manage_options', RECAPTCHA_TEST_PAGE_SLUG, 'recaptcha_test');

    //call register settings function
    add_action('admin_init', 'register_user_login_settings');
}

function register_user_login_settings()
{
    //register our settings
    register_setting('user-login-settings-group', 'recaptcha_site_key');
    register_setting('user-login-settings-group', 'recaptcha_secret_key');
    register_setting('user-login-settings-group', USER_ROLE_OPTION_NAME);
    register_setting('user-login-settings-group', SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME);
    register_setting('user-login-settings-group', LOAD_PLUGIN_CSS_JS_OPTION_NAME);
    register_setting('user-login-settings-group', BLACKLISTED_USERNAMES_OPTION_NAME);
    register_setting('user-login-settings-group', BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME);
    register_setting('user-login-settings-group', VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME);
    register_setting('user-login-settings-group', ENABLE_BLACKLIST_CHECK_OPTION_NAME);
    register_setting('user-login-settings-group', ENABLE_WHITELIST_CHECK_OPTION_NAME);
    register_setting('user-login-settings-group', WHITELISTED_EMAIL_DOMAINS_OPTION_NAME);
    register_setting('user-login-settings-group', DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME);
    register_setting('user-login-settings-group', WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME);
    register_setting('user-login-settings-group', WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME);
    register_setting('user-login-settings-group', DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME);
    register_setting('user-login-settings-group', DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME);
    register_setting('user-login-settings-group', WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME);
    register_setting('user-login-settings-group', DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME);
    register_setting('user-login-settings-group', WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME);
    register_setting('user-login-settings-group', PASSWORD_MINIMUM_LENGTH_OPTION_NAME);
}


function registration_login_page()
{
    ?>

        <script type="text/javascript">

            // page load event
            document.addEventListener('DOMContentLoaded', function () {
                // get the form element
                const form = document.getElementById('settings_page_form');
                // add submit event listener
                form.addEventListener('submit', function (event) {
                    // prevent form submission
                    event.preventDefault();

                    // get registration page id by name
                    const registrationPageId = document.getElementsByName('<?php echo WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME; ?>')[0].value;
                    // get login page id by name
                    const loginPageId = document.getElementsByName('<?php echo WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME; ?>')[0].value;

                    const registrationPageOverrideEnabled = document.getElementsByName('<?php echo DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME; ?>')[0].checked ? 'true' : 'false';
                    const loginPageOverrideEnabled = document.getElementsByName('<?php echo DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME; ?>')[0].checked ? 'true' : 'false';
                    const passwordResetPageOverrideEnabled = document.getElementsByName('<?php echo DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME; ?>')[0].checked ? 'true' : 'false';
                    const passwordResetPageId = document.getElementsByName('<?php echo WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME; ?>')[0].value;
                    const passwordSetPageOverrideEnabled = document.getElementsByName('<?php echo DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME; ?>')[0].checked ? 'true' : 'false';
                    const passwordSetPageId = document.getElementsByName('<?php echo WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME; ?>')[0].value;


                    // submit to wp ajax
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'setting_handle',
                            registrationPageId: registrationPageId,
                            registrationShortCodeName: 'register_form',
                            loginPageId: loginPageId,
                            loginShortCodeName: 'login_form',
                            registrationPageOverrideEnabled: registrationPageOverrideEnabled,
                            loginPageOverrideEnabled: loginPageOverrideEnabled,
                            passwordResetPageOverrideEnabled: passwordResetPageOverrideEnabled,
                            passwordResetPageId: passwordResetPageId,
                            passwordResetShortCodeName: 'password_recovery_form',
                            passwordSetPageOverrideEnabled: passwordSetPageOverrideEnabled,
                            passwordSetPageId: passwordSetPageId,
                            passwordSetPageShortCodeName: 'set_user_password_form'

                        },
                        success: function (response) {

                            // we need to check both registration and login shortcode response is true
                            const responseObj = JSON.parse(response);


                            // check if both registration and login shortcodes are true
                            if (responseObj.registrationShortCodeCheck === 'true' && responseObj.loginShortCodeCheck === 'true' && responseObj.passwordResetShortCodeCheck === 'true' && responseObj.passwordSetShortCodeCheck === 'true') {

                                HTMLFormElement.prototype.submit.call(form);


                            } else {

                                // collect error messages
                                let errorMessage = '<div style="display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: center;" class="error_div">';

                                // add header image
                                errorMessage += '<?php echo get_404_image(); ?>';

                                // check if registration shortcode is false
                                if (responseObj.registrationShortCodeCheck === 'false') {
                                    errorMessage += '<span class="error"><b>Error:</b> Registration page does not contain the shortcode [register_form]. Please add the shortcode to the page and try again.</span>';
                                }

                                // check if login shortcode is false
                                if (responseObj.loginShortCodeCheck === 'false') {
                                    errorMessage += '<span class="error"><b>Error:</b> Login page does not contain the shortcode [login_form]. Please add the shortcode to the page and try again.</span>';
                                }

                                // check if password reset shortcode is false
                                if (responseObj.passwordResetShortCodeCheck === 'false') {
                                    errorMessage += '<span class="error"><b>Error:</b> Password reset page does not contain the shortcode [password_recovery_form]. Please add the shortcode to the page and try again.</span>';
                                }

                                // check if password set shortcode is false
                                if (responseObj.passwordSetShortCodeCheck === 'false') {
                                    errorMessage += '<span class="error"><b>Error:</b> Password set page does not contain the shortcode [set_user_password_form]. Please add the shortcode to the page and try again.</span>';
                                }

                                // close the error div
                                errorMessage += '</div>';

                                createAndShowDialog('', errorMessage, null, [{text: 'Cancel', onClick: () => {closeDialog();}}]);



                            }
                        }
                    });



                });
            });
        </script>

    <div class="wrap">
        <h2>User Registration & Login Settings</h2>

        <div>

            <p>
                Use the settings below to customize plugin behaviour.
                The registration or login form does not work properly,
                contact us here at: <a href="https://eazewebit.com">eazewebit.com</a>

                A brief description of each setting is provided below:
            </p>

            <ul>
                <li>Recaptcha Site Key: The site key provided by Google Recaptcha.</li>
                <li>Recaptcha Secret Key: The secret key provided by Google Recaptcha.</li>
                <li>User Role: The role assigned to the user after registration.</li>
                <li>Send Registration Email to Admin: Send an email to the admin when a new user registers.</li>
                <li>Load Plugin CSS and JS For Form Styles: Load the plugin CSS and JS for form styles.</li>
                <li>Blacklisted Usernames: A list of blacklisted usernames separated by a new line. users cannot use
                    those
                    protected usernames to create a user account at your website.
                </li>
                <li>Enable blacklist Check on email domains: Enable blacklist check on email domains.
                    For example, if you add example.com in this field,
                    all email addresses end with @example.com will be unable
                    to create a user account on your website.
                    An example such as user@example.com, user2@example.com ...@example.com, more.
                    In order to make this work, you need to enable blacklist check on email domains,
                    and need to disable whitelist check on email domains.
                </li>
                <li>Blacklisted Email Domains: A list of blacklisted email domains separated by a new line.</li>
                <li>Enable Whitelist Check: Enable whitelist check on email domains.
                    Only allowed email domain users will be able to create a new user account at your website.
                    This setting also respects blacklist username check.

                    If you enable this setting, the blacklist check on email domains will not perform.

                    Example:
                    If you add example.com in this field,
                    only users with email addresses ending with @example.com will be able
                    to create a user account on your website.
                    Email addresses ending with @example.com will be allowed,
                    and all other email addresses with different domains will be blocked.

                    For example, email addresses such as user@example1.com, user@example2.com, ...more will be blocked.
                </li>
                <li>Whitelisted Email Domains: A list of whitelisted email domains separated by a new line.</li>
                <li>Verify for disposable email domain to reduce spam: Verify for disposable email domain to reduce
                    spam.
                    This function works regardless of blacklist or whitelist settings!
                </li>
                <li>
                    Disable WordPress Default Login Page:
                    Disable WordPress default login Page.
                    If you enable this setting, the default WordPress registration/login URL will be disabled,
                    and request made to it will be redirected to the custom registration/login URL
                    that you have set.
                    You need to select a page that contains the login form, from the dropdown below.
                </li>
                <li>
                    WordPress Default Login Page: The custom login Page that you want to set.
                    If you want to disable the WordPress default login URL, you need to set a custom login URL here.
                    This custom login URL should contain the login form.
                    This setting is effective if you enable WordPress default login page.

                </li>
                <li>
                    Disable WordPress Default Registration Page:
                    Disable WordPress default registration Page.

                    If you enable this setting, the default WordPress registration URL will be disabled, and request made to it will be redirected to the custom registration URL that you have set.
                    You need to select a page that contains the registration form, from the dropdown below.
                </li>
                <li>
                    WordPress Default Registration Page: The custom registration Page that you want to set.
                    If you want to disable the WordPress default registration URL,
                    you need to set a custom registration URL here.
                    This custom registration URL should contain the registration form.
                    This setting is effective if you enable WordPress default registration page.
                </li>

                <li>
                    Disable WordPress Default Password Reset Page:
                    Disable WordPress default password reset Page.
                    If you enable this setting, the default WordPress password reset URL will be disabled, and request made to it will be redirected to the custom password reset URL that you have set.
                    You need to select a page that contains the password reset form, from the dropdown below.
                </li>
                <li>
                    WordPress Default Password Reset Page: The custom password reset Page that you want to set.
                    If you want to disable the WordPress default password reset URL,
                    you need to set a custom password reset URL here.
                    This custom password reset URL should contain the password reset form.
                    This setting is effective if you enable WordPress default password reset page.
                </li>

                <li>
                    Disable WordPress Default Password Set Page:
                    Disable WordPress default password set Page.
                    If you enable this setting, the default WordPress password set URL will be disabled, and request made to it will be redirected to the custom password set URL that you have set.
                    You need to select a page that contains the password set form from the dropdown below.
                </li>
                <li>
                    WordPress Default Password Set Page: The custom password set Page that you want to set.
                    If you want to disable the WordPress default password set URL,
                    you need to set a custom password set URL here.
                    This custom password set URL should contain the password set form.
                    This setting is effective if you enable WordPress default password set page.
                </li>
                <li>
                    Password Minimum Length: The minimum length of the password.
                    This setting is used to validate the password length during password reset
                    or setting a new password,
                    and effective only if the shortcode from this plugin is used.
                </li>

            </ul>

            If you have any questions or need help,
            please contact us at <a href="https://eazewebit.com">eazewebit.com</a>
        </div>

        <form id="settings_page_form" method="post" action="options.php">
            <?php settings_fields('user-login-settings-group'); ?>
            <?php do_settings_sections('user-login-settings-group'); ?>


            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Recaptcha Site Key</th>
                    <td><input type="text" name="recaptcha_site_key"
                               value="<?php echo get_option(RECAPTCHA_SITE_KEY_OPTION_NAME); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Recaptcha Secret Key</th>
                    <td><input type="text" name="recaptcha_secret_key"
                               value="<?php echo get_option(RECAPTCHA_SECRET_KEY_OPTION_NAME); ?>"/></td>
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
                        <input type="checkbox" name="<?php echo SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Load Plugin CSS and JS For Form Styles</th>
                    <td>
                        <input type="checkbox" name="<?php echo LOAD_PLUGIN_CSS_JS_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Blacklisted Usernames (Seperated by new line)</th>
                    <td>
                        <textarea name="<?php echo BLACKLISTED_USERNAMES_OPTION_NAME; ?>" rows="5"
                                  cols="50"><?php echo get_option(BLACKLISTED_USERNAMES_OPTION_NAME); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Enable blacklist Check on email domains? (Checks will not perform if whitelist check
                        is enabled)
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo ENABLE_BLACKLIST_CHECK_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(ENABLE_BLACKLIST_CHECK_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Blacklisted Email Domains (Seperated by new line)</th>
                    <td>
                        <textarea name="<?php echo BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME; ?>" rows="5"
                                  cols="50"><?php echo get_option(BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME); ?></textarea>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Enable Whitelist Check</th>
                    <td>
                        <input type="checkbox" name="<?php echo ENABLE_WHITELIST_CHECK_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(ENABLE_WHITELIST_CHECK_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Whitelisted Email Domains (Seperated by new line)</th>
                    <td>
                        <textarea name="<?php echo WHITELISTED_EMAIL_DOMAINS_OPTION_NAME; ?>" rows="5"
                                  cols="50"><?php echo get_option(WHITELISTED_EMAIL_DOMAINS_OPTION_NAME); ?></textarea>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Verify for disposable email domain to reduce spam?</th>
                    <td>
                        <input type="checkbox" name="<?php echo VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Disable WordPress Default Login Page?</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <!-- create a dropdown for the default login url,
                a database query to get all pages and select the default page id -->
                <tr valign="top">
                    <th scope="row">Set Website Default Login Page</th>
                    <td>
                        <select name="<?php echo WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME; ?>">
                            <option value="">Select a page</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                ?>
                                <option value="<?php echo $page->ID; ?>" <?php echo strval(get_option(WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME)) === strval($page->ID) ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Disable WordPress Default Registration Page?</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Set Website Default Registration Page</th>
                    <td>
                        <select name="<?php echo WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME; ?>">
                            <option value="">Select a page</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                ?>
                                <option value="<?php echo $page->ID; ?>" <?php echo strval(get_option(WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME)) === strval($page->ID) ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>


                <tr valign="top">
                    <th scope="row">Disable WordPress Default Password Reset Page?</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Set Website Default Password Reset Page</th>
                    <td>
                        <select name="<?php echo WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME; ?>">
                            <option value="">Select a page</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                ?>
                                <option value="<?php echo $page->ID; ?>" <?php echo strval(get_option(WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME)) === strval($page->ID) ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Disable WordPress Default Password Set Page?</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Set Website Default Password Set Page</th>
                    <td>
                        <select name="<?php echo WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME; ?>">
                            <option value="">Select a page</option>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                ?>
                                <option value="<?php echo $page->ID; ?>" <?php echo strval(get_option(WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME)) === strval($page->ID) ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Password Minimum Length (This setting is effective if you use this plugin to override default WordPress password set page) </th>
                    <td>
                        <input type="number" name="<?php echo PASSWORD_MINIMUM_LENGTH_OPTION_NAME; ?>"
                               value="<?php echo get_option(PASSWORD_MINIMUM_LENGTH_OPTION_NAME); ?>"/>
                    </td>
                </tr>

            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


