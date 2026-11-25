jQuery(document).ready(function($) {
    /**
     * 生成带样式的状态文本节点（避免使用 .html() 注入服务端文本）
     */
    var wpcaStatusNode = function(className, text) {
        return $('<span>').addClass(className).text(text || '');
    };

    /**
     * 在容器内展示内联消息，替代 alert()
     */
    var wpcaShowInlineMessage = function($container, className, text) {
        var $message = $container.find('.wpca-inline-message');
        if (!$message.length) {
            $message = $('<div>').addClass('wpca-inline-message').attr('role', 'status').attr('aria-live', 'polite');
            $container.append($message);
        }
        $message.removeClass('wpca-inline-error wpca-inline-success wpca-inline-info')
            .addClass('wpca-inline-' + className)
            .text(text);
        $message.show();
    };

    $(document).on('click', '.wpca-run-diagnostics', function(e) {
        e.preventDefault();

        var $widget = $(this).closest('.wpca-diagnostics-widget');
        var $placeholder = $widget.find('.wpca-diagnostics-placeholder');
        var $loading = $widget.find('.wpca-diagnostics-loading');
        var $results = $widget.find('.wpca-diagnostics-results');

        $placeholder.hide();
        $loading.show();
        $results.hide();

        $.ajax({
            url: wpca_dashboard_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'wpca_run_diagnostics',
                _wpnonce: wpca_dashboard_vars.nonce
            },
            dataType: 'json',
            success: function(response) {
                $loading.hide();
                $results.show();

                if (!response || !response.success || !response.data) {
                    wpcaShowInlineMessage($widget, 'error', (response && response.message) ? response.message : 'Failed to run diagnostics');
                    return;
                }

                var data = response.data;
                var summary = data.summary || {};
                var checks = data.data || [];
                var statusText = '';
                var statusClass = '';

                if (data.status === 'error') {
                    statusText = 'Error';
                    statusClass = 'wpca-status-error';
                } else if (data.status === 'warning') {
                    statusText = 'Warning';
                    statusClass = 'wpca-status-warning';
                } else {
                    statusText = 'All Good';
                    statusClass = 'wpca-status-success';
                }

                $widget.find('.wpca-diagnostics-status').empty().append(
                    $('<strong>').addClass(statusClass).text(statusText)
                );
                $widget.find('.wpca-diagnostics-count').text(
                    (summary.passed || 0) + ' passed, ' +
                    (summary.warning || 0) + ' warnings, ' +
                    (summary.error || 0) + ' errors'
                );

                var $list = $('<ul>');
                $.each(checks, function(index, check) {
                    var statusIcon = '';
                    var itemClass = '';

                    switch (check.status) {
                        case 'pass':
                            statusIcon = '✓';
                            itemClass = 'wpca-check-pass';
                            break;
                        case 'warning':
                            statusIcon = '⚠';
                            itemClass = 'wpca-check-warning';
                            break;
                        case 'error':
                            statusIcon = '✗';
                            itemClass = 'wpca-check-error';
                            break;
                    }

                    var $item = $('<li>').addClass(itemClass);
                    $item.append($('<span>').addClass('wpca-status-icon').text(statusIcon));
                    $item.append($('<span>').addClass('wpca-check-name').text(check.name || ''));
                    $item.append($('<span>').addClass('wpca-check-message').text(check.message || ''));
                    if (check.action) {
                        $item.append($('<span>').addClass('wpca-check-action').text(check.action));
                    }
                    $list.append($item);
                });

                $widget.find('.wpca-diagnostics-list').empty().append($list);
            },
            error: function() {
                $loading.hide();
                $placeholder.text('Failed to run diagnostics').show();
            }
        });
    });

    $(document).on('click', '.wpca-quick-action', function(e) {
        e.preventDefault();

        var action = $(this).data('action');
        var $button = $(this);
        var originalText = $button.val();
        var $container = $button.closest('.wpca-diagnostics-widget');

        $button.val('Processing...').prop('disabled', true);
        wpcaShowInlineMessage($container, 'info', 'Processing: ' + action + ' ...');

        $.ajax({
            url: wpca_dashboard_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'wpca_run_quick_action',
                _wpnonce: wpca_dashboard_vars.nonce,
                action_name: action
            },
            dataType: 'json',
            success: function(response) {
                $button.val(originalText).prop('disabled', false);

                if (response && response.success && response.data) {
                    wpcaShowInlineMessage($container, 'success', response.data.message || 'Done');
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 1200);
                } else {
                    wpcaShowInlineMessage($container, 'error', (response && response.message) ? response.message : 'Action failed');
                }
            },
            error: function() {
                $button.val(originalText).prop('disabled', false);
                wpcaShowInlineMessage($container, 'error', 'Action failed');
            }
        });
    });
});
