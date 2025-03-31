jQuery(document).ready(function($) {
    // Initialize Font Awesome
    $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">');

    // Handle item actions
    $('.item-action').on('click', function() {
        const button = $(this);
        const action = button.data('action');
        const type = button.data('type');
        const identifier = button.data('identifier');
        
        showConfirmationModal(
            `Are you sure you want to ${action} this ${type}?`,
            { action, type, identifier }
        );
    });

    // Handle bulk actions
    $('.bulk-action').on('click', function() {
        const action = $(this).data('action');
        const type = $('.item-action').first().data('type'); // Get type from first item
        
        showConfirmationModal(
            `Are you sure you want to ${action} ALL vulnerable items?`,
            { action, type, bulk: true }
        );
    });

    // Refresh scan button
    $('#refresh-scan').on('click', function() {
        location.reload();
    });

    // Confirmation modal functions
    let currentAction = null;

    function showConfirmationModal(message, actionData) {
        currentAction = actionData;
        $('#modal-message').text(message);
        $('#confirmation-modal').removeClass('hidden').addClass('flex');
    }

    function hideConfirmationModal() {
        $('#confirmation-modal').removeClass('flex').addClass('hidden');
        currentAction = null;
    }

    $('#modal-cancel').on('click', hideConfirmationModal);
    $('#modal-confirm').on('click', function() {
        if (!currentAction) {
            hideConfirmationModal();
            return;
        }

        const { action, type, identifier, bulk } = currentAction;
        const data = {
            action: 'security_checker_action',
            nonce: securityChecker.nonce,
            action_type: action,
            item_type: type
        };

        if (!bulk) {
            data.item_identifier = identifier;
        }

        // Show loading state
        $('#modal-confirm').html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...').prop('disabled', true);

        $.post(securityChecker.ajax_url, data, function(response) {
            if (response.success) {
                showToast(response.data.message, 'success');
                if (bulk || action === 'uninstall') {
                    // Reload for bulk actions or uninstalls
                    setTimeout(() => location.reload(), 1500);
                } else {
                    // Just hide modal for single actions
                    hideConfirmationModal();
                }
            } else {
                showToast(response.data.message, 'error');
                hideConfirmationModal();
            }
        }).fail(function() {
            showToast('An error occurred. Please try again.', 'error');
            hideConfirmationModal();
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="fixed top-4 right-4 z-50 px-4 py-2 rounded shadow-lg text-white toast-${type}">
                ${message}
            </div>
        `);

        if (type === 'success') {
            toast.addClass('bg-green-500');
        } else {
            toast.addClass('bg-red-500');
        }

        $('body').append(toast);
        setTimeout(() => toast.fadeOut(500, () => toast.remove()), 3000);
    }
});