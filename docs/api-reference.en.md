# WP Clean Admin API Quick Reference (English)

> **Applies to**: 1.9.18  |  **Last updated**: 2026-11-25  |  **Status**: Quick reference
> The **authoritative HTTP contract** is [`API接口文档.md`](API接口文档.md) and [`openapi.yaml`](openapi.yaml); this document is a quick reference for global functions, options and core classes. The extension capability spec lives in [`extension-api/spec.md`](../openspec/changes/规范改进与功能增强计划/specs/extension-api/spec.md) — no forward-looking promises are made here.
> The Simplified Chinese original is [`api-reference.md`](api-reference.md); when the two diverge, the Chinese original wins.

## Global Function Library (`wpca_*` prefix)

Defined in `includes/wpca-core-functions.php` and its split libraries (`-settings-` / `-admin-` / `-path-` / `-util-` `-functions.php`):

| Function | Description |
|----------|-------------|
| `wpca_get_settings()` | Reads the `wpca_settings` option (returns the default structure when unset); memoized per request since 1.9.18 |
| `wpca_load_template()` | Safely loads a template fragment (guarded by `EXTR_SKIP`) |

> The full function list follows the source code; new global functions must use the `wpca_` prefix (CONTRIBUTING §3).

## Filters

| Hook | Location | Description |
|------|----------|-------------|
| `wpca_update_settings` | `wpca-core-settings-functions.php` | Fired before a settings write, receiving the fully sanitized settings array; the return value is what gets stored. Third-party plugins can use it to append whitelisted fields or force a configuration value |

## Options

| Key | Structure |
|-----|-----------|
| `wpca_settings` | Main settings: `general` / `performance` (including `cache_enabled`, `cache_expiration`) / `menu` / `security` / `user_roles` and so on |
| `wpca_menu_items` | Menu manager item configuration |
| `wpca_menu_customizer_settings` | Menu customizer configuration (`enabled` / `menu_items` / `submenu_items` / `admin_bar_items` / `menu_groups`) — the single data source since 1.9.18 |
| `wpca_cache_{key}` | Cache entries of the database backend (`clear( 'database' )` purges them by prefix in batches) |

## AJAX Conventions

- Nonce field: `wpca_ajax_nonce`; required capability: `manage_options`.
- Standard response DTO (`WPCleanAdmin\AJAX\Response`): `{ success, error_code?, message, data }`; error codes come from `WPCA_Errors` (1001 database / 1002 permission / 1003 invalid input / 1004 file operation / 1005 AJAX / 1006 validation / 1007 authentication / 9999 unknown). Since 1.9.18 all four registered handlers emit this DTO and error responses carry `error_code`; the previously unmounted `settings` handlers were removed and the recursive sanitization moved down into `Helpers::sanitize_settings()`.

## Core Classes (the `WPCleanAdmin\Modules\*` authoritative system)

| Class | Responsibility |
|-------|----------------|
| `Modules\Core\Classes\Core` / `Module_Loader` | Plugin bootstrap and module assembly |
| `Modules\Admin\Classes\Menu_Manager` / `Menu_Customizer` / `Permissions` / `User_Roles` / `Login` | Admin menu, admin bar, permissions, roles, login protection |
| `Modules\Admin\Settings\Settings` | Settings page (fields / styles / scripts) |
| `Modules\Utilities\Classes\Cache` | Unified entry point for the three cache tiers (`get/set/delete/clear`) |
| `Modules\Utilities\Classes\Helpers` | Formatting, error responses, logging, validation (`WPCA_Errors` is the single source of error codes) |
| `Modules\Utilities\Classes\Resources` / `Diagnostics` | Resource optimization / environment diagnostics |

> The old same-named classes under the root `includes/` are archived dead code (`archive/legacy-includes-1.9.12/`) and **must not be referenced**.

## Extension API (specification stage)

The extension registration and lifecycle interfaces follow [`extension-api/spec.md`](../openspec/changes/规范改进与功能增强计划/specs/extension-api/spec.md); a reference implementation is available in [`examples/sample-extension.php`](../examples/sample-extension.php). Until the interfaces land, this document labels them as "specification stage" and no backward compatibility is guaranteed.

---

*This document is updated together with the plugin version; new or changed hooks must be registered here (release self-check list item).*
