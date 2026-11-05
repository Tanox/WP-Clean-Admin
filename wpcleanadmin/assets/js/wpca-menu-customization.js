(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Make menu order list sortable
        $('#wpca-menu-order-list').sortable({
            handle: '.wpca-menu-order-handle',
            update: function(event, ui) {
                // Update hidden input values
                $(this).find('.wpca-menu-order-item').each(function(index) {
                    $(this).find('input[type="hidden"]').val($(this).data('menu-slug'));
                });
            }
        });
        
        // Toggle submenu items
        $('.wpca-menu-item-header').on('click', function(e) {
            // Only toggle if clicked on header or toggle icon, not checkbox
            if (!$(e.target).is('input[type="checkbox"]')) {
                var submenu = $(this).next('.wpca-submenu-items');
                if (submenu.length > 0) {
                    submenu.slideToggle();
                    $(this).toggleClass('expanded');
                }
            }
        });
        
        // Select all menu items
        $('#wpca-select-all-menu-items').on('click', function() {
            $('.wpca-menu-tree input[type="checkbox"]').prop('checked', true);
        });
        
        // Deselect all menu items
        $('#wpca-deselect-all-menu-items').on('click', function() {
            $('.wpca-menu-tree input[type="checkbox"]').prop('checked', false);
        });
        
        // Reset menu order to default
        $('#wpca-reset-menu-order').on('click', function() {
            var menuOrderList = $('#wpca-menu-order-list');
            var originalOrder = [];
            
            // Get original order from hidden inputs
            menuOrderList.find('.wpca-menu-order-item').each(function() {
                originalOrder.push($(this));
            });
            
            // Append items in original order
            menuOrderList.empty();
            $.each(originalOrder, function(index, item) {
                menuOrderList.append(item);
            });
            
            // Update hidden input values
            menuOrderList.find('.wpca-menu-order-item').each(function(index) {
                $(this).find('input[type="hidden"]').val($(this).data('menu-slug'));
            });
        });
        
        // Initially collapse all submenu items
        $('.wpca-submenu-items').hide();
    });
})(jQuery);