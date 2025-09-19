<?php


class RecaptchaVerify
{
	/**
	 * Verify reCAPTCHA response
	 *
	 * @param string $recaptcha_response The reCAPTCHA response from the form
	 * @return bool True if verification successful, false otherwise
	 */
	public function verifyResponse($recaptcha_response)
	{
		return test_recaptcha_submission_with_site_options($recaptcha_response);
	}

}
/**
 * Verify reCAPTCHA response
 *
 * @param string $recaptcha_response The reCAPTCHA response from the form
 * @return bool True if verification successful, false otherwise
 */
function test_recaptcha_submission_with_site_options($recaptcha_response) {
	// Check if recaptcha is enabled
	if (!get_option(RECAPTCHA_VERIFIED_OPTION_NAME)) {
		return true; // If not verified, accept all submissions
	}

	// Check if we have the reCAPTCHA keys
	$site_key = get_option(RECAPTCHA_SITE_KEY_OPTION_NAME);
	$secret_key = get_option(RECAPTCHA_SECRET_KEY_OPTION_NAME);

	if (empty($site_key) || empty($secret_key)) {
		return false; // No keys configured
	}

	// Verify the reCAPTCHA response using WordPress transients for caching
	$cache_key = 'recaptcha_verify_' . md5($recaptcha_response);
	$cached_result = get_transient($cache_key);

	if ($cached_result !== false) {
		return (bool)$cached_result;
	}

	// If not cached, verify with Google's API
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => $secret_key,
		'response' => $recaptcha_response
	);

	// Use wp_remote_post for better error handling
	$response = wp_remote_post($url, array(
		'body' => $data,
		'timeout' => 10, // 10 second timeout
		'user-agent' => 'WordPress Plugin - User Registration & Login'
	));

	if (is_wp_error($response)) {
		// Cache error for a short time to prevent repeated API calls
		set_transient($cache_key, false, 300); // 5 minutes cache on error
		return false;
	}

	$body = wp_remote_retrieve_body($response);
	$result = json_decode($body, true);

	if (isset($result['success']) && $result['success'] === true) {
		// Cache successful verification for longer period
		set_transient($cache_key, true, 3600); // 1 hour cache
		return true;
	} else {
		// Cache failed verification for a short time
		set_transient($cache_key, false, 300); // 5 minutes cache
		return false;
	}
}