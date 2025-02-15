<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Global Constants for recaptcha site key and secret key
const RECAPTCHA_SITE_KEY = '';
const RECAPTCHA_SECRET_KEY = '';
const RECAPTCHA_SITE_KEY_OPTION_NAME = 'recaptcha_site_key';
const RECAPTCHA_SECRET_KEY_OPTION_NAME = 'recaptcha_secret_key';
// redirect after activation option name
const REDIRECT_AFTER_ACTIVATION_OPTION_NAME = 'redirect_after_activation';
// captcha test on plugin activation option name
const RECAPTCHA_TESTED_OPTION_NAME = 'recaptcha_tested';

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
