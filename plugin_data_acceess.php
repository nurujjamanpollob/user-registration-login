<?php

/**
 * Provides access to some plugin data
 */
class PluginDataAccess {
    /**
     * Get the list of disposable email domains
     * @return array
     */
    public static function getDisposableEmailDomains(): array
    {
        // Check if domains are cached in transient
        $domains = get_transient('disposable_email_domains');
        
        if ($domains === false) {
            $emailListPath = plugin_dir_path(__FILE__) . 'assets/file/disposable_email_blocklist.conf';
            
            // Use a more efficient file reading approach
            if (file_exists($emailListPath)) {
                // Read file content and process it efficiently
                $content = file_get_contents($emailListPath);
                if ($content !== false) {
                    // Split by newlines and filter out empty lines
                    $domains = array_filter(explode("\n", $content), function($line) {
                        return !empty(trim($line));
                    });
                    // Cache for 24 hours
                    set_transient('disposable_email_domains', $domains, 86400);
                } else {
                    // Fallback to empty array if file read fails
                    $domains = array();
                }
            } else {
                // Fallback to empty array if file doesn't exist
                $domains = array();
            }
        }
        
        return $domains;
    }
    
    /**
     * Get disposable email domains in a hash format for faster lookup
     * @return array Hash map of disposable domains for O(1) lookup
     */
    public static function getDisposableEmailDomainsHash(): array
    {
        // Check if hash is cached in transient
        $domain_hash = get_transient('disposable_email_domains_hash');
        
        if ($domain_hash === false) {
            $domains = self::getDisposableEmailDomains();
            // Create hash map for O(1) lookup
            $domain_hash = array_fill_keys($domains, true);
            set_transient('disposable_email_domains_hash', $domain_hash, 86400); // Cache for 24 hours
        }
        
        return $domain_hash;
    }
}