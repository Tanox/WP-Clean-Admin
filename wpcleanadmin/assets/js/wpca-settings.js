/**
 * WP Clean Admin — Settings Page Scripts
 *
 * E-2 抽离资产（1.9.17）：活设置页 Tab 切换 / 折叠 / 提交反馈交互。
 * 本地化文案由 class-wpca-settings-scripts.php 注入为 window.wpcaSaveMessage。
 *
 * @version 1.9.17
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const tabButtons = $('.wpca-tab-button');
        const tabContents = $('.wpca-tab-content');

        tabButtons.on('click', function() {
            const tabId = $(this).data('tab');

            tabButtons.removeClass('active');
            tabContents.removeClass('active');

            $(this).addClass('active');
            $(`#wpca-tab-${tabId}`).addClass('active');

            $('html, body').animate({
                scrollTop: $('.wpca-settings-form').offset().top - 20
            }, 300);
        });

        $('#wpca-settings-form').on('submit', function() {
            const msg = ('undefined' !== typeof window.wpcaSaveMessage) ? window.wpcaSaveMessage : 'Saving...';
            $('#wpca-save-message').html('<span class="wpca-saving">' + msg + '</span>');
        });

        $('.wpca-settings-section h3').on('click', function() {
            const section = $(this).closest('.wpca-settings-section');
            const content = section.nextUntil('.wpca-settings-section');

            section.toggleClass('collapsed');
            content.slideToggle();
        });
    });
})(jQuery);
