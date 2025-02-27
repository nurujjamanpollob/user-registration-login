<?php

class VerifyWhitelistedEmailDomains
{

    /**
     * @var array
     */
    private array $whitelisted_email_domains;

    public function __construct()
    {
        $whitelisted_email_str = get_option(WHITELISTED_EMAIL_DOMAINS_OPTION_NAME);

        $this->whitelisted_email_domains = $this->separateString($whitelisted_email_str);
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
        if (in_array($email_domain, $this->whitelisted_email_domains)) {
            return true;
        }

        return false;
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
