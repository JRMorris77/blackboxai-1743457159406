<?php
namespace KWS\SecurityChecker;

use function __;
use function _n;
use function _x;

class SecurityChecker {
    private static $instance = null;
    private $vulnerable_items = [];

    public static function init() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_loaded', [$this, 'check_for_vulnerabilities']);
    }

    public function check_for_vulnerabilities() {
        $this->vulnerable_items = $this->get_vulnerable_items();
    }

    public function get_vulnerable_items() {
        $items = [];
        
        // Check plugins
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugins = get_plugins();
        foreach ($plugins as $plugin_file => $plugin_data) {
            if ($this->has_security_update($plugin_file)) {
            $items[] = [
                'type' => 'plugin',
                'file' => $plugin_file,
                'name' => $plugin_data['Name'],
                'version' => $plugin_data['Version'],
                'sites' => $this->get_affected_sites($plugin_file, 'plugin')
            ];
            }
        }

        // Check themes
        $themes = wp_get_themes();
        foreach ($themes as $theme_slug => $theme_data) {
            if ($this->has_security_update($theme_slug, 'theme')) {
                $items[] = [
                    'type' => 'theme',
                    'slug' => $theme_slug,
                    'name' => $theme_data->get('Name'),
                    'version' => $theme_data->get('Version'),
                    'sites' => $this->get_affected_sites($theme_slug, 'theme')
                ];
            }
        }

        return $items;
    }

    private function has_security_update($item, $type = 'plugin') {
        // This would be replaced with actual security bulletin checks
        // For now, we'll simulate finding vulnerable items
        // Simulated vulnerable items with translatable names
        $vulnerable_items = [
            'plugin' => [
                'akismet/akismet.php' => __('Akismet Anti-Spam', 'kws-security-checker'),
                'hello.php' => __('Hello Dolly', 'kws-security-checker')
            ],
            'theme' => [
                'twentytwentyone' => __('Twenty Twenty-One', 'kws-security-checker')
            ]
        ];

        return array_key_exists($item, $vulnerable_items[$type]);
    }

    private function get_affected_sites($item, $type = 'plugin') {
        $sites = [];
        
        if (is_multisite()) {
            foreach (get_sites() as $site) {
                switch_to_blog($site->blog_id);
                
                if ($type === 'plugin' && is_plugin_active($item)) {
                    $sites[] = [
                        'id' => $site->blog_id,
                        'name' => get_bloginfo('name'),
                        'url' => get_site_url()
                    ];
                } elseif ($type === 'theme' && get_stylesheet() === $item) {
                    $sites[] = [
                        'id' => $site->blog_id,
                        'name' => get_bloginfo('name'),
                        'url' => get_site_url()
                    ];
                }
                
                restore_current_blog();
            }
        } else {
            if (($type === 'plugin' && is_plugin_active($item)) || 
                ($type === 'theme' && get_stylesheet() === $item)) {
                $sites[] = [
                    'id' => get_current_blog_id(),
                    'name' => get_bloginfo('name'),
                    'url' => get_site_url()
                ];
            }
        }

        return $sites;
    }

    public function quarantine_item($type, $identifier, $site_id = null) {
        if ($type === 'plugin') {
            return $this->quarantine_plugin($identifier, $site_id);
        } elseif ($type === 'theme') {
            return $this->quarantine_theme($identifier, $site_id);
        }
        return false;
    }

    private function quarantine_plugin($plugin_file, $site_id = null) {
        if ($site_id) {
            switch_to_blog($site_id);
            $result = deactivate_plugins($plugin_file);
            restore_current_blog();
        } else {
            if (is_multisite()) {
                foreach (get_sites() as $site) {
                    switch_to_blog($site->blog_id);
                    if (is_plugin_active($plugin_file)) {
                        deactivate_plugins($plugin_file);
                    }
                    restore_current_blog();
                }
                $result = true;
            } else {
                $result = deactivate_plugins($plugin_file);
            }
        }

        if ($result) {
            $quarantined = get_site_option('security_checker_quarantined_plugins', []);
            $quarantined[$plugin_file] = time();
            update_site_option('security_checker_quarantined_plugins', $quarantined);
        }

        return $result;
    }

    private function quarantine_theme($theme_slug, $site_id = null) {
        // Implementation would switch themes to default before quarantining
        // Similar structure to quarantine_plugin()
        return true;
    }

    public function get_vulnerabilities() {
        return $this->vulnerable_items;
    }
}