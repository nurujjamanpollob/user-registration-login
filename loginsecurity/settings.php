<?php
/**
 * Login Security Settings Integration
 */

/**
 * Register login security settings
 */

function register_login_security_settings() {
    // Register settings for login security
    register_setting('user-login-settings-group', LOGIN_SECURITY_ATTEMPT_THRESHOLD_OPTION);
    register_setting('user-login-settings-group', LOGIN_SECURITY_TIME_WINDOW_OPTION);
    register_setting('user-login-settings-group', LOGIN_SECURITY_ENABLED_OPTION);
}


/**
 * Add login security fields to the settings page
 */
function add_login_security_settings_fields() {
    ?>
    <tr valign="top">
        <th scope="row">Enable Login Security</th>
        <td>
            <input type="checkbox" name="<?php echo LOGIN_SECURITY_ENABLED_OPTION; ?>"
                   value="1" <?php echo get_option(LOGIN_SECURITY_ENABLED_OPTION, 1) === '1' ? 'checked' : ''; ?>>
            <p class="description">Enable account lockout functionality after failed login attempts.</p>
        </td>
    </tr>
    
    <tr valign="top">
        <th scope="row">Failed Login Attempt Threshold</th>
        <td>
            <input type="number" name="<?php echo LOGIN_SECURITY_ATTEMPT_THRESHOLD_OPTION; ?>"
                   value="<?php echo get_option(LOGIN_SECURITY_ATTEMPT_THRESHOLD_OPTION, 5); ?>" 
                   min="1" />
            <p class="description">Number of failed login attempts before account lockout.</p>
        </td>
    </tr>
    
    <tr valign="top">
        <th scope="row">Lockout Time Window (minutes)</th>
        <td>
            <input type="number" name="<?php echo LOGIN_SECURITY_TIME_WINDOW_OPTION; ?>"
                   value="<?php echo get_option(LOGIN_SECURITY_TIME_WINDOW_OPTION, 60); ?>" 
                   min="1" max="1440"/>
            <p class="description">Time window in minutes during which failed attempts are counted.</p>
        </td>
    </tr>
    <?php
}

// Hook into the existing settings registration
add_action('admin_init', 'register_login_security_settings');

// Add fields to the setting page - hook after the existing settings are defined
add_action('admin_init', function() {
    // Add a new section for login security in settings
    add_settings_section(
        'login_security_section',
        'User Login & Registration Settings',
        null,
        'user-login-settings-group'
    );
    
    // Add fields to the login security section
    add_settings_field(
        'login_security_enabled',
        'Manage User Related Parameters',
        'add_login_security_settings_fields',
        'user-login-settings-group',
        'login_security_section'
    );
}, 20);

// Ensure login security submenu is properly added to plugin menu
add_action('admin_menu', function() {
    // Add locked accounts submenu page under the main plugin menu
    add_submenu_page(
        DASHBOARD_PAGE_SLUG,
        __('Locked Accounts', 'user-registration-login'),
        __('Locked Accounts', 'user-registration-login'), 
        'manage_options',
        'locked-accounts',
        'login_security_locked_accounts_page'
    );
}, 100);


// Function to display the locked accounts admin page
function login_security_locked_accounts_page() {
    global $login_security;
    
    if (isset($login_security) && method_exists($login_security, 'admin_page')) {
        // Call the original admin page method from LoginSecurity class
        $login_security->admin_page();
    } else {
        echo '<div class="wrap">';
        echo '<h1>' . __('Locked Accounts', 'user-registration-login') . '</h1>';
        echo '<p>' . __('The login security functionality is not properly initialized.', 'user-registration-login') . '</p>';
        echo '</div>';
    }
}