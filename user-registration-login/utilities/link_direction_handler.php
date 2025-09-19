<?php


/**
 * This class used to handle the link navigation, and captures wp login and registration, password reset page navigations
 */

class LinkDirectionHandler {

    public function __construct()
    {
        // add action to handle the link direction
        add_action('init', array($this, 'handle_link_direction'));
    }

    /**
     * Handle the link direction
     */
    public function handle_link_direction() {
        global $pagenow;

        // handle default WordPress registration page
        if( 'wp-login.php' == $pagenow && isset($_GET['action']) && $_GET['action'] == 'register') {

            // if the option is enabled to disable the default wordpress registration page
            if(get_option(DISABLE_DEFAULT_REGISTRATION_URL_OPTION_NAME)) {

                // access to the WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME option and match
                //if any page is set and the page is accessible
                $registration_page_id = get_option(WORDPRESS_DEFAULT_REGISTRATION_URL_OPTION_NAME);

                // get the page by page id
                $page = get_post($registration_page_id);

                // if the page is not null
                if ($page) {

                    // check if shortcode called 'registration_form' is present in the page
                    if($this->isPageContainsShortcode($registration_page_id, 'register_form')) {
                        // redirect to the page
                        wp_redirect(get_permalink($registration_page_id));
                        exit();
                    }
                }
            }

        }

        // handle default WordPress login page, other url parameters should be null
        if( 'wp-login.php' == $pagenow && !isset($_GET['action'])) {

            // if the option is enabled to disable the default WordPress login page
            if(get_option(DISABLE_WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME)) {

                // access to the WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME option and match
                //if any page is set and page is accessible
                $login_page_id = get_option(WORDPRESS_DEFAULT_LOGIN_URL_OPTION_NAME);

                // get the page by page id
                $page = get_post($login_page_id);


                // if the page is not null
                if ($page) {

                    // check if shortcode called 'login_form' is present in the page
                    if($this->isPageContainsShortcode($login_page_id, 'login_form')) {

                        // get the previous url: wp-admin.php, wp-login.php, etc, if the url getting null, add wp-admin as fallback
                        $previous_url = wp_get_referer();
                        if (!$previous_url) {
                            $previous_url = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';
                        }
                        if (empty($previous_url)) {
                            $previous_url = admin_url();
                        }
                        // determine if the previous url is about setting a user password with key, login, and rp action
                        // check the previous url if it contains the key, login, and action parameters
                        $is_user_password_set_link = strpos($previous_url, 'key=') !== false && strpos($previous_url, 'login=') !== false && strpos($previous_url, 'action=rp') !== false;

                        // if the previous url is a user password set link, then redirect to home page
                        if ($is_user_password_set_link) {
                            // set prev link to home page
                            $previous_url = home_url();
                        }

                        // redirect to the page, include the previous url as a query parameter
                        $login_page_link = get_permalink($login_page_id);
                        $login_page_link = add_query_arg('previous_url', urlencode($previous_url), $login_page_link);
                        wp_redirect($login_page_link);

                        // exit to stop the execution
                        exit();
                    }
                }
            }

        }

        // handle the default WordPress password reset page
        if( 'wp-login.php' == $pagenow && isset($_GET['action']) && $_GET['action'] == 'lostpassword') {

            // if the option is enabled to disable the default WordPress password reset page
            if(get_option(DISABLE_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME)) {

                // access to the WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME option and match
                //if any page is set and page is accessible
                $password_reset_page_id = get_option(WORDPRESS_DEFAULT_PASSWORD_RESET_URL_OPTION_NAME);

                // get the page by page id
                $page = get_post($password_reset_page_id);

                // if the page is not null
                if ($page) {

                    // check if shortcode called 'password_recovery_form' is present in the page
                    if($this->isPageContainsShortcode($password_reset_page_id, 'password_recovery_form')) {
                        // redirect to the page
                        wp_redirect(get_permalink($password_reset_page_id));
                        exit();
                    }
                }
            }

        }

        // handle the default WordPress password set page, this following parameter needed: login, key, action should be rp
        if( 'wp-login.php' == $pagenow && isset($_GET['key']) && isset($_GET['login']) && isset($_GET['action']) && $_GET['action'] == 'rp') {

            // if the option is enabled to disable the default WordPress password set page
            if(get_option(DISABLE_DEFAULT_PASSWORD_SET_URL_OPTION_NAME)) {

                // access to the WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME option and match
                //if any page is set and the page is accessible
                $password_set_page_id = get_option(WORDPRESS_DEFAULT_PASSWORD_SET_URL_OPTION_NAME);

                // get the page by page id
                $page = get_post($password_set_page_id);

                // if the page is not null
                if ($page) {

                    // check if shortcode called 'set_user_password_form' is present in the page
                    if($this->isPageContainsShortcode($password_set_page_id, 'set_user_password_form')) {
                        // redirect to the page

                        $password_set_page_link = get_permalink($password_set_page_id);

                        // add the login and key to the link, and action
                        $password_set_page_link = add_query_arg(array('login' => $_GET['login'], 'key' => $_GET['key'], 'action' => 'rp'), $password_set_page_link);

                        wp_redirect($password_set_page_link);

                        exit();
                    }
                }
            }

        }

    }


    /**
     * This method return true if the pageId contains a VALID SHORTCODE provided
     * @param $pageId
     * @@param $shortcode string
     * @return bool
     * @since 1.0.0
     */
    public static function isPageContainsShortcode($pageId, string $shortcode): bool
    {
        $page = get_post($pageId);
        if ($page) {
            return has_shortcode($page->post_content, $shortcode);
        }
        return false;
    }
}