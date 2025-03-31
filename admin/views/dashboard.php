<div class="security-checker wrap">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <span class="text-blue-600">Security</span> Checker
            <span class="ml-2 text-yellow-500 text-xl"><?php echo SECURITY_CHECKER_VERSION; ?></span>
        </h1>
        <div class="flex space-x-2">
            <button id="refresh-scan" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-sync-alt mr-2"></i> Rescan
            </button>
        </div>
    </div>

    <?php if (empty($vulnerabilities)) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <p>No security vulnerabilities detected!</p>
        </div>
    <?php else : ?>
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Bulk Actions</h2>
            <div class="flex space-x-4">
                <button class="bulk-action px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" data-action="quarantine">
                    <i class="fas fa-shield-alt mr-2"></i> Quarantine All
                </button>
                <button class="bulk-action px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600" data-action="update">
                    <i class="fas fa-sync-alt mr-2"></i> Update All
                </button>
                <button class="bulk-action px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800" data-action="uninstall">
                    <i class="fas fa-trash-alt mr-2"></i> Uninstall All
                </button>
            </div>
        </div>

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
                                count(array_filter($vulnerabilities, fn($item) => $item['type'] === 'plugin')),
                                'kws-security-checker'
                            ),
                            count(array_filter($vulnerabilities, fn($item) => $item['type'] === 'plugin'))
                        );
                        ?>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($vulnerabilities as $item) : ?>
                        <?php if ($item['type'] === 'plugin') : ?>
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?php echo esc_html($item['name']); ?></h4>
                                        <p class="text-sm text-gray-500">Version <?php echo esc_html($item['version']); ?></p>
                                        <div class="mt-2">
                                            <?php foreach ($item['sites'] as $site) : ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                    <?php echo esc_html($site['name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="item-action px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600" 
                                                data-type="plugin" 
                                                data-action="quarantine" 
                                                data-identifier="<?php echo esc_attr($item['file']); ?>">
                                            <i class="fas fa-shield-alt mr-1"></i> <?php _e('Quarantine', 'kws-security-checker'); ?>
                                        </button>
                                        <button class="item-action px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600" 
                                                data-type="plugin" 
                                                data-action="update" 
                                                data-identifier="<?php echo esc_attr($item['file']); ?>">
                                            <i class="fas fa-sync-alt mr-1"></i> Update
                                        </button>
                                        <button class="item-action px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800" 
                                                data-type="plugin" 
                                                data-action="uninstall" 
                                                data-identifier="<?php echo esc_attr($item['file']); ?>">
                                            <i class="fas fa-trash-alt mr-1"></i> Uninstall
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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
                                count(array_filter($vulnerabilities, fn($item) => $item['type'] === 'theme')),
                                'kws-security-checker'
                            ),
                            count(array_filter($vulnerabilities, fn($item) => $item['type'] === 'theme'))
                        );
                        ?>
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($vulnerabilities as $item) : ?>
                        <?php if ($item['type'] === 'theme') : ?>
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900"><?php echo esc_html($item['name']); ?></h4>
                                        <p class="text-sm text-gray-500">Version <?php echo esc_html($item['version']); ?></p>
                                        <div class="mt-2">
                                            <?php foreach ($item['sites'] as $site) : ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                                    <?php echo esc_html($site['name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="item-action px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600" 
                                                data-type="theme" 
                                                data-action="quarantine" 
                                                data-identifier="<?php echo esc_attr($item['slug']); ?>">
                                            <i class="fas fa-shield-alt mr-1"></i> Quarantine
                                        </button>
                                        <button class="item-action px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600" 
                                                data-type="theme" 
                                                data-action="update" 
                                                data-identifier="<?php echo esc_attr($item['slug']); ?>">
                                            <i class="fas fa-sync-alt mr-1"></i> Update
                                        </button>
                                        <button class="item-action px-3 py-1 bg-gray-700 text-white rounded text-sm hover:bg-gray-800" 
                                                data-type="theme" 
                                                data-action="uninstall" 
                                                data-identifier="<?php echo esc_attr($item['slug']); ?>">
                                            <i class="fas fa-trash-alt mr-1"></i> Uninstall
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-bold mb-4" id="modal-title">Confirm Action</h3>
            <p class="mb-6" id="modal-message">Are you sure you want to perform this action?</p>
            <div class="flex justify-end space-x-3">
                <button id="modal-cancel" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                <button id="modal-confirm" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Confirm</button>
            </div>
        </div>
    </div>
</div>