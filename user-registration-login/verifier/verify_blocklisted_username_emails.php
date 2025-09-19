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

    public function __construct()
    {
        $blocklisted_username_str = get_option(BLACKLISTED_USERNAMES_OPTION_NAME);
        $blocklisted_email_str = get_option(BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME);

        $this->blocklisted_usernames = $this->separateString($blocklisted_username_str);
        // do blocklisted email domains contain line breaks or seperated by space?
        $this->blocklisted_email_domains = $this->separateString($blocklisted_email_str);

    }

    /**
     * Check if the username is blocklisted
     * @param $username
     * @return bool
     */
    public function isUsernameBlocklisted($username): bool
    {
        if (in_array($username, $this->blocklisted_usernames)) {
            return true;
        }
        return false;
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
        if (in_array($email_domain, $this->blocklisted_email_domains)) {
            return true;
        }
        return false;
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
        return preg_split('/\s+/', $string);
    }
}
