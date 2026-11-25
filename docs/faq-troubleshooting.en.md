# WP Clean Admin FAQ and Troubleshooting Guide (English)

> **Applies to**: 1.9.18  |  **Last updated**: 2026-11-25  |  **Status**: User documentation
> Read first: [`installation-guide.en.md`](installation-guide.en.md), [`user-guide.en.md`](user-guide.en.md). This document lists **known behaviours of the current version**; defect tracking lives in [`优化建议.md`](优化建议.md) and [`tasks.md`](tasks.md).
> The Simplified Chinese original is [`FAQ与故障排除.md`](FAQ与故障排除.md); when the two diverge, the Chinese original wins.

## Table of Contents

- [1. Frequently Asked Questions](#1-frequently-asked-questions)
- [2. Troubleshooting Matrix](#2-troubleshooting-matrix)
- [3. Diagnostics and Logging](#3-diagnostics-and-logging)
- [4. Emergency Recovery](#4-emergency-recovery)
- [5. Pre-Submission Checklist](#5-pre-submission-checklist)
- [6. Document Version Control](#6-document-version-control)

---

## 1. Frequently Asked Questions

**Q1: Where do I configure the plugin after activation?**
Admin → WP Clean Admin → Settings, with five tabs: General / Cleanup / Performance / Security / Diagnostics. The plugin list also shows a Settings shortcut link (`wp-clean-admin.php:218-233`).

**Q2: Why do I see no change after enabling menu customization?**
This defect was fixed in 1.9.18: the menu customizer now uses `wpca_menu_customizer_settings` as its **single data source** (`Menu_Customizer_Options` owns the reads and writes; Tree/Render receive the same instance through constructor injection), and all four AJAX actions are **registered unconditionally** (they are no longer guarded by the disabled-by-default `settings.menu_customizer` switch). Data from the older `wpca_settings.menu_customizer` key is migrated idempotently on the first read. If nothing still changes, confirm that the `admin-ajax.php` request in your browser's Network panel returns `success:true`, then flush your object cache and retry.

**Q3: Why do saved settings not take effect immediately?**
Settings are written to `wpca_settings` and read on the next request through `wpca_get_settings()` (request-level memoization since 1.9.18; write paths invalidate the cache automatically). If your site uses an object-cache plugin, flush its cache. Field echo was also fixed in 1.9.18: rendering now reads the nested `wpca_settings[group][id]` first and falls back to the standalone option, and the form `name` attribute matches (OPT-003).

**Q4: The login captcha image does not show.**
Check whether the PHP GD extension is installed. Without it, image generation is skipped safely and login validation rejects requests that carry no captcha. Install GD, restart the web server and retry (see `modules/admin/classes/class-wpca-login-captcha.php`).

**Q5: I enabled 2FA and can no longer log in.**
Use the 6-digit rotating code from your authenticator app; the form nonce field is `wpca_nonce`. If the binding is lost, clear that user's 2FA user meta through the database (back up first) or ask an administrator to help restore access. **Recovery codes are not provided in the current release**; file a request in [`tasks.md`](tasks.md) if you need that capability.

**Q6: The file cache misbehaves or serves stale content.**
Since 1.9.18 the file cache directory lives at `wp-content/uploads/wpca-cache/` (with `index.php` and `.htaccess` protection), writes use a temporary file plus an atomic `rename()`, and cleanup is handled by the hourly cron job. Delete the `.cache` files in that directory to flush it; corrupted or legacy serialized files are discarded automatically on read. If problems persist, switch the `performance` cache backend to database or memory. The old `wpcleanadmin/cache/` directory can be removed manually.

**Q7: Why can anonymous REST requests still reach the API?**
Since 1.9.18 the REST restriction is registered during `rest_api_init` via the `rest_authentication_errors` filter; anonymous requests receive HTTP 403 (error code `rest_not_logged_in`) while logged-in users are unaffected (OPT-005). Note that this switch only covers the REST API — **do not treat it as your only security boundary**; add server-level restrictions if you need to close REST entirely.

**Q8: Is any data left behind after uninstalling?**
`uninstall.php` deletes `wpca_%` options, transients and usermeta, and since 1.9.18 it also clears WP-Cron events and the file cache directory (`uploads/wpca-cache`). **Backup files and log directories still need manual cleanup** (`backups/` and the log directory).

**Q9: Does it work on multisite?**
`Helpers::is_network_activated()` provides network-activation detection; settings are stored per site in each site's own `wpca_settings`. Verify roles, menus and login protection site by site — do not assume network-wide unified configuration exists.

**Q10: How do I confirm that an AJAX request is properly authenticated?**
Open the browser developer tools' Network panel and inspect the `admin-ajax.php` request: the form must carry `_wpnonce` (action `wpca_ajax_nonce`). Since 1.9.18 all responses follow the `{success, error_code?, message, data}` DTO: a missing nonce returns `{"success":false,"error_code":1007,"message":"Invalid nonce","data":{}}`, and insufficient permissions return `error_code:1002` (`Insufficient permissions`).

**Q11: The dashboard is slow.**
Since 1.9.18 the statistics are cached in a 15-minute transient (`wpca_dashboard_stats`); on a cache hit the plugin no longer runs the `information_schema` summation or the leading-wildcard `LIKE '%transient%'` scan, and orphan-meta counting was rewritten as an index-friendly `NOT EXISTS` query (OPT-010). The first visit still computes the values once; if the dashboard is still slow, temporarily disable the corresponding widget.

**Q12: How do I know whether a feature is enabled?**
Inspect the `wpca_settings` option (the `wp_options` row, or `wpca_get_settings()`); activation defaults are listed in [`installation-guide.en.md`](installation-guide.en.md) §4.2.

**Q13: What happens to menu and cache data after upgrading from an older version?**
Legacy menu customization data is migrated **idempotently** from `wpca_settings.menu_customizer` to `wpca_menu_customizer_settings` on first read; the old data is kept, not deleted. Activation defaults never overwrite existing settings. The file cache moved to `uploads/wpca-cache/`; the old `wpcleanadmin/cache/` directory is no longer used and can be removed manually after verification. If customizations do not appear after upgrading, flush the object cache and retry (see Q2).

## 2. Troubleshooting Matrix

| Symptom | Likely cause | How to check | Remedy | Reference |
|---------|--------------|--------------|--------|-----------|
| Settings page blank / JS errors | Assets not loaded (wrong path after the E-2 extraction) | In Network, check that `assets/js/wpca-settings.js` and `assets/css/wpca-settings.css` return 200 | Make sure the plugin directory is named `wp-clean-admin` and re-upload the complete package | Installation §2 |
| Save AJAX returns `Invalid nonce` | Expired nonce, or `_wpnonce` not sent | Inspect the request payload in Network | Reload the settings page to obtain a fresh nonce | API docs §2 |
| Save AJAX returns `Insufficient permissions` | The current user lacks `manage_options` | Check the user role | Use an administrator account | User guide §1 |
| Menu customization saves but has no effect | Object cache not flushed / migrated data not written | Compare `wpca_menu_customizer_settings` with `wpca_settings`; inspect the Network response | 1.9.18 unified the data source and registers the actions unconditionally; flush the object cache and retry | OPT-001 |
| Captcha does not show and login fails | GD missing | `php -m | grep gd` (needs a PHP environment) or the hosting panel | Install GD; in an emergency, clear the captcha switch in the database | FAQ Q4 |
| File cache keeps failing | Directory not writable / legacy serialized files | Check the permissions of `uploads/wpca-cache/` and whether file contents are JSON | Grant write permission or switch the cache backend; the old `wpcleanadmin/cache/` can be deleted | FAQ Q6 |
| REST restriction has no effect | Switch not enabled / the server layer allows access anyway | Request `/wp-json` directly and observe the status code | Since 1.9.18 anonymous requests receive 403; server-level restrictions are still recommended | OPT-005 |
| Leftover `wpca_*` data after uninstall | The uninstall routine does not cover custom keys | `SELECT option_name FROM wp_options WHERE option_name LIKE 'wpca_%'` | Delete manually (back up first) | FAQ Q8 |
| Settings fields show stale values | Object cache not flushed | Save, reload and compare against the database values | Since 1.9.18 the nested `wpca_settings[group][id]` is read; flush the cache and retry | FAQ Q3 |
| The same field appears on all four tabs | Older versions called `do_settings_sections` on every tab | Check for duplicated HTML ids | Since 1.9.18 each tab renders through its own page slug exactly once | OPT-009 |

## 3. Diagnostics and Logging

1. **WordPress debugging**: set `WP_DEBUG=true` in `wp-config.php` (do not leave it on in production). Plugin errors are written to the PHP error log by `Error_Handler` and `wpca_log()`.
2. **Plugin diagnostics page**: the General/Diagnostics tab shows system information and warnings; `diagnostics.show_warnings` and `severity_filter` control what is displayed.
3. **Browser developer tools**: check console errors, the `admin-ajax.php` requests and responses in Network, and the settings page semantic IDs (`wpca-settings-tabs` and so on).
4. **Database verification**: query `wpca_%` in `wp_options`; the cache entry prefix is `wpca_cache_%`; user meta uses the `wpca_` prefix.
5. **Autoload smoke test**: `tests/PluginAutoloadSmokeTest.php` verifies that the classes load (requires a PHP environment; executed by CI).
6. **Version verification**: `Version:` and `WPCA_VERSION` in `wpcleanadmin/wp-clean-admin.php` must match (currently 1.9.18); version history lives in `CHANGELOG.md`.

## 4. Emergency Recovery

| Scenario | Steps |
|----------|-------|
| The plugin makes the admin unusable | Rename the `wp-content/plugins/wp-clean-admin` directory (for example to `wp-clean-admin.disabled`); WordPress deactivates it automatically. Then investigate |
| Unexpected database writes | Restore the `wpca_%` rows in `wp_options` from a backup, or run `DELETE FROM wp_options WHERE option_name LIKE 'wpca_%'` (**back up first**) |
| Locked out by 2FA | Clear that user's 2FA user meta through the database (back up first), or restore a site backup |
| All AJAX calls fail | Check that `admin-ajax.php` is reachable, that the nonce action string is `wpca_ajax_nonce`, and that you are using an administrator account |
| Something broke after an upgrade | Roll back the plugin directory and the database per [`installation-guide.en.md`](installation-guide.en.md) §7 |

## 5. Pre-Submission Checklist

- [ ] The plugin version matches `WPCA_VERSION` (1.9.18) and the package is complete (contains `includes/modules/` and `uninstall.php`)
- [ ] You have worked through this FAQ and the troubleshooting matrix
- [ ] You recorded the WordPress/PHP/MySQL versions, the browser, the reproduction steps and the Network response body
- [ ] You can clearly separate "known limitation" (this document / OPT-*) from a new problem
- [ ] New problems are filed in the format of [`tasks.md`](tasks.md) (priority, owner, planned date)

## 6. Document Version Control

| Document version | Applies to plugin version | Date | Maintainer | Summary |
|------------------|---------------------------|------|------------|---------|
| 1.1 | 1.9.18 | 2026-11-25 | Tanox | Added the post-upgrade data migration entry (Q13); corrected the 2FA recovery-code statement; aligned with `FAQ与故障排除.md` v1.2 |
| 1.0 | 1.9.18 | 2026-11-25 | Tanox | English mirror of `FAQ与故障排除.md`: 12 FAQ entries, troubleshooting matrix, diagnostics, emergency recovery and the pre-submission checklist |
