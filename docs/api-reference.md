# WP Clean Admin API 参考（T014）

> 版本基线 1.9.17　|　扩展能力规范见 [`extension-api/spec.md`](../openspec/changes/规范改进与功能增强计划/specs/extension-api/spec.md)　|　本文仅登记**当前代码真实存在**的对外接口，不做前瞻性承诺。

## 全局函数库（`wpca_*` 前缀）

定义于 `includes/wpca-core-functions.php` 及其拆分库（`-settings-` / `-admin-` / `-path-` / `-util-` `-functions.php`）：

| 函数 | 说明 |
|------|------|
| `wpca_get_settings()` | 读取 `wpca_settings` 选项（未设置时返回默认结构） |
| `wpca_load_template()` | 安全加载模板片段（`EXTR_SKIP` 守卫） |

> 完整函数清单以源码为准；新增全局函数必须使用 `wpca_` 前缀（CONTRIBUTING §3）。

## 过滤器（Filters）

| 钩子 | 位置 | 说明 |
|------|------|------|
| `wpca_update_settings` | `wpca-core-settings-functions.php` | 设置保存入库前触发，传入经校验的完整设置数组；返回值即入库内容。可用于第三方插件追加白名单字段或强制覆盖某项配置 |

## 选项（Options）

| 键 | 结构 |
|----|------|
| `wpca_settings` | 主设置：`general` / `performance`（含 `cache_enabled`、`cache_expiration`）/ `menu` / `security` / `user_roles` 等 |
| `wpca_menu_items` | 菜单管理器菜单项配置 |
| `wpca_menu_customizer_settings` | 菜单定制配置（`enabled` / `menu_items` / `submenu_items` / `admin_bar_items` / `menu_groups`） |
| `wpca_cache_{key}` | 数据库缓存后端的缓存项（`clear( 'database' )` 按此前缀批量清理） |

## AJAX 约定

- nonce 字段：`wpca_ajax_nonce`；能力要求：`manage_options`。
- 响应结构：`{ success, error_code?, message, data }`；错误码取自 `WPCA_Errors`（1001 数据库 / 1002 权限 / 1003 输入 / 1004 文件 / 1005 AJAX / 1006 校验 / 1007 认证 / 9999 未知）。

## 核心类（`WPCleanAdmin\Modules\*` 权威体系）

| 类 | 职责 |
|----|------|
| `Modules\Core\Classes\Core` / `Module_Loader` | 插件启动与模块装配 |
| `Modules\Admin\Classes\Menu_Manager` / `Menu_Customizer` / `Permissions` / `User_Roles` / `Login` | 后台菜单、管理栏、权限、角色、登录保护 |
| `Modules\Admin\Settings\Settings` | 设置页（字段/样式/脚本） |
| `Modules\Utilities\Classes\Cache` | 三级缓存统一入口（`get/set/delete/clear`） |
| `Modules\Utilities\Classes\Helpers` | 格式化、错误响应、日志、校验（`WPCA_Errors` 错误码单一来源） |
| `Modules\Utilities\Classes\Resources` / `Diagnostics` | 资源优化 / 环境诊断 |

> 根 `includes/` 下旧同名类为已归档死代码（`archive/legacy-includes-1.9.12/`），**禁止引用**。

## 扩展 API（规范阶段）

扩展注册/生命周期接口以 [`extension-api/spec.md`](../openspec/changes/规范改进与功能增强计划/specs/extension-api/spec.md) 为准，参考实现见 [`examples/sample-extension.php`](../examples/sample-extension.php)。接口落地前以本文档标注「规范阶段」为准，不保证向后兼容。

---

*本文与插件版本同步更新；钩子新增/变更须在此登记（发布自检清单项）。*
