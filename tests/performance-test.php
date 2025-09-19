<?php
/**
 * Performance Test Script for User Registration & Login Plugin
 * This file tests the implemented optimizations
 */

// Ensure we're running in WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../../../../');
}

// Include necessary WordPress files for testing
require_once ABSPATH . 'wp-config.php';
require_once ABSPATH . 'wp-includes/wp-db.php';

/**
 * Test class for performance validation
 */
class PerformanceTest {
    
    /**
     * Test disposable email verification performance
     */
    public function testDisposableEmailVerification() {
        echo "Testing Disposable Email Verification Performance...\n";
        
        // Get the disposable email domains hash
        require_once plugin_dir_path(__FILE__) . '../plugin_data_acceess.php';
        require_once plugin_dir_path(__FILE__) . '../disposable-mail-verify/verify_disposable_mail.php';
        
        $start_time = microtime(true);
        
        // Test with a few sample emails
        $test_emails = [
            'user@gmail.com',
            'test@yahoo.com', 
            'spam@disposable.com',
            'valid@example.org'
        ];
        
        $verifier = new DisposableEmailVerifier();
        
        foreach ($test_emails as $email) {
            $is_disposable = $verifier->isDisposableEmail($email);
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds
        
        echo "Execution time: {$execution_time} ms\n";
        echo "Performance test completed successfully.\n";
    }
    
    /**
     * Test login security caching performance
     */
    public function testLoginSecurityCaching() {
        echo "Testing Login Security Caching Performance...\n";
        
        // Initialize login security
        require_once plugin_dir_path(__FILE__) . '../loginsecurity/class-login-security.php';
        
        $start_time = microtime(true);
        
        // Create instance and access cache methods multiple times
        $login_security = new LoginSecurity();
        
        // Test multiple cache operations
        for ($i = 0; $i < 100; $i++) {
            $failed_attempts = $login_security->get_cached_failed_attempts();
            $locked_accounts = $login_security->get_cached_locked_accounts();
        }
        
        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds
        
        echo "Cache operations execution time: {$execution_time} ms\n";
        echo "Login security caching test completed successfully.\n";
    }
    
    /**
     * Run all performance tests
     */
    public function runAllTests() {
        echo "Running Performance Tests for User Registration & Login Plugin...\n";
        echo "=========================================================\n\n";
        
        $this->testDisposableEmailVerification();
        echo "\n";
        
        $this->testLoginSecurityCaching();
        echo "\n";
        
        echo "All performance tests completed!\n";
    }
}

// Run the tests if this file is executed directly
if (php_sapi_name() === 'cli') {
    $test = new PerformanceTest();
    $test->runAllTests();
}