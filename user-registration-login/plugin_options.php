<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Global Constants for recaptcha site key and secret key
const RECAPTCHA_SITE_KEY = '';
const RECAPTCHA_SECRET_KEY = '';
const RECAPTCHA_SITE_KEY_OPTION_NAME = 'recaptcha_site_key';
const RECAPTCHA_SECRET_KEY_OPTION_NAME = 'recaptcha_secret_key';

// Define menu slug
const REGISTRATION_LOGIN_MENU_SETTINGS_SLUG = 'user-registration-login-settings';

// shortcodes page slug
const SHORTCODES_PAGE_SLUG = 'user-registration-login-shortcodes';

// dashboard page slug
const DASHBOARD_PAGE_SLUG = 'user-registration-login-dashboard';

// recaptcha test page slug
const RECAPTCHA_TEST_PAGE_SLUG = 'user-registration-login-recaptcha-test';

// add user role option name
const USER_ROLE_OPTION_NAME = 'user_registration_login_new_created_user_role';

// send registration email to admin option name
const SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME = 'send_registration_email_to_admin';

// Load Plugin CSS and js options name
const LOAD_PLUGIN_CSS_JS_OPTION_NAME = 'load_user-registration_and_login_plugin_css_js';

// option name for a list of blacklisted usernames
const BLACKLISTED_USERNAMES_OPTION_NAME = 'user_registration_login_blacklisted_usernames';

// option name for a list of blacklisted email domains
const BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME = 'user_registration_login_blacklisted_email_domains';

// verify disposable email domains option name
const VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME = 'user_registration_login_verify_disposable_email_domains';

// enable blacklist check option name
const ENABLE_BLACKLIST_CHECK_OPTION_NAME = 'user_registration_login_enable_blacklist_check';

// enable whitelist check option name
const ENABLE_WHITELIST_CHECK_OPTION_NAME = 'user_registration_login_enable_whitelist_check';

// option name for a list of whitelisted email domains
const WHITELISTED_EMAIL_DOMAINS_OPTION_NAME = 'user_registration_login_whitelisted_email_domains';

// disable wordpress default login url option name
const DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME = 'user_registration_login_disable_wordpress_default_login_url';

// wordpress default login url option name
const WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME = 'user_registration_login_wordpress_default_login_url';

// disable default registration url option name
const DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME = 'user_registration_login_disable_default_registration_url';

// WordPress default registration url option name
const WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME = 'user_registration_login_wordpress_default_registration_url';

// captcha verified option name
const RECAPTCHA_VERIFIED_OPTION_NAME = 'user_registration_login_captcha_verified';
// wordpress disable default password reset url option name
const DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME = 'user_registration_login_disable_default_password_reset_url';

// wordpress default password reset url option name
const WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME = 'user_registration_login_wordpress_default_password_reset_url';

// wordpress disable default password set url option name
const DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME = 'user_registration_login_disable_default_password_set_url';

// wordpress default password set url option name
const WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME = 'user_registration_login_wordpress_default_password_set_url';

// password minimum length option name
const PASSWORD_MINIMUM_LENGTH_OPTION_NAME = 'user_registration_login_password_minimum_length';