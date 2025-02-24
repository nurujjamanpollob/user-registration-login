<?php

class SetSiteActivationOptions
{

    /**
     * Set site activation options
     */
    public function __construct()
    {
        // add options to the database
        add_option(RECAPTCHA_SITE_KEY_OPTION_NAME, RECAPTCHA_SITE_KEY, '', true);
        add_option(RECAPTCHA_SECRET_KEY_OPTION_NAME, RECAPTCHA_SECRET_KEY, '', true);
        add_option(USER_ROLE_OPTION_NAME, 'subscriber');
        add_option(SEND_REGISTRATION_EMAIL_TO_ADMIN_OPTION_NAME, false);
        add_option(LOAD_PLUGIN_CSS_JS_OPTION_NAME, true, '', true);
        add_option(BLACKLISTED_USERNAMES_OPTION_NAME, '', '', false);
        add_option(BLACKLISTED_EMAIL_DOMAINS_OPTION_NAME, '', '', false);
        add_option(VERIFY_DISPOSABLE_EMAIL_DOMAINS_OPTION_NAME, true, '', true);
        add_option(ENABLE_BLACKLIST_CHECK_OPTION_NAME, false, '', true);
        add_option(ENABLE_WHITELIST_CHECK_OPTION_NAME, false, '', true);
        add_option(WHITELISTED_EMAIL_DOMAINS_OPTION_NAME, '', '', false);
        add_option(DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME, false, '', false);
        add_option(WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME, '', '', false);
        add_option(WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME, '', '', false);
        add_option(DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME, false, '', false);
        add_option(RECAPTCHA_VERIFIED_OPTION_NAME, false, '', true);

        // set transient to redirect to settings page
        set_transient('registration_login_activation_redirect', true, 30);
    }

}