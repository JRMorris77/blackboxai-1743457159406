<?php
/**
 * Plugin Name: KWS Security Checker
 * Description: Checks for security bulletins in installed themes/plugins and allows quarantining or updating.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: kws-security-checker
 * Domain Path: /languages
 * Network: true
 * License: GPL2
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('KWS_SECURITY_CHECKER_VERSION', '1.0.0');
define('KWS_SECURITY_CHECKER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KWS_SECURITY_CHECKER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load translations
add_action('plugins_loaded', function() {
    load_plugin_textdomain(
        'kws-security-checker', 
        false, 
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
});

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'security_checker_activate');
register_deactivation_hook(__FILE__, 'security_checker_deactivate');

function security_checker_activate() {
    if (is_multisite()) {
        add_site_option('kws_security_checker_network_active', true);
    }
    require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'includes/class-install.php';
    if (class_exists('KWS\SecurityChecker\Install')) {
        KWS\SecurityChecker\Install::install();
    } else {
        error_log('KWS Security Checker: Failed to load Install class during activation');
    }
}

function security_checker_deactivate() {
    require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'includes/class-install.php';
    if (class_exists('KWS\SecurityChecker\Install')) {
        KWS\SecurityChecker\Install::uninstall();
    } else {
        error_log('KWS Security Checker: Failed to load Install class during deactivation');
    }
}

// Load the main plugin class
require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'includes/class-security-checker.php';
if (class_exists('KWS\SecurityChecker\SecurityChecker')) {
    KWS\SecurityChecker\SecurityChecker::init();
} else {
    error_log('KWS Security Checker: Failed to load SecurityChecker class');
}

// Load admin functionality
if (is_admin()) {
    require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'admin/class-admin.php';
    if (class_exists('KWS\SecurityChecker\Admin')) {
        KWS\SecurityChecker\Admin::init();
    } else {
        error_log('KWS Security Checker: Failed to load Admin class');
    }
}
