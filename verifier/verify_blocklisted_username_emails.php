<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../disposable-mail-verify/verify_disposable_mail.php';

// constructor
class VerifyBlocklistedUsernameEmails
{

    /**
     * @var array
     */
    private array $blocklisted_usernames;

    /**
     * @var array
     */
    private array $blocklisted_email_domains;

    /**
     * @var array Hash map for fast username lookup
     */
    private array $blocklisted_usernames_hash;

    /**
     * @var array Hash map for fast email domain lookup
     */
    private array $blocklisted_email_domains_hash;

    public function __construct()
    {
        // Get blocklisted data with caching
        $blocklisted_username_str = get_option(BLACKLISTED_USERNAMES_OPTION_NAME);
        $blocklisted_email_str = get_option(BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME);

        $this->blocklisted_usernames = $this->separateString($blocklisted_username_str);
        $this->blocklisted_email_domains = $this->separateString($blocklisted_email_str);
        
        // Create hash maps for fast lookup
        $this->blocklisted_usernames_hash = array_fill_keys($this->blocklisted_usernames, true);
        $this->blocklisted_email_domains_hash = array_fill_keys($this->blocklisted_email_domains, true);
    }

    /**
     * Check if the username is blocklisted
     * @param $username
     * @return bool
     */
    public function isUsernameBlocklisted($username): bool
    {
        return isset($this->blocklisted_usernames_hash[$username]);
    }

    /**
     * Check if the email is blocklisted
     * @param $email
     * @return bool
     */
    public function isEmailBlocklisted($email): bool
    {

        $email_parts = explode('@', $email);

        // if the email parts are less than 2, then the email is invalid
        if (count($email_parts) < 2) {
            return false;
        }

        $email_domain = $email_parts[1];
        return isset($this->blocklisted_email_domains_hash[$email_domain]);
    }

    /**
     * Verify both username and email, do not verify disposable email
     * @param $username
     * @param $email
     * @return bool
     */
    public function isBlockListed($username, $email): bool
    {
        if ($this->isUsernameBlocklisted($username) || $this->isEmailBlocklisted($email)) {
            return true;
        }

        return false;
    }

    /**
     * Verify disposable email domain
     * @param $email
     * @return bool
     */
    public function isDisposableEmail($email): bool
    {
        return (new DisposableEmailVerifier)->isDisposableEmail($email);
    }

    /**
     * Separate string by space or new line
     * @param $string
     * @return array
     */
    private function separateString($string): array
    {
        // Use a more efficient approach with preg_split
        $result = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter out empty elements and trim whitespace
        return array_filter(array_map('trim', $result), function($item) {
            return !empty($item);
        });
    }
}