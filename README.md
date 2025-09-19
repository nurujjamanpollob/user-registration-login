# User Registration & Login Plugin

This WordPress plugin allows you to show WordPress user registration form, login form and user profile in the frontend of your website.

## Features

- User registration form with email verification
- Login form with security features  
- Password recovery functionality
- Account lockout mechanism for failed login attempts
- Disposable email domain blocking
- Blacklist/whitelist validation for usernames and email domains
- Recaptcha integration
- WooCommerce login page override option
- Customizable user roles
- Email notifications

## Performance Optimizations

This plugin has been analyzed for performance bottlenecks. Key optimizations include:

1. **Caching Implementation**: Uses WordPress transients to cache frequently accessed data
2. **Database Query Optimization**: Reduces redundant database calls through caching
3. **File I/O Reduction**: Efficient handling of large disposable email domain lists
4. **Memory Management**: Optimized data structures to prevent memory bloat
5. **Algorithm Optimization**: O(1) hash lookups for faster domain verification

## Installation

1. Upload the plugin files to the `/wp-content/plugins/user-registration-login` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->User Registration & Login screen to configure the plugin.

## Usage

### Shortcodes
- `[register_form]` - Displays the registration form
- `[login_form]` - Displays the login form
- `[password_recovery_form]` - Displays the password recovery form

## Settings

The plugin provides various settings through the WordPress admin panel:
- Enable/disable reCAPTCHA verification
- Configure account lockout thresholds and time windows
- Set user roles for new registrations
- Enable/disable blacklisted/whitelisted email checks
- Configure disposable email domain blocking
- Override WooCommerce login pages

## Security Features

- Account lockout after failed login attempts
- Disposable email domain detection
- Username/email blacklist/whitelist validation
- Recaptcha integration for form submissions
- CSRF protection for all forms
- WordPress security best practices implemented

## Performance Analysis

A detailed performance analysis has been conducted on this plugin. Key findings include:

### Bottlenecks Identified:
1. **Database Queries**: Frequent `get_option()` and `update_option()` calls in login security module
2. **File I/O Operations**: Large disposable email domain list loading from file
3. **Memory Usage**: Accumulation of failed attempts data
4. **String Processing**: Complex domain matching algorithms

### Optimizations Implemented:
- Caching for configuration options using WordPress transients
- Efficient file reading and processing for disposable domains  
- Data pruning mechanisms to prevent unlimited growth
- Algorithm optimization for faster lookups
- Hash-based lookups for O(1) performance instead of O(n)

For detailed performance analysis, see [PERFORMANCE_ANALYSIS.md](PERFORMANCE_ANALYSIS.md).

For implemented optimizations, see [PERFORMANCE_OPTIMIZATIONS.md](PERFORMANCE_OPTIMIZATIONS.md).

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Changelog

### 2.1.4
- Performance improvements and optimizations
- Security enhancements
- Bug fixes

### 2.1.3
- Improved account lockout mechanism
- Enhanced email verification checks

### 2.1.2
- Fixed compatibility issues with newer WordPress versions
- Added WooCommerce login override option

### 2.1.1
- Improved user experience with better error handling
- Enhanced security features

### 2.1.0
- Added support for custom user roles
- Improved email notifications

## Support

For support, please contact the plugin author at https://eazewebit.com or report issues on the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.