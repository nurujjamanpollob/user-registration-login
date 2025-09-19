<?php
/**
 * Login Security Initialization File
 * This file ensures the login security module can be initialized properly
 */

// Ensure WordPress environment is loaded
if (!defined('ABSPATH')) {
    exit;
}

// Include core files
require_once plugin_dir_path(__FILE__) . 'class-login-security.php';
require_once plugin_dir_path(__FILE__) . 'config.php';
require_once plugin_dir_path(__FILE__) . 'settings.php';

// Initialize the login security module
if (class_exists('LoginSecurity')) {
    $login_security = new LoginSecurity();
}