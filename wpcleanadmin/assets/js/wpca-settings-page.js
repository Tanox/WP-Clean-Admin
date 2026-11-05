(function($) {
    'use strict';

    $(document).ready(function() {
        // Settings tabs functionality
        const tabButtons = $('.wpca-tab-button');
        const tabContents = $('.wpca-tab-content');

        tabButtons.on('click', function() {
            const tabId = $(this).data('tab');

            // Remove active class from all tabs
            tabButtons.removeClass('active');
            tabContents.removeClass('active');

            // Add active class to selected tab
            $(this).addClass('active');
            $(`#wpca-tab-${tabId}`).addClass('active');

            // Scroll to top of settings
            $('html, body').animate({
                scrollTop: $('.wpca-settings-form').offset().top - 20
            }, 300);
        });

        // Form submission handling
        $('#wpca-settings-form').on('submit', function(e) {
            // Show saving message（'Saving...' 由 wp_localize_script 注入的 wpcaSettings.saveMessage 提供）
            var saveMsg = ( typeof wpcaSettings !== 'undefined' && wpcaSettings.saveMessage ) ? wpcaSettings.saveMessage : 'Saving...';
            $('#wpca-save-message').html('<span class="wpca-saving">' + saveMsg + '</span>');
        });

        // Add toggle functionality to setting sections
        $('.wpca-settings-section h3').on('click', function() {
            const section = $(this).closest('.wpca-settings-section');
            const content = section.nextUntil('.wpca-settings-section');

            section.toggleClass('collapsed');
            content.slideToggle();
        });
    });
})(jQuery);