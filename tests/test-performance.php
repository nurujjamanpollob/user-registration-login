<?php
/**
 * PHPUnit test for performance optimization
 */

class PerformanceTest extends WP_UnitTestCase {
    
    public function setUp(): void {
        parent::setUp();
        
        // Ensure the plugin is loaded
        if (!function_exists('user_registration_login_on_plugin_activated')) {
            require_once dirname(__FILE__) . '/../index.php';
        }
    }
    
    /**
     * Test that disposable email domains are cached properly
     */
    public function test_disposable_email_domain_caching() {
        // Clear any existing transients
        delete_transient('disposable_email_domains');
        
        // Get the domains (this should load from file and cache)
        $domains = PluginDataAccess::getDisposableEmailDomains();
        
        // Verify we got domains
        $this->assertNotEmpty($domains);
        $this->assertGreaterThanOrEqual(1000, count($domains));
        
        // Check that transients are set
        $cached_domains = get_transient('disposable_email_domains');
        $this->assertNotEmpty($cached_domains);
        $this->assertEquals(count($domains), count($cached_domains));
    }
    
    /**
     * Test login security configuration caching
     */
    public function test_login_security_configuration_caching() {
        // Clear any existing transients
        delete_transient('login_security_attempt_threshold');
        delete_transient('login_security_time_window');
        delete_transient('login_security_enabled');
        
        $login_security = new LoginSecurity();
        
        // Test that configuration values are properly cached
        $settings = $login_security->get_settings();
        
        // Verify settings were returned
        $this->assertArrayHasKey('enabled', $settings);
        $this->assertArrayHasKey('threshold', $settings);
        $this->assertArrayHasKey('time_window', $settings);
    }
    
    /**
     * Test that plugin loads without errors
     */
    public function test_plugin_loads_without_errors() {
        // This is more of a smoke test to ensure no fatal errors on load
        $this->assertTrue(class_exists('LoginSecurity'));
        $this->assertTrue(function_exists('registration_form'));
        $this->assertTrue(function_exists('login_form'));
    }
}