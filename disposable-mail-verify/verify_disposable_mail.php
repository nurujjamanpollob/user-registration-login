<?php

require_once plugin_dir_path(__FILE__) . '../plugin_data_acceess.php';

/**
 * this class used to verify disposable email domains
 */
class DisposableEmailVerifier   {

    /**
     * Verify if the email is disposable
     * @param $email
     * @return bool
     */
    public function isDisposableEmail($email): bool
    {
        // strip the domain from the email
        $email_parts = explode('@', $email);

        // if the email parts are less than 2, then the email is invalid
        if (count($email_parts) < 2) {
            return false;
        }

        $email_domain = $email_parts[1];

        // get the hash of disposable email domains with caching
        $disposable_email_domains_hash = PluginDataAccess::getDisposableEmailDomainsHash();

        // Use hash lookup for O(1) performance instead of array_search
        return isset($disposable_email_domains_hash[$email_domain]);
    }
}