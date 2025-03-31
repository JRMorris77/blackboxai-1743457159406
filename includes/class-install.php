<?php
class SecurityChecker_Install {
    public static function install() {
        self::create_tables();
        self::set_default_options();
    }

    public static function uninstall() {
        self::remove_options();
        self::maybe_drop_tables();
    }

    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'security_checker_logs';

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            log_type varchar(20) NOT NULL,
            item_type varchar(10) NOT NULL,
            item_name varchar(255) NOT NULL,
            item_version varchar(20) NOT NULL,
            action_taken varchar(20) NOT NULL,
            site_id bigint(20) DEFAULT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY log_type (log_type),
            KEY item_type (item_type),
            KEY site_id (site_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private static function maybe_drop_tables() {
        global $wpdb;

        // Only drop tables if network deactivated on multisite
        if (is_multisite() && get_site_option('security_checker_network_active')) {
            return;
        }

        $table_name = $wpdb->prefix . 'security_checker_logs';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    private static function set_default_options() {
        if (is_multisite()) {
            add_site_option('security_checker_scan_interval', 'daily');
            add_site_option('security_checker_notify_admins', 1);
            add_site_option('security_checker_quarantined_plugins', []);
            add_site_option('security_checker_quarantined_themes', []);
        } else {
            add_option('security_checker_scan_interval', 'daily');
            add_option('security_checker_notify_admins', 1);
            add_option('security_checker_quarantined_plugins', []);
            add_option('security_checker_quarantined_themes', []);
        }
    }

    private static function remove_options() {
        if (is_multisite()) {
            delete_site_option('security_checker_scan_interval');
            delete_site_option('security_checker_notify_admins');
            delete_site_option('security_checker_quarantined_plugins');
            delete_site_option('security_checker_quarantined_themes');
            delete_site_option('security_checker_network_active');
        } else {
            delete_option('security_checker_scan_interval');
            delete_option('security_checker_notify_admins');
            delete_option('security_checker_quarantined_plugins');
            delete_option('security_checker_quarantined_themes');
        }
    }
}