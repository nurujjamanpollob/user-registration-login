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
            !isset($_POST['loginPageOverrideEnabled'])
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
    // add the registration shortcode check result to the response
    $response['registrationShortCodeCheck'] = $registrationShortCodeCheck ? 'true' : 'false';

    // add the login shortcode check result to the response
    $response['loginShortCodeCheck'] = $loginShortCodeCheck ? 'true' : 'false';

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
                            loginPageOverrideEnabled: loginPageOverrideEnabled
                        },
                        success: function (response) {

                            // we need to check both registration and login shortcode response is true
                            const responseObj = JSON.parse(response);

                            // log the response
                            console.log(responseObj);

                            // check if both registration and login shortcodes are true
                            if (responseObj.registrationShortCodeCheck === 'true' && responseObj.loginShortCodeCheck === 'true') {

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
                    Disable WordPress Default Registration/Login URL: Disable WordPress default registration/login URL.
                    If you enable this setting, the default WordPress registration/login URL will be disabled.
                    Users will not be able to access the default WordPress registration/login URL.
                    Instead, they will be redirected to the custom registration/login URL
                    that you have set in the plugin settings.
                    Do not do this
                    if you don't have a custom page as a registration/login url which contains the registration/login
                    form,
                    and yet not set the custom registration/login URL in the plugin settings.
                    You also need to make sure that reCaptcha is working properly.
                </li>
                <li>
                    WordPress Default Login URL: The custom login URL that you want to set.
                    If you disable the WordPress default login URL, you need to set a custom login URL here.
                    This custom login URL should contain the login form.
                    If you don't have a custom login URL, do not disable the WordPress default login URL.

                    You can select the default WordPress login URL by choosing from the dropdown.
                </li>
                <li>
                    WordPress Default Registration URL: The custom registration URL that you want to set.
                    If you disable the WordPress default registration URL, you need to set a custom registration URL
                    here.
                    This custom registration URL should contain the registration form.
                    If you don't have a custom registration URL, do not disable the WordPress default registration URL.

                    You can select the default WordPress registration URL by choosing from the dropdown.
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
                    <th scope="row">Disable WordPress Default Login URL</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <!-- create a dropdown for the default login url,
                a database query to get all pages and select the default page id -->
                <tr valign="top">
                    <th scope="row">WordPress Default Login URL</th>
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
                    <th scope="row">Disable WordPress Default Registration URL</th>
                    <td>
                        <input type="checkbox" name="<?php echo DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME; ?>"
                               value="1" <?php echo get_option(DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME) === '1' ? 'checked' : ''; ?>>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">WordPress Default Registration URL</th>
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

            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


