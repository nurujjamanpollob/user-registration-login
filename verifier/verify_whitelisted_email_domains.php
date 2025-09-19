<?php

class VerifyWhitelistedEmailDomains
{

    /**
     * @var array
     */
    private array $whitelisted_email_domains;

    /**
     * @var array Hash map for fast lookup
     */
    private array $whitelisted_email_domains_hash;

    public function __construct()
    {
        // Get whitelisted data with caching
        $whitelisted_email_str = get_option(WHITELISTED_EMAIL_DOMAINS_OPTION_NAME);

        $this->whitelisted_email_domains = $this->separateString($whitelisted_email_str);
        
        // Create hash map for fast lookup
        $this->whitelisted_email_domains_hash = array_fill_keys($this->whitelisted_email_domains, true);
    }

    /**
     * Check if the email is whitelisted
     * @param $email
     * @return bool
     */
    public function isEmailWhitelisted($email): bool
    {

        $email_parts = explode('@', $email);

        // if the email parts are less than 2, then the email is invalid
        if (count($email_parts) < 2) {
            return false;
        }

        $email_domain = $email_parts[1];
        return isset($this->whitelisted_email_domains_hash[$email_domain]);
    }

    /**
     * Separate string by space or new line
     * @param $string
     * @return array
     */
    private function separateString($string): array
    {
        // Use preg_split to handle various whitespace separators
        $result = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter out empty elements and trim whitespace
        return array_filter(array_map('trim', $result), function($item) {
            return !empty($item);
        });
    }

}