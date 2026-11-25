# WP Clean Admin API 接口文档

> **适用基线**：1.9.17　|　**更新日期**：2026-11-25　|　**状态**：现行文档
> 机器可读契约：[`openapi.yaml`](openapi.yaml)（OpenAPI 3.1）。本文只登记**当前代码真实存在**的接口；规范阶段能力见文末与 [`优化建议.md`](优化建议.md)。

## 目录

- [1. 接口范围与技术事实](#1-接口范围与技术事实)
- [2. 鉴权与调用约定](#2-鉴权与调用约定)
- [3. 已注册 AJAX 接口](#3-已注册-ajax-接口)
- [4. 未挂载的设置处理器](#4-未挂载的设置处理器)
- [5. 错误码定义](#5-错误码定义)
- [6. 全局函数 API](#6-全局函数-api)
- [7. 过滤器与选项](#7-过滤器与选项)
- [8. 登录表单参数（非 AJAX）](#8-登录表单参数非-ajax)
- [9. 契约差异与已知问题](#9-契约差异与已知问题)
- [10. 文档版本控制](#10-文档版本控制)

---

## 1. 接口范围与技术事实

| 接口类型 | 当前状态 | 依据 |
|----------|----------|------|
| REST API | **不存在**：运行时代码无 `register_rest_route` / `rest_api_init` 注册 | 全仓运行时扫描为 0 匹配 |
| AJAX | **4 个已注册**（均登录用户可用，无 `wp_ajax_nopriv_*`） | `modules/admin/classes/class-wpca-menu-customizer.php:95-100` |
| 设置 AJAX 处理器 | 3 个方法已定义但**未挂载 action** | `includes/ajax/settings-ajax.php:29/73/112` |
| 全局 `wpca_*` 函数 | 运行期加载，供插件内部与扩展代码调用 | `includes/wpca-core-*-functions.php` |
| 对外过滤器 | 1 个：`wpca_update_settings` | `includes/wpca-core-settings-functions.php:73` |

所有 AJAX 请求统一走 WordPress 后台入口 `POST /wp-admin/admin-ajax.php`，通过表单字段 `action` 分发。

## 2. 鉴权与调用约定

| 项目 | 约定 |
|------|------|
| HTTP 方法 | POST（handler 仅读取 `$_POST`） |
| 登录状态 | 必须已登录；未注册 `wp_ajax_nopriv_*` 变体 |
| Nonce | 字段 `_wpnonce`，动作串 `wpca_ajax_nonce`；菜单定制器用 `wp_verify_nonce()` 手动校验（非 `check_ajax_referer`） |
| 能力 | `current_user_can('manage_options')`，不满足返回 `wp_send_json_error('Insufficient permissions')` |
| 内容类型 | `application/x-www-form-urlencoded` |
| 标准响应 DTO | `Helpers\create_success_response()` / `create_error_response()`：`{success, message, data}` / `{success, error_code, message, data}`（**仅部分代码使用**，见 §9） |

Nonce 生成示例（后台页面内）：`wp_create_nonce( 'wpca_ajax_nonce' )`；渲染处见 `modules/admin/classes/class-wpca-dashboard-widget-tasks.php:108`。

## 3. 已注册 AJAX 接口

四个接口共用私有校验 `MenuCustomizerAjaxTasks::verify_ajax_request()`（`modules/admin/classes/class-wpca-menu-customizer-ajax-tasks.php:31-49`）：nonce 失败 → `wp_send_json_error('Invalid nonce')`；权限不足 → `wp_send_json_error('Insufficient permissions')`。

### 3.1 保存菜单定制设置

| 项目 | 内容 |
|------|------|
| 路径 / 方法 | `POST /wp-admin/admin-ajax.php` |
| `action` | `wpca_save_menu_customizer` |
| 参数 | `_wpnonce`（string，必填）；`settings`（array，必填，递归消毒；非数组置空） |
| 成功响应 | `{"success":true,"data":{"message":"Menu customizer settings saved successfully"}}` |
| 失败响应 | `{"success":false,"data":"Failed to save menu customizer settings"}` |
| 代码 | 注册 `class-wpca-menu-customizer.php:95`；实现 `class-wpca-menu-customizer-ajax-tasks.php:54-74` |

调用示例（后台页面 JS）：

```javascript
jQuery.post( ajaxurl, {
  action: 'wpca_save_menu_customizer',
  _wpnonce: wpcaSettings.nonce,
  settings: { enabled: true, menu_items: { 'edit.php': { hidden: false } } }
}, function ( res ) { console.log( res.success, res.data ); } );
```

### 3.2 重置菜单定制设置

| 项目 | 内容 |
|------|------|
| `action` | `wpca_reset_menu_customizer` |
| 参数 | `_wpnonce` |
| 成功响应 | `{"success":true,"data":{"message":"Menu customizer settings reset to default"}}` |
| 失败响应 | `{"success":false,"data":"Failed to reset menu customizer settings"}` |
| 代码 | `class-wpca-menu-customizer.php:96`；`class-wpca-menu-customizer-ajax-tasks.php:80-93` |

### 3.3 导出菜单定制设置

| 项目 | 内容 |
|------|------|
| `action` | `wpca_export_menu_customizer` |
| 参数 | `_wpnonce` |
| 成功响应 | `{"success":true,"data":{"menu_items":{},"submenu_items":{},"admin_bar_items":{},"menu_order":[],"menu_groups":{}}}` |
| 实现 | `class-wpca-menu-customizer-ajax-tasks.php:100-106`；数据结构见 `class-wpca-menu-customizer-render.php:142-154` |
| 已知问题 | 导出读取 `wpca_settings`，与保存键不一致（OPT-001） |

### 3.4 导入菜单定制设置

| 项目 | 内容 |
|------|------|
| `action` | `wpca_import_menu_customizer` |
| 参数 | `_wpnonce`；`import_data`（JSON 字符串或数组，解码后写入 `wpca_menu_customizer_settings`） |
| 成功响应 | `{"success":true,"data":{"message":"Menu customizer settings imported successfully"}}` |
| 失败响应 | `{"success":false,"data":"Failed to import menu customizer settings"}` |
| 实现 | `class-wpca-menu-customizer-ajax-tasks.php:113-135` |

## 4. 未挂载的设置处理器

`WPCleanAdmin\AJAX\Settings`（`includes/ajax/settings-ajax.php`）定义以下方法，但**仓库中无对应 `wp_ajax_*` 注册**，因此当前不可通过 HTTP 调用；它们仍被菜单定制器复用 `sanitize_settings()`。

| 方法 | 预期 action（未注册） | 参数 | 行为 |
|------|----------------------|------|------|
| `Settings::save_settings()` | `wpca_save_settings`（示意，勿当真实现调用） | `_wpnonce`、`settings` | 消毒后 `update_option('wpca_settings')`，`:29-63` |
| `Settings::get_settings()` | `wpca_get_settings`（示意） | `_wpnonce` | 返回 `{"settings":{…}}`，`:73-102` |
| `Settings::reset_settings()` | `wpca_reset_settings`（示意） | `_wpnonce` | `delete_option('wpca_settings')`，`:112-140` |

> 三者均校验 nonce（`wpca_ajax_nonce`）与 `manage_options`；响应使用 `wp_send_json_success/error`。是否注册或删除这些 handler 见 OPT-002。

## 5. 错误码定义

`WPCA_Errors`（`modules/utilities/classes/class-wpca-helpers.php:26-44`）为错误码单一来源；消息映射见 `class-wpca-helpers-error-tasks.php:96-109`。

| 常量 | 值 | 默认消息 |
|------|----|----------|
| `ERROR_NONE` | 0 | No error occurred. |
| `ERROR_DATABASE` | 1001 | A database error occurred. Please check the logs for more details. |
| `ERROR_PERMISSION` | 1002 | You do not have permission to perform this action. |
| `ERROR_INVALID_INPUT` | 1003 | Invalid input provided. Please check your entries and try again. |
| `ERROR_FILE_OPERATION` | 1004 | A file operation failed. Please check file permissions and try again. |
| `ERROR_AJAX` | 1005 | An AJAX request failed. Please try again. |
| `ERROR_VALIDATION` | 1006 | Settings validation failed. Please check your entries. |
| `ERROR_AUTH` | 1007 | Authentication failed. Please log in again. |
| `ERROR_UNKNOWN` | 9999 | An unknown error occurred. Please try again.（兜底） |

> 注意：当前 4 个已注册 AJAX handler 的错误响应为字符串载荷，**未携带 `error_code`**；错误码主要由 Helpers DTO 使用。统一契约见 OPT-002。

## 6. 全局函数 API

| 函数 | 位置 | 返回 | 用途 |
|------|------|------|------|
| `wpca_get_settings($key='', $default=false)` | `includes/wpca-core-settings-functions.php:24` | mixed | 读取 `wpca_settings` 或单键 |
| `wpca_update_settings($settings)` | 同上 `:40` | bool | 递归消毒 → `wpca_update_settings` 过滤器 → 写 option |
| `wpca_sanitize_nested_array($array)` | 同上 `:85` | array | 嵌套消毒（键 `sanitize_key`、字符串 `sanitize_text_field`） |
| `wpca_get_database_settings()` / `wpca_get_performance_settings()` | 同上 `:112` / `:129` | mixed | 读取对应分组选项 |
| `wpca_get_plugin_url($path='')` / `wpca_get_plugin_dir($path='')` / `wpca_get_asset_url($path='')` | `includes/wpca-core-path-functions.php:23/35/47` | string | 路径与资源 URL 拼接 |
| `wpca_load_template($name, $args=[])` | 同上 `:67` | void | 安全加载模板（`extract(..., EXTR_SKIP)`） |
| `wpca_current_user_can()` / `wpca_admin_notice()` / `wpca_get_settings_url()` | `includes/wpca-core-admin-functions.php:23/33/58` | bool/void/string | 后台能力、提示、设置页 URL |
| `wpca_get_version()` / `wpca_is_debug()` / `wpca_log()` / `wpca_is_wp_version_gte()` | `includes/wpca-core-util-functions.php:22/31/41/61` | string/bool/void/bool | 版本、调试与日志工具 |

完整函数清单以源码为准；新增函数必须使用 `wpca_` 前缀（`CONTRIBUTING.md` §3）。

## 7. 过滤器与选项

| 类型 | 名称 | 说明 |
|------|------|------|
| Filter | `wpca_update_settings` | 入库前接收完整消毒后的设置数组；返回值即最终写入内容（`wpca-core-settings-functions.php:73`） |
| Option | `wpca_settings` | 主设置：`general`/`performance`/`menu`/`security`/`diagnostics`/`user_roles` |
| Option | `wpca_menu_items` / `wpca_menu_customizer_settings` | 菜单管理与定制配置 |
| Option | `wpca_database_settings` / `wpca_performance_settings` | 分组设置读取来源 |
| Option | `wpca_cache_{key}` | 数据库缓存项；`Cache::clear('database')` 按前缀批量清理 |
| Option | `wpca_disable_rest_api` | REST 限制相关；卸载时删除 |

## 8. 登录表单参数（非 AJAX）

| 参数 | 位置 | 校验 / 行为 | 代码 |
|------|------|-------------|------|
| `wpca_captcha` | POST（`wp-login.php`） | `Login_Captcha::verify_captcha()`；GD 缺失时拒绝未带验证码请求 | `modules/admin/classes/class-wpca-login-captcha.php:160-173` |
| `wpca_captcha=1` | GET（登录页） | 输出验证码图片 | `class-wpca-login.php:101` |
| `wpca_two_factor` / `wpca_user_id` | REQUEST | 2FA 表单渲染判定 | `class-wpca-login-two-factor-auth-tasks.php:30/39` |
| `wpca_two_factor_code` / `wpca_user_id` / `wpca_nonce` | POST | TOTP 校验 + nonce；失败返回 `WP_Error` | 同文件 `:55-87` |

这些入口挂在 `authenticate` / `wp_authenticate_user` 钩子上，返回 `WP_Error`，**不是 JSON 接口**。

## 9. 契约差异与已知问题

| 问题 | 影响 | 跟踪 |
|------|------|------|
| 4 个 AJAX handler 的字符串错误载荷与 Helpers DTO 不一致 | 客户端无法按 `error_code` 分支处理 | OPT-002（P1） |
| 设置 AJAX 3 个方法未注册 action | 文档承诺的接口不可调用 | OPT-002（P1） |
| 菜单定制器保存/读取 option 键不一致、AJAX 默认未注册 | 接口可调用性/闭环受损 | OPT-001（P0） |
| 无 REST 端点与 OpenAPI 历史缺失 | 外部系统无法机器可读集成 | OPT-012（P2）；本文 `openapi.yaml` 已补 AJAX 契约 |

> 调用方请以 `openapi.yaml` 与本节「已注册」清单为准；`docs/api-reference.md` 中“统一响应结构”描述为**目标契约**，当前仅有部分实现。

## 10. 文档版本控制

| 文档版本 | 适用插件版本 | 日期 | 维护人 | 变更摘要 |
|----------|--------------|------|--------|----------|
| 1.0 | 1.9.17 | 2026-11-25 | Tanox | 首版：4 个 AJAX 接口、错误码、全局函数、过滤器/选项与非 AJAX 登录参数；附 OpenAPI 3.1 契约 |
