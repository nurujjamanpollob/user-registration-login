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

    /**
     * This method is kind of depending on probability,
     * and this method used to correctly filter out the domain name from the given address.
     *
     * In some challenging cases, the domain name is not the last part of the email address, some examples are:
     * 1. anonymous.example.com.au (domain name is example.com.au), but we can't just take the last two parts of the address, because it is going to be com.au, and it is not a domain name. but we still need to correctly filter out the domain name, and this method is used to do that.
     * We can use probability to possibly match names, such as breaking them effectively by the dot, match and count how many identical parts are there, and if the count is more than 1, then we can say that the domain name is the last two parts of the address.
     * This method is not perfect, but it is a good way to filter out domain names from the given address. at least it is providing better results than just taking the last two parts of the address.
     * @param string $domain domain name $domain
     * @param string $another email address $another
     * @return bool
     */
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

        // count the number of identical parts
        $identical_parts_count = 0;

        // if domain part is bigger than another part, then we need to run the loop by the size of domain part, otherwise, we need to run the loop by the size of another part
        if ($domain_parts_count > $another_parts_count) {
            for ($i = 0; $i < $domain_parts_count; $i++) {

                // secondary loop to match single part in the array
                for ($j = 0; $j < $another_parts_count; $j++) {
                    if (isset($domain_parts[$i]) && isset($another_parts[$j]) && $domain_parts[$i] === $another_parts[$j]) {
                        $identical_parts_count++;
                    }
                }


            }
        } else {
            for ($i = 0; $i < $another_parts_count; $i++) {

                // secondary loop to match single part in the array
                for ($j = 0; $j < $domain_parts_count; $j++) {
                    if (isset($domain_parts[$j]) && isset($another_parts[$i]) && $domain_parts[$j] === $another_parts[$i]) {
                        $identical_parts_count++;
                    }
                }
            }
        }

        // if the identical parts count is more than 1, then it is a domain name
        if ($identical_parts_count > 1) {
            return true;
        }

        return false;


    }
}