/* ============================================================
   WP Clean Admin 原型 · 前端交互（纯演示，无后端）
   - 选项卡切换
   - 菜单定制树：展开/折叠、全选、取消全选、拖拽排序、恢复默认
   - 保存按钮：演示保存反馈
   ============================================================ */
(function () {
    'use strict';

    // ---------- 选项卡切换 ----------
    var tabButtons = document.querySelectorAll('.wpca-tab-button');
    var tabContents = document.querySelectorAll('.wpca-tab-content');

    tabButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-tab');
            tabButtons.forEach(function (b) {
                var on = b === btn;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            tabContents.forEach(function (c) {
                var on = c.id === 'wpca-tab-' + tab;
                c.classList.toggle('is-active', on);
                c.hidden = !on;
            });
        });
    });

    // ---------- 菜单树展开/折叠 ----------
    var tree = document.getElementById('wpca-menu-tree');
    if (tree) {
        tree.addEventListener('click', function (e) {
            var header = e.target.closest('.wpca-menu-item-header');
            if (header && !e.target.matches('input[type="checkbox"]')) {
                header.classList.toggle('expanded');
                var sub = header.nextElementSibling;
                if (sub && sub.classList.contains('wpca-submenu-items')) {
                    sub.style.display = (sub.style.display === 'none') ? 'block' : 'none';
                }
            }
        });
        tree.querySelectorAll('.wpca-submenu-items').forEach(function (s) { s.style.display = 'none'; });
    }

    // ---------- 全选 / 取消全选 ----------
    function bindToggleAll(triggerId, checked) {
        var btn = document.getElementById(triggerId);
        if (!btn || !tree) { return; }
        btn.addEventListener('click', function () {
            tree.querySelectorAll('input[type="checkbox"]').forEach(function (c) { c.checked = checked; });
        });
    }
    bindToggleAll('wpca-select-all-menu-items', true);
    bindToggleAll('wpca-deselect-all-menu-items', false);

    // ---------- 恢复默认菜单顺序 ----------
    var orderList = document.getElementById('wpca-menu-order-list');
    var defaultOrder = orderList ? Array.from(orderList.children).map(function (el) { return el.outerHTML; }) : [];
    var resetBtn = document.getElementById('wpca-reset-menu-order');
    if (resetBtn && orderList) {
        resetBtn.addEventListener('click', function () {
            orderList.innerHTML = defaultOrder.join('');
            bindDrag(orderList);
        });
    }

    // ---------- 拖拽排序 ----------
    function bindDrag(list) {
        if (!list) { return; }
        var dragEl = null;
        list.querySelectorAll('.wpca-menu-order-item').forEach(function (item) {
            item.setAttribute('draggable', 'true');
            item.addEventListener('dragstart', function () { dragEl = item; item.classList.add('dragging'); });
            item.addEventListener('dragend', function () { item.classList.remove('dragging'); dragEl = null; });
            item.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (!dragEl || dragEl === item) { return; }
                var rect = item.getBoundingClientRect();
                var after = (e.clientY - rect.top) > rect.height / 2;
                list.insertBefore(dragEl, after ? item.nextSibling : item);
            });
        });
    }
    bindDrag(orderList);

    // ---------- 保存演示 ----------
    var form = document.getElementById('wpca-settings-form');
    var msg = document.getElementById('wpca-save-message');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            msg.textContent = '保存中…';
            msg.className = 'wpca-save-message wpca-saving';
            setTimeout(function () {
                msg.textContent = '✓ 设置已保存';
                msg.className = 'wpca-save-message wpca-saved';
                var notice = document.querySelector('.notice-info');
                if (notice) {
                    notice.className = 'notice notice-success';
                    notice.querySelector('p').textContent = '设置已保存。';
                }
            }, 700);
        });
    }
})();
