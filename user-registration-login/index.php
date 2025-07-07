<?php

/*
 * Plugin Name: User Registration & Login
 * Plugin URI: https://eazewebit.com
 * Description: This plugin allows you to show WordPress user registration form, login form and user profile in the frontend of your website.
 * Version:           2.1.4
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Eaze Web IT
 * Author URI:        https://eazewebit.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://eazewebit.com/user-registration-login-wordpress-plugin/
 * Text Domain:       user-registration-login
 */

include(plugin_dir_path(__FILE__) . 'plugin_options.php');

// include the recaptcha verifier
require_once plugin_dir_path(__FILE__) . 'verifier/recaptcha_verify.php';
require_once plugin_dir_path(__FILE__) . 'utilities/link_direction_handler.php';
require_once plugin_dir_path(__FILE__) . 'forms/registration_form.php';
require_once plugin_dir_path(__FILE__) . 'forms/registration_login_errors.php';
require_once plugin_dir_path(__FILE__) . 'forms/login_form.php';
require_once plugin_dir_path(__FILE__) . 'forms/password_recovery_form.php';
require_once plugin_dir_path(__FILE__) . 'forms/set_user_password_form.php';


// Handle link direction
new LinkDirectionHandler();

// if OVERRIDE_WOOCOMMERCE_LOGIN_PAGE_OPTION_NAME is enabled
if (get_option(OVERRIDE_WOOCOMMERCE_LOGIN_PAGE_OPTION_NAME)) {
    require_once plugin_dir_path(__FILE__) . 'woocommerce/woocommerce_login_page_overrider.php';
    new WooCommerceLoginPageOverrider();
}



if (is_admin()) {
    include_once(plugin_dir_path(__FILE__) . '/pages/setting_page.php');
    include_once(plugin_dir_path(__FILE__) . '/pages/show_shortcodes.php');
    include_once(plugin_dir_path(__FILE__) . '/pages/recaptcha_test.php');

}



// listen on plugin activation
register_activation_hook(__FILE__, 'user_registration_login_on_plugin_activated');


// method that runs on plugin activation
function user_registration_login_on_plugin_activated()
{
    // include once the file that contains the method
    require_once plugin_dir_path(__FILE__) . 'utilities/set_site_option_on_activation.php';

    new SetSiteActivationOptions();

}

/**
 * Initialize and register the CSS and js files
 */
function register_plugin_assets()
{

    if (get_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME)) {
        wp_register_style('registration-login-css', plugin_dir_url(__FILE__) . 'assets/css/user_registration_login_form_styles.css');
        wp_enqueue_style('registration-login-css');
        wp_enqueue_style('dm-sans', 'https://fonts.googleapis.com/css?family=DM Sans');
    }

    wp_register_script('minimal-materialize-dialog', plugin_dir_url(__FILE__) . 'assets/js/dialog.js');
    wp_register_script("recaptcha", "https://www.google.com/recaptcha/api.js?explicit&hl=" . get_locale());
    wp_enqueue_script("recaptcha");
    wp_enqueue_script('minimal-materialize-dialog');

}

add_action('wp_enqueue_scripts', 'register_plugin_assets');



// redirect to settings page after activation
function registration_login_redirect_to_settings_page()
{


    if (!get_transient('registration_login_activation_redirect')) {
        return;
    }

    delete_transient('registration_login_activation_redirect');
    if (isset($_GET['activate-multi'])) {
        return;
    }
    wp_safe_redirect(admin_url('admin.php?page=' . DASHBOARD_PAGE_SLUG));
}


//add action when admin_init
add_action('admin_init', 'registration_login_redirect_to_settings_page');


// add settings page link to plugin page
function registration_login_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=' . REGISTRATION_LOGIN_MENU_SETTINGS_SLUG . '">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'registration_login_settings_link');

// add a shortcodes page link to plugin page
function registration_login_shortcodes_link($links)
{
    $shortcodes_link = '<a href="admin.php?page=' . SHORTCODES_PAGE_SLUG . '">' . __('Shortcodes') . '</a>';
    array_unshift($links, $shortcodes_link);
    return $links;
}

add_filter("plugin_action_links_$plugin", 'registration_login_shortcodes_link');


