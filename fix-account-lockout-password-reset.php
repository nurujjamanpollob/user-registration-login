<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to fix account lockout persistence after password reset
 */
class UserRegistrationPasswordResetLockoutFix {
    
    /**
     * Constructor - hooks into WordPress
     */
    public function __construct() {
        // Hook into WordPress password reset process to clear lockouts
        add_action('password_reset', array($this, 'clear_lockout_on_password_reset'));
        add_action('wp_login', array($this, 'clear_lockout_on_successful_login'), 10, 2);
        
        // Hook into WordPress reset password functionality to ensure lockout is cleared
        add_action('set_password', array($this, 'clear_lockout_after_password_set'));
    }
    
    /**
     * Clear account lockout when a user resets their password via the standard WordPress process
     * 
     * @param WP_User $user The user object
     */
    public function clear_lockout_on_password_reset($user) {
        // Get the login security class instance to clear lockouts
        if (class_exists('LoginSecurity')) {
            $login_security = new LoginSecurity();
            
            // Clear any lockout for this user
            $locked_accounts = get_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, array());
            
            if (isset($locked_accounts[$user->user_login])) {
                unset($locked_accounts[$user->user_login]);
                update_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, $locked_accounts);
                
                // Also clear failed attempts for this user
                $failed_attempts = get_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, array());
                unset($failed_attempts[$user->user_login]);
                update_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, $failed_attempts);
                
                // Log the action for debugging purposes
                error_log("UserRegistrationPasswordResetLockoutFix: Cleared lockout for user " . $user->user_login);
            }
        }
    }
    
    /**
     * Clear account lockout when a user successfully logs in (to ensure consistency)
     * 
     * @param string $user_login The user login
     * @param WP_User $user The user object
     */
    public function clear_lockout_on_successful_login($user_login, $user) {
        // Get the login security class instance to clear lockouts on successful login
        if (class_exists('LoginSecurity')) {
            $login_security = new LoginSecurity();
            
            // Clear any lockout for this user on successful login
            $locked_accounts = get_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, array());
            
            if (isset($locked_accounts[$user_login])) {
                unset($locked_accounts[$user_login]);
                update_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, $locked_accounts);
                
                // Also clear failed attempts for this user
                $failed_attempts = get_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, array());
                unset($failed_attempts[$user_login]);
                update_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, $failed_attempts);
                
                // Log the action for debugging purposes
                error_log("UserRegistrationPasswordResetLockoutFix: Cleared lockout for user " . $user_login . " on successful login");
            }
        }
    }
    
    /**
     * Clear account lockout after password is set via reset_password function
     * 
     * @param WP_User $user The user object
     */
    public function clear_lockout_after_password_set($user) {
        // Get the login security class instance to clear lockouts
        if (class_exists('LoginSecurity')) {
            $login_security = new LoginSecurity();
            
            // Clear any lockout for this user
            $locked_accounts = get_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, array());
            
            if (isset($locked_accounts[$user->user_login])) {
                unset($locked_accounts[$user->user_login]);
                update_option(LoginSecurity::LOCKED_ACCOUNTS_OPTION, $locked_accounts);
                
                // Also clear failed attempts for this user
                $failed_attempts = get_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, array());
                unset($failed_attempts[$user->user_login]);
                update_option(LoginSecurity::FAILED_ATTEMPTS_OPTION, $failed_attempts);
                
                // Log the action for debugging purposes
                error_log("UserRegistrationPasswordResetLockoutFix: Cleared lockout for user " . $user->user_login . " after password set");
            }
        }
    }
}

// Initialize the fix
new UserRegistrationPasswordResetLockoutFix();
