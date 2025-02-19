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