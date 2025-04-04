<?php
namespace KWS\SecurityChecker;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use function __;
use function _n;
use function _x;

class Admin {
    private static $instance = null;

    public static function init() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_security_checker_action', [$this, 'handle_ajax_request']);
    }

    public function add_admin_menu() {
        $capability = is_multisite() ? 'manage_network' : 'manage_options';
        add_menu_page(
            __('Security Checker', 'kws-security-checker'),
            __('Security Checker', 'kws-security-checker'),
            $capability,
            'security-checker',
            [$this, 'render_dashboard'],
            'dashicons-shield',
            80
        );
    }

    public function enqueue_assets($hook) {
        if ('toplevel_page_security-checker' !== $hook) {
            return;
        }

        // Enqueue assets with version-specific dependencies
        $style_deps = [];
        $script_deps = ['jquery'];
        
        if (version_compare(get_bloginfo('version'), '6.7', '>=')) {
            $style_deps = ['wp-components'];
            $script_deps = ['jquery', 'wp-api-fetch'];
        }

        // Enqueue Tailwind CSS
        wp_enqueue_style(
            'security-checker-tailwind',
            'https://cdn.tailwindcss.com',
            $style_deps,
            \KWS_SECURITY_CHECKER_VERSION
        );

        // Enqueue custom styles
        wp_enqueue_style(
            'security-checker-admin',
            \KWS_SECURITY_CHECKER_PLUGIN_URL . 'admin/assets/styles.css',
            [],
            \KWS_SECURITY_CHECKER_VERSION
        );

        // Enqueue scripts
        wp_enqueue_script(
            'security-checker-admin',
            \KWS_SECURITY_CHECKER_PLUGIN_URL . 'admin/assets/scripts.js',
            ['jquery'],
            \KWS_SECURITY_CHECKER_VERSION,
            true
        );

        wp_localize_script(
            'security-checker-admin',
            'securityChecker',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('security-checker-nonce')
            ]
        );
    }

    public function render_dashboard() {
        $security_checker = \KWS\SecurityChecker\SecurityChecker::init();
        $vulnerabilities = $security_checker->get_vulnerabilities();
        
        include \KWS_SECURITY_CHECKER_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function handle_ajax_request() {
        check_ajax_referer('security-checker-nonce', 'nonce');

        if (!current_user_can(is_multisite() ? 'manage_network' : 'manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }

        $action = sanitize_text_field($_POST['action_type'] ?? '');
        $type = sanitize_text_field($_POST['item_type'] ?? '');
        $identifier = sanitize_text_field($_POST['item_identifier'] ?? '');
        $site_id = isset($_POST['site_id']) ? absint($_POST['site_id']) : null;

        $security_checker = \KWS\SecurityChecker\SecurityChecker::init();
        $result = false;
        $message = '';

        switch ($action) {
            case 'quarantine':
                $result = $security_checker->quarantine_item($type, $identifier, $site_id);
                $message = $result ? 'Item quarantined successfully' : 'Failed to quarantine item';
                break;
            case 'update':
                // Implementation for update action
                $message = 'Update functionality will be implemented';
                break;
            case 'uninstall':
                // Implementation for uninstall action
                $message = 'Uninstall functionality will be implemented';
                break;
            default:
                $message = 'Invalid action';
        }

        if ($result) {
            wp_send_json_success(['message' => $message]);
        } else {
            wp_send_json_error(['message' => $message]);
        }
    }
}