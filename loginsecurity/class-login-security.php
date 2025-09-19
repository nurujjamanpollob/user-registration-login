<?php

/**
 * Login Security Class for WordPress Plugin
 * Implements account lockout functionality for failed login attempts
 */
class LoginSecurity {
    
    /**
     * Plugin version
     */
    const VERSION = '1.0.0';
    
    /**
     * Option name for failed attempts tracking
     */
    const FAILED_ATTEMPTS_OPTION = 'login_security_failed_attempts';
    
    /**
     * Option name for locked accounts
     */
    const LOCKED_ACCOUNTS_OPTION = 'login_security_locked_accounts';
    
    /**
     * Option name for lockout threshold
     */
    const ATTEMPT_THRESHOLD_OPTION = 'login_security_attempt_threshold';
    
    /**
     * Option name for time window (in minutes)
     */
    const TIME_WINDOW_OPTION = 'login_security_time_window';
    
    /**
     * Option name for security toggle
     */
    const SECURITY_ENABLED_OPTION = 'login_security_enabled';
    
    /**
     * Default attempt threshold
     */
    const DEFAULT_ATTEMPT_THRESHOLD = 5;
    
    /**
     * Default time window in minutes
     */
    const DEFAULT_TIME_WINDOW = 60;
    
    /**
     * Default security enabled
     */
    const DEFAULT_SECURITY_ENABLED = false;
    
    /**
     * Transient name for failed attempts tracking (for caching)
     */
    const FAILED_ATTEMPTS_TRANSIENT = 'login_security_failed_attempts';
    
    /**
     * Transient name for locked accounts (for caching)
     */
    const LOCKED_ACCOUNTS_TRANSIENT = 'login_security_locked_accounts';
    
    /**
     * Transient name for configuration options
     */
    const CONFIGURATION_TRANSIENT = 'login_security_config';
    
    /**
     * Maximum number of failed attempts to store before pruning
     */
    const MAX_FAILED_ATTEMPTS_STORED = 1000;
    
