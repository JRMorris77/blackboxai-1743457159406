<?php
if (!defined('ABSPATH')) {
    exit;
}

$security_checker = \KWS\SecurityChecker\SecurityChecker::init();
$vulnerabilities = $security_checker->get_vulnerabilities();

// Convert REST response to array if needed
$vulnerabilities_data = is_a($vulnerabilities, 'WP_REST_Response') 
    ? $vulnerabilities->get_data() 
    : (array)$vulnerabilities;
?>
<div class="security-checker wrap">
    <!-- [Previous HTML content remains exactly the same until vulnerabilities usage] -->
    
    <?php if (empty($vulnerabilities_data)) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <p><?php _e('No security vulnerabilities detected!', 'kws-security-checker'); ?></p>
        </div>
    <?php else : ?>
        <!-- [Bulk actions HTML] -->

        <div class="space-y-8">
            <!-- Plugins Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-plug mr-2 text-blue-500"></i>
                        <?php 
                        printf(
                            _n(
                                'Vulnerable Plugin (%d)',
                                'Vulnerable Plugins (%d)',
                                count(array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'plugin')),
                                'kws-security-checker'
                            ),
                            count(array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'plugin'))
                        );
                        ?>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach (array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'plugin') as $item) : ?>
                        <!-- [Plugin item HTML] -->
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Themes Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-paint-brush mr-2 text-purple-500"></i>
                        <?php 
                        printf(
                            _n(
                                'Vulnerable Theme (%d)',
                                'Vulnerable Themes (%d)',
                                count(array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'theme')),
                                'kws-security-checker'
                            ),
                            count(array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'theme'))
                        );
                        ?>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach (array_filter($vulnerabilities_data, fn($item) => $item['type'] === 'theme') as $item) : ?>
                        <!-- [Theme item HTML] -->
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- [Modal HTML remains the same] -->
</div>