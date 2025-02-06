<?php

class RecaptchaVerify
{

    /**
     * @var false|mixed|null
     */
    private $site_secret_key;


    public function __construct()
    {
        // site secret key, should read from database
        $this->site_secret_key = get_option('recaptcha_secret_key');

    }

    /**
     * Returns true if the response is valid, false otherwise
     * @param $response
     * @return bool
     */
    public function verifyResponse($response): bool
    {
        $remoteip = $_SERVER["REMOTE_ADDR"];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->site_secret_key,
            'response' => $response,
            'remoteip' => $remoteip
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            return false;
        }

        $result = json_decode($result);

        return $result->success;
    }

}