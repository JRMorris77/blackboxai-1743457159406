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
    KWS\SecurityChecker\Install::install();
}

function security_checker_deactivate() {
    require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'includes/class-install.php';
    KWS\SecurityChecker\Install::uninstall();
}

// Load the main plugin class
require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'includes/class-security-checker.php';
KWS\SecurityChecker\SecurityChecker::init();

// Load admin functionality
if (is_admin()) {
    require_once KWS_SECURITY_CHECKER_PLUGIN_DIR . 'admin/class-admin.php';
    KWS\SecurityChecker\Admin::init();
}
