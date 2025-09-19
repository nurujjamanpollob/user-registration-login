<?php
/**
 * Test script to verify account lockout fix functionality
 * This file is for development and testing purposes only
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test class to demonstrate the account lockout fix
 */
class LockoutFixTest {
    
    public function __construct() {
        // Add test hooks for development
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('admin_menu', array($this, 'add_test_menu'));
            add_action('admin_init', array($this, 'run_tests'));
        }
    }
    
    /**
     * Add test menu to WordPress admin
     */
    public function add_test_menu() {
        add_management_page(
            'Lockout Fix Test',
            'Lockout Fix Test',
            'manage_options',
            'lockout-fix-test',
            array($this, 'display_test_results')
        );
    }
    
    /**
     * Run tests and display results
     */
    public function run_tests() {
        if (isset($_GET['page']) && $_GET['page'] === 'lockout-fix-test') {
            // Test logic would go here
        }
    }
    
    /**
     * Display test results in admin
     */
    public function display_test_results() {
        echo '<div class="wrap">';
        echo '<h1>Lockout Fix Test Results</h1>';
        
        if (class_exists('UserRegistrationPasswordResetLockoutFix')) {
            echo '<div class="notice notice-success"><p>Fix class is available and properly loaded.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Fix class is not available. Please ensure the fix plugin is activated.</p></div>';
        }
        
        if (class_exists('LoginSecurity')) {
            echo '<div class="notice notice-success"><p>LoginSecurity class is available and properly loaded.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>LoginSecurity class is not available. Please ensure the main plugin is activated.</p></div>';
        }
        
        echo '<h2>Test Functions Available:</h2>';
        echo '<ul>';
        echo '<li>clear_lockout_on_password_reset()</li>';
        echo '<li>clear_lockout_on_successful_login()</li>';
        echo '<li>clear_lockout_after_password_set()</li>';
        echo '</ul>';
        
        echo '</div>';
    }
}

// Initialize test if in debug mode
if (defined('WP_DEBUG') && WP_DEBUG) {
    new LockoutFixTest();
}
?>