    public function __construct() {
        // Register activation hook to set up database options
        add_action('plugins_loaded', array($this, 'init'));
        
        // Hook into WordPress login process
        add_action('wp_login_failed', array($this, 'handle_failed_login'));
        add_filter('wp_authenticate_user', array($this, 'check_account_lockout'), 10, 2);
        
        // Add admin menu for account unlocking
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Handle AJAX requests for admin unlock functionality
        add_action('wp_ajax_unlock_account', array($this, 'ajax_unlock_account'));
        add_action('wp_ajax_unlock_accounts_bulk', array($this, 'ajax_unlock_accounts_bulk'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Set default options if they don't exist
        if (!get_option(self::ATTEMPT_THRESHOLD_OPTION)) {
            update_option(self::ATTEMPT_THRESHOLD_OPTION, self::DEFAULT_ATTEMPT_THRESHOLD);
        }
        
        if (!get_option(self::TIME_WINDOW_OPTION)) {
            update_option(self::TIME_WINDOW_OPTION, self::DEFAULT_TIME_WINDOW);
        }
        
        if (!get_option(self::SECURITY_ENABLED_OPTION)) {
            update_option(self::SECURITY_ENABLED_OPTION, self::DEFAULT_SECURITY_ENABLED);
        }
    }
    
    /**
     * Handle failed login attempts
     *
     * @param string $username The username that failed to log in
     */
    public function handle_failed_login($username) {
        // If security is disabled, do nothing
        if (!get_option(self::SECURITY_ENABLED_OPTION)) {
            return;
        }
        
        // Get current failed attempts with caching
        $failed_attempts = $this->get_cached_failed_attempts();
        
        // Get current locked accounts
        $locked_accounts = $this->get_cached_locked_accounts();
        
        // Get configuration options with caching
        $threshold = $this->get_configuration_option(self::ATTEMPT_THRESHOLD_OPTION, self::DEFAULT_ATTEMPT_THRESHOLD);
        $time_window = $this->get_configuration_option(self::TIME_WINDOW_OPTION, self::DEFAULT_TIME_WINDOW);
        
        // Check if user is already locked
        if (isset($locked_accounts[$username]) && $locked_accounts[$username]['locked']) {
            return;
        }
        
        // Initialize attempts for this user if not exists
        if (!isset($failed_attempts[$username])) {
            $failed_attempts[$username] = array();
        }
        
        // Add current failed attempt timestamp
        $failed_attempts[$username][] = time();
        
        // Clean up old attempts outside of time window
        $current_time = time();
        $failed_attempts[$username] = array_filter($failed_attempts[$username], function($timestamp) use ($current_time, $time_window) {
            return ($current_time - $timestamp) <= ($time_window * 60);
        });
        
        // Prune old attempts to prevent unlimited growth
        if (count($failed_attempts[$username]) > self::MAX_FAILED_ATTEMPTS_STORED) {
            // Keep only the most recent attempts
            $failed_attempts[$username] = array_slice($failed_attempts[$username], -self::MAX_FAILED_ATTEMPTS_STORED);
        }
        
        // Update failed attempts with caching
        $this->update_cached_failed_attempts($failed_attempts);
        
        // Check if user should be locked out
        if (count($failed_attempts[$username]) >= $threshold) {
            // Lock the account
            $locked_accounts[$username] = array(
                'locked' => true,
                'lockout_time' => time(),
                'attempts' => count($failed_attempts[$username])
            );
            
            $this->update_cached_locked_accounts($locked_accounts);
            
            // Clear failed attempts for this user after lockout
            unset($failed_attempts[$username]);
            $this->update_cached_failed_attempts($failed_attempts);
        }
    }
    
    /**
     * Check if account is locked out before authentication
     *
     * @param WP_User $user The user object
     * @param string $username The username being authenticated
     * @return WP_User|WP_Error
     */
    public function check_account_lockout($user, $username) {
        // If security is disabled, do nothing
        if (!get_option(self::SECURITY_ENABLED_OPTION)) {
            return $user;
        }
        
        // Get locked accounts with caching
        $locked_accounts = $this->get_cached_locked_accounts();
        
        // Check if user is locked
        if (isset($locked_accounts[$username]) && $locked_accounts[$username]['locked']) {
            // Check if lockout period has expired
            $time_window = $this->get_configuration_option(self::TIME_WINDOW_OPTION, self::DEFAULT_TIME_WINDOW);
            $lockout_time = $locked_accounts[$username]['lockout_time'];
            $current_time = time();
            
            // If lockout period hasn't expired, block access
            if (($current_time - $lockout_time) <= ($time_window * 60)) {
                return new WP_Error('account_locked', __('Your account has been locked due to too many failed login attempts. Please reset your password to regain access.', 'user-registration-login'));
            } else {
                // Lockout period expired, unlock the account
                unset($locked_accounts[$username]);
                $this->update_cached_locked_accounts($locked_accounts);
                
                // Clear any failed attempts for this user
                $failed_attempts = $this->get_cached_failed_attempts();
                unset($failed_attempts[$username]);
                $this->update_cached_failed_attempts($failed_attempts);
            }
        }
        
        return $user;
    }
    
    /**
     * Add admin menu for account unlocking
     */
    public function add_admin_menu() {
        add_submenu_page(
            'users.php',
            __('Locked Accounts', 'user-registration-login'),
            __('Locked Accounts', 'user-registration-login'),
            'manage_options',
            'locked-accounts',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Display the admin page for locked accounts
     */
    public function admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        // Process bulk unlock
        if (isset($_POST['unlock_accounts_bulk']) && !empty($_POST['locked_accounts'])) {
            $accounts = $_POST['locked_accounts'];
            foreach ($accounts as $username) {
                $this->unlock_account($username);
            }
            echo '<div class="notice notice-success"><p>' . __('Selected accounts have been unlocked.', 'user-registration-login') . '</p></div>';
        }
        
        // Get locked accounts
        $locked_accounts = $this->get_cached_locked_accounts();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Locked Accounts', 'user-registration-login'); ?></h1>
            
            <?php if (empty($locked_accounts)): ?>
                <p><?php _e('No accounts are currently locked.', 'user-registration-login'); ?></p>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="unlock_accounts_bulk" value="1" />
                    <div class="tablenav top">
                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                            <select name="action" id="bulk-action-selector-top">
                                <option value="-1"><?php _e('Bulk Actions', 'user-registration-login'); ?></option>
                                <option value="unlock"><?php _e('Unlock', 'user-registration-login'); ?></option>
                            </select>
                            <input type="submit" id="doaction" class="button action" value="<?php _e('Apply', 'user-registration-login'); ?>">
                        </div>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td scope="col" class="manage-column column-cb check-column">
                                    <input type="checkbox" id="select-all-locked-accounts" />
                                </td>
                                <th scope="col"><?php _e('Username', 'user-registration-login'); ?></th>
                                <th scope="col"><?php _e('Email', 'user-registration-login'); ?></th>
                                <th scope="col"><?php _e('Lockout Time', 'user-registration-login'); ?></th>
                                <th scope="col"><?php _e('Failed Attempts', 'user-registration-login'); ?></th>
                                <th scope="col"><?php _e('Actions', 'user-registration-login'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($locked_accounts as $username => $account_data): ?>
                                <?php
                                $user = get_user_by('login', $username);
                                if (!$user) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="locked_accounts[]" value="<?php echo esc_attr($username); ?>" />
                                    </th>
                                    <td><?php echo esc_html($username); ?></td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', $account_data['lockout_time']); ?></td>
                                    <td><?php echo esc_html($account_data['attempts']); ?></td>
                                    <td>
                                        <button type="button" class="button unlock-account-btn" 
                                                data-username="<?php echo esc_attr($username); ?>">
                                            <?php _e('Unlock', 'user-registration-login'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="tablenav bottom">
                        <div class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
                            <select name="action2" id="bulk-action-selector-bottom">
                                <option value="-1"><?php _e('Bulk Actions', 'user-registration-login'); ?></option>
                                <option value="unlock"><?php _e('Unlock', 'user-registration-login'); ?></option>
                            </select>
                            <input type="submit" id="doaction2" class="button action" value="<?php _e('Apply', 'user-registration-login'); ?>">
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.unlock-account-btn').click(function() {
                    var username = $(this).data('username');
                    if (confirm('<?php _e('Are you sure you want to unlock this account?', 'user-registration-login'); ?>')) {
                        $.post(ajaxurl, {
                            action: 'unlock_account',
                            username: username,
                            nonce: '<?php echo wp_create_nonce('unlock_account_nonce'); ?>'
                        }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('<?php _e('Error unlocking account:', 'user-registration-login'); ?> ' + response.data);
                            }
                        });
                    }
                });
                
                $('#select-all-locked-accounts').click(function() {
                    var checked = $(this).is(':checked');
                    $('input[name="locked_accounts[]"]').prop('checked', checked);
                });
            });
        </script>
        <?php
    }
    
    /**
     * AJAX handler to unlock a single account
     */
    public function ajax_unlock_account() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'unlock_account_nonce')) {
            wp_die(__('Security check failed', 'user-registration-login'));
        }
        
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'user-registration-login'));
        }
        
        $username = sanitize_text_field($_POST['username']);
        
        if ($this->unlock_account($username)) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to unlock account', 'user-registration-login'));
        }
    }
    
    /**
     * AJAX handler to unlock multiple accounts
     */
    public function ajax_unlock_accounts_bulk() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'unlock_account_nonce')) {
            wp_die(__('Security check failed', 'user-registration-login'));
        }
        
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'user-registration-login'));
        }
        
        $accounts = $_POST['accounts'];
        
        foreach ($accounts as $username) {
            $this->unlock_account(sanitize_text_field($username));
        }
        
        wp_send_json_success();
    }
    
    /**
     * Unlock a specific account
     *
     * @param string $username The username to unlock
     * @return bool True if successful, false otherwise
     */
    public function unlock_account($username) {
        $locked_accounts = $this->get_cached_locked_accounts();
        
        if (isset($locked_accounts[$username])) {
            unset($locked_accounts[$username]);
            $this->update_cached_locked_accounts($locked_accounts);
            
            // Clear any failed attempts for this user
            $failed_attempts = $this->get_cached_failed_attempts();
            unset($failed_attempts[$username]);
            $this->update_cached_failed_attempts($failed_attempts);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get lockout status for a user
     *
     * @param string $username The username to check
     * @return array Lockout information
     */
    public function get_lockout_status($username) {
        $locked_accounts = $this->get_cached_locked_accounts();
        
        if (isset($locked_accounts[$username])) {
            return $locked_accounts[$username];
        }
        
        return false;
    }
    
    /**
     * Get all locked accounts
     *
     * @return array List of locked accounts
     */
    public function get_locked_accounts() {
        return $this->get_cached_locked_accounts();
    }
    
    /**
     * Get current configuration settings
     *
     * @return array Configuration options
     */
    public function get_settings() {
        return array(
            'enabled' => $this->get_configuration_option(self::SECURITY_ENABLED_OPTION, self::DEFAULT_SECURITY_ENABLED),
            'threshold' => $this->get_configuration_option(self::ATTEMPT_THRESHOLD_OPTION, self::DEFAULT_ATTEMPT_THRESHOLD),
            'time_window' => $this->get_configuration_option(self::TIME_WINDOW_OPTION, self::DEFAULT_TIME_WINDOW)
        );
    }
    
    /**
     * Update configuration settings
     *
     * @param array $settings The new settings to update
     */
    public function update_settings($settings) {
        if (isset($settings['enabled'])) {
            update_option(self::SECURITY_ENABLED_OPTION, (bool)$settings['enabled']);
            delete_transient(self::CONFIGURATION_TRANSIENT); // Clear cache
        }
        if (isset($settings['threshold'])) {
            update_option(self::ATTEMPT_THRESHOLD_OPTION, (int)$settings['threshold']);
            delete_transient(self::CONFIGURATION_TRANSIENT); // Clear cache
        }
        if (isset($settings['time_window'])) {
            update_option(self::TIME_WINDOW_OPTION, (int)$settings['time_window']);
            delete_transient(self::CONFIGURATION_TRANSIENT); // Clear cache
        }
    }
    
    /**
     * Get cached failed attempts with fallback to database
     *
     * @return array Failed attempts data
     */
    private function get_cached_failed_attempts() {
        $failed_attempts = get_transient(self::FAILED_ATTEMPTS_TRANSIENT);
        
        if ($failed_attempts === false) {
            $failed_attempts = get_option(self::FAILED_ATTEMPTS_OPTION, array());
            set_transient(self::FAILED_ATTEMPTS_TRANSIENT, $failed_attempts, 300); // Cache for 5 minutes
        }
        
        return $failed_attempts;
    }
    
    /**
     * Update cached failed attempts
     *
     * @param array $failed_attempts Failed attempts data to cache
     */
    private function update_cached_failed_attempts($failed_attempts) {
        update_option(self::FAILED_ATTEMPTS_OPTION, $failed_attempts);
        set_transient(self::FAILED_ATTEMPTS_TRANSIENT, $failed_attempts, 300); // Cache for 5 minutes
    }
    
    /**
     * Get cached locked accounts with fallback to database
     *
     * @return array Locked accounts data
     */
    private function get_cached_locked_accounts() {
        $locked_accounts = get_transient(self::LOCKED_ACCOUNTS_TRANSIENT);
        
        if ($locked_accounts === false) {
            $locked_accounts = get_option(self::LOCKED_ACCOUNTS_OPTION, array());
            set_transient(self::LOCKED_ACCOUNTS_TRANSIENT, $locked_accounts, 300); // Cache for 5 minutes
        }
        
        return $locked_accounts;
    }
    
    /**
     * Update cached locked accounts
     *
     * @param array $locked_accounts Locked accounts data to cache
     */
    private function update_cached_locked_accounts($locked_accounts) {
        update_option(self::LOCKED_ACCOUNTS_OPTION, $locked_accounts);
        set_transient(self::LOCKED_ACCOUNTS_TRANSIENT, $locked_accounts, 300); // Cache for 5 minutes
    }
    
    /**
     * Get configuration option with caching
     *
     * @param string $option_name Option name to get
     * @param mixed $default Default value if option doesn't exist
     * @return mixed Option value or default
     */
    private function get_configuration_option($option_name, $default) {
        $config = get_transient(self::CONFIGURATION_TRANSIENT);
        
        if ($config === false) {
            $config = array(
                self::ATTEMPT_THRESHOLD_OPTION => get_option(self::ATTEMPT_THRESHOLD_OPTION, self::DEFAULT_ATTEMPT_THRESHOLD),
                self::TIME_WINDOW_OPTION => get_option(self::TIME_WINDOW_OPTION, self::DEFAULT_TIME_WINDOW),
                self::SECURITY_ENABLED_OPTION => get_option(self::SECURITY_ENABLED_OPTION, self::DEFAULT_SECURITY_ENABLED)
            );
            set_transient(self::CONFIGURATION_TRANSIENT, $config, 3600); // Cache for 1 hour
        }
        
        return isset($config[$option_name]) ? $config[$option_name] : $default;
    }
}