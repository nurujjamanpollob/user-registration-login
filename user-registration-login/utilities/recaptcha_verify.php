<?php

/**
 * Verify recaptcha respecting site preferences
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../verifier/recaptcha_verify.php';

/**
 * Handle recaptcha test submission needs recaptcha response
 * This method will return true if the recaptcha response is valid, otherwise false
 * @param $recaptcha_response string
 * @return bool
 */
function test_recaptcha_submission_with_site_options(string $recaptcha_response): bool
{

    // get site option
    $recaptcha_verified = get_option(RECAPTCHA_VERIFIED_OPTION_NAME);

    // if we do not verify recaptcha, return true
    if (!$recaptcha_verified) {
        return true;
    }

    $recaptcha_verify = new RecaptchaVerify();
    return $recaptcha_verify->verifyResponse($recaptcha_response);
}