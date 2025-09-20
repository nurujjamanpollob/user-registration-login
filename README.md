# User Registration & Login Plugin

A WordPress plugin for user registration, login, and account security with lockout functionality.

## Features

- User registration form with email validation
- Login form with reCAPTCHA support  
- Account lockout mechanism for failed login attempts
- Disposable email domain blocking
- Whitelist/blacklist functionality for usernames and email domains
- WooCommerce integration support

## Security Audit

This plugin has undergone a comprehensive security audit. Please review the detailed security report:

[SECURITY_AUDIT_REPORT.md](SECURITY_AUDIT_REPORT.md)

## Installation

1. Upload the plugin files to the `/wp-content/plugins/user-registration-login` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure settings through the plugin menu in WordPress admin.

## Usage

Add shortcodes to pages:
- `[register_form]` - Registration form
- `[login_form]` - Login form

## Security Notes

?? **Critical Security Issues Identified**

This plugin contains several security vulnerabilities that require immediate attention before deployment:

1. Account lockout bypass issues
2. Inadequate CSRF protection in AJAX endpoints  
3. Improper input sanitization
4. Direct database access without validation

Please review the [SECURITY_AUDIT_REPORT.md](SECURITY_AUDIT_REPORT.md) for detailed vulnerability information and remediation steps.

## License

This plugin is licensed under the GPL v2 or later.