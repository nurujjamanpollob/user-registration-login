<?php
/**
 * Activation hook for the account lockout password reset fix
 * This file should be called during plugin activation to ensure the fix is loaded
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Activate the password reset lockout fix
 */
function user_registration_login_fix_activate() {
    // The fix is implemented as a standalone plugin that gets activated separately
    // This function can be used to add any necessary initialization code
    
    // Log activation for debugging purposes
    error_log('User Registration Login: Plugin activated');
}

/**
 * Deactivate the password reset lockout fix
 */
function user_registration_login_fix_deactivate() {
    // Cleanup code if needed
    error_log('User Registration Login: Plugin deactivated');
}

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'user_registration_login_fix_activate');
register_deactivation_hook(__FILE__, 'user_registration_login_fix_deactivate');

// The actual fix implementation is in fix-account-lockout-password-reset.php
// This file serves as an activation script only
