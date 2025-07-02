<?php

/**
 * Used to override the WooCommerce login page for guests/non-logged-in users
 * @author nurujjamanpollob
 * @version 1.0.0
 */
class WooCommerceLoginPageOverrider
{
    public function __construct()
    {
        // add action to override the WooCommerce login page(listen on template_redirect)
        add_action('template_redirect', [$this, 'ural_my_account_guest_override']);
    }

    /**
     * We are overriding the WooCommerce my account page for guests/non-logged-in users
     * This will replace the content of the account page with a login form, the default WooCommerce login form will be replaced
     *
     * The js replaces the login form with the login form shortcode. This method may be not reliable, in that case, change the js code logic to replace the login form with the login form shortcode.
     */
    function ural_my_account_guest_override() {
        if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in()) {
            add_action('wp_footer', function() {
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var wc = document.querySelector('.woocommerce');
                        if (wc) {
                            wc.innerHTML = '';
                            var container = document.createElement('div');
                            container.innerHTML = <?php echo json_encode(do_shortcode('[login_form]')); ?>;
                            wc.appendChild(container);
                        }

                        // wc not present, try to get any form, and replace it with the login form
                        var form = document.querySelector('form');
                        if (form) {
                            form.innerHTML = '';
                            var container = document.createElement('div');
                            container.innerHTML = <?php echo json_encode(do_shortcode('[login_form]')); ?>;
                            form.appendChild(container);
                        }
                    });
                </script>
                <?php
            });
        }
    }
}
