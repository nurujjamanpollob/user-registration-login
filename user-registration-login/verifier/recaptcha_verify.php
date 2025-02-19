<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RecaptchaVerify
{

    /**
     * @var false|mixed|null
     */
    private $site_secret_key;


    public function __construct()
    {
        // site secret key should read from a database
        $this->site_secret_key = get_option(RECAPTCHA_SECRET_KEY_OPTION_NAME);

    }

    /**
     * Returns true if the response is valid, false otherwise
     * @param $response
     * @return bool
     */
    public function verifyResponse($response): bool
    {

        // check
        if (empty($response)) {
            return false;
        }
        // sanitize the response
        $response = sanitize_text_field($response);

        $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=$this->site_secret_key&response=$response");
        $response_body = wp_remote_retrieve_body($response);
        $response_data = json_decode($response_body);
        if ($response_data->success) {
            return true;
        } else {
            return false;
        }

    }

}