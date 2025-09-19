<?php
/**
 * Bootstrap file for PHPUnit tests
 */

// Define constants needed for WordPress
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../../../../' );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

// Load WordPress
require_once ABSPATH . 'wp-settings.php';

// Load the plugin
$plugin_file = dirname(__FILE__) . '/../index.php';
if (file_exists($plugin_file)) {
    require_once $plugin_file;
}