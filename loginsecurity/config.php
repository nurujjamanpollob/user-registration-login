<?php
/**
 * Login Security Configuration File
 */

// Security constants - only define if not already defined
if (!defined('LOGIN_SECURITY_ATTEMPT_THRESHOLD')) {
    define('LOGIN_SECURITY_ATTEMPT_THRESHOLD', 5);
}

if (!defined('LOGIN_SECURITY_TIME_WINDOW')) {
    define('LOGIN_SECURITY_TIME_WINDOW', 60);
}

if (!defined('LOGIN_SECURITY_ENABLED')) {
    define('LOGIN_SECURITY_ENABLED', false);
}

// Option names for database storage
if (!defined('LOGIN_SECURITY_FAILED_ATTEMPTS_OPTION')) {
    define('LOGIN_SECURITY_FAILED_ATTEMPTS_OPTION', 'login_security_failed_attempts');
}

if (!defined('LOGIN_SECURITY_LOCKED_ACCOUNTS_OPTION')) {
    define('LOGIN_SECURITY_LOCKED_ACCOUNTS_OPTION', 'login_security_locked_accounts');
}

if (!defined('LOGIN_SECURITY_ATTEMPT_THRESHOLD_OPTION')) {
    define('LOGIN_SECURITY_ATTEMPT_THRESHOLD_OPTION', 'login_security_attempt_threshold');
}

if (!defined('LOGIN_SECURITY_TIME_WINDOW_OPTION')) {
    define('LOGIN_SECURITY_TIME_WINDOW_OPTION', 'login_security_time_window');
}

if (!defined('LOGIN_SECURITY_ENABLED_OPTION')) {
    define('LOGIN_SECURITY_ENABLED_OPTION', 'login_security_enabled');
}

// Admin menu slug
if (!defined('LOGIN_SECURITY_ADMIN_MENU_SLUG')) {
    define('LOGIN_SECURITY_ADMIN_MENU_SLUG', 'locked-accounts');
}

// AJAX actions
if (!defined('LOGIN_SECURITY_UNLOCK_ACCOUNT_ACTION')) {
    define('LOGIN_SECURITY_UNLOCK_ACCOUNT_ACTION', 'unlock_account');
}

if (!defined('LOGIN_SECURITY_UNLOCK_ACCOUNTS_BULK_ACTION')) {
    define('LOGIN_SECURITY_UNLOCK_ACCOUNTS_BULK_ACTION', 'unlock_accounts_bulk');
}