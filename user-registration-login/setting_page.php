<?php


class User_Login_Register_Settings
{

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'User Login Register',
            'manage_options',
            'user-login-register-setting-admin',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        ?>
        <div class="wrap">
            <h1>User Login Register Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('user_login_register_option_group');
                do_settings_sections('user-login-register-setting-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        register_setting(
            'user_login_register_option_group', // Option group
            'user_login_register_option_name', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array($this, 'print_section_info'), // Callback
            'user-login-register-setting-admin' // Page
        );

        add_settings_field(
            'recaptcha_secret_key', // ID
            'Recaptcha Secret Key', // Title
            array($this, 'recaptcha_secret_key_callback'), // Callback
            'user-login-register-setting-admin', // Page
            'setting_section_id' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array
     */
    public function sanitize(array $input): array
    {
        $new_input = array();
        if (isset($input['recaptcha_secret_key']))
            $new_input['recaptcha_secret_key'] = sanitize_text_field($input['recaptcha_secret_key']);

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function recaptcha_secret_key_callback() {
        $options = get_option('recaptcha_secret_key');
        ?>
        <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key" value="<?php echo $options['recaptcha_secret_key']; ?>">
        <?php
    }



}
