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

        // get the list of disposable email domains
        $disposable_email_domains = PluginDataAccess::getDisposableEmailDomains();

        // check if the domain is in the list
        return $this->isContains($email_domain, $disposable_email_domains);
    }


    private function isContains(string $input, array $array): bool
    {
        foreach ($array as $item) {

            if($this->deepDomainNameMatch(strtolower($item), strtolower($input))) {
                return true;
            }
        }
        return false;
    }

    private function deepDomainNameMatch(string $domain, string $another): bool
    {
        // split the domain name by dot
        $domain_parts = explode('.', $domain);
        $another_parts = explode('.', $another);

        // count the number of parts
        $domain_parts_count = count($domain_parts);
        $another_parts_count = count($another_parts);

        // if they both identical in size, then we can simply compare them and return the result
        if ($domain_parts_count === $another_parts_count) {
            return $domain_parts === $another_parts;
        }


        // if the domain parts count is less than 2, then it is not a domain name
        if ($domain_parts_count < 2) {
            return false;
        }

        // if another part count is less than 2, then it is not a domain name
        if ($another_parts_count < 2) {
            return false;
        }

        // if domain part is bigger than another part, then we need to run the loop by the size of domain part, otherwise, we need to run the loop by the size of another part
        if ($domain_parts_count > $another_parts_count) {

            return $this->matchDomainParts($domain_parts, $another_parts);

        } else {

            return $this->matchDomainParts($another_parts, $domain_parts);
        }


    }

    private function matchDomainParts(array $domainPartsBiggest, array $domainPartsSmallest): bool {

        // if any of part is empty, or less than 2, then return false
        if(count($domainPartsBiggest) < 2 || count($domainPartsSmallest) < 2) {
            return false;
        }


        // create a copy array, which can be modified
        $domainPartsBiggestCopy = $domainPartsBiggest;

        // get the first occurrence of the part in the biggest array
        $firstOccurrence = array_search($domainPartsSmallest[0], $domainPartsBiggestCopy);

        // check the first occurrence
        if($firstOccurrence === false) {
            return false;
        }

        // slice the biggest array from the first occurrence
        $domainPartsBiggestCopy = array_slice($domainPartsBiggestCopy, $firstOccurrence);

        // now test if the smaller array not larger than the biggest array
        if(count($domainPartsSmallest) > count($domainPartsBiggestCopy)) {
            return false;
        }

        // now test their equality and order, to match each part, if any part is not matched, then return false
        foreach($domainPartsSmallest as $key => $part) {
            if($part !== $domainPartsBiggestCopy[$key]) {
                return false;
            }
        }


        return true;
    }
}