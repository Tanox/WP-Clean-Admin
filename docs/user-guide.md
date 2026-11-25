# WP Clean Admin 用户操作指南

> **适用基线**：1.9.17　|　**更新日期**：2026-11-25　|　**状态**：用户文档
> 本文面向站点管理员与编辑用户。安装部署见 [`安装部署指南.md`](安装部署指南.md)；常见问题见 [`FAQ与故障排除.md`](FAQ与故障排除.md)；功能背景见 [`功能模块说明.md`](功能模块说明.md)。

## 目录

- [1. 角色与权限说明](#1-角色与权限说明)
- [2. 快速开始](#2-快速开始)
- [3. 操作流程图](#3-操作流程图)
- [4. 设置页详细步骤](#4-设置页详细步骤)
- [5. 菜单定制器操作](#5-菜单定制器操作)
- [6. 数据库与安全操作](#6-数据库与安全操作)
- [7. 保存、重置与导入导出](#7-保存重置与导入导出)
- [8. 文档版本控制](#8-文档版本控制)

---

## 1. 角色与权限说明

| 角色 | 可访问范围 | 关键能力 | 依据 |
|------|------------|----------|------|
| 管理员（Administrator） | 设置页、菜单定制、数据库、权限/角色、诊断 | `manage_options`；所有写操作要求此能力 | `modules/admin/classes/class-wpca-menu-customizer-ajax-tasks.php:41`、`includes/ajax/settings-ajax.php:39` |
| 编辑（Editor） | 仅其默认 WordPress 能力；插件不额外授权 | 受 `Permissions::filter_user_capabilities()` 影响 | `modules/admin/classes/class-wpca-permissions.php:87` |
| 作者 / 投稿者 / 订阅者 | 默认 WordPress 能力 | 不提供插件设置入口 | `UserRolesDataCapabilitiesTasks::get_default_role_capabilities()` |
| 自定义角色 | 由管理员在角色管理中创建 | 能力可增删改、可重置为内置默认值 | `UserRolesCapabilitiesTasks:69/116` |

> 权限设置项 `security.restrict_admin_access` 可限制指定后台页面访问；启用前请以测试账号验证，避免把自己锁在后台之外（见 `权限与角色管理` 章节）。

## 2. 快速开始

1. 安装并启用插件（见 [`安装部署指南.md`](安装部署指南.md) §3）。
2. 进入后台 **WP Clean Admin → 设置**；页面有 General / Cleanup / Performance / Security / Diagnostics 五个选项卡。
3. 保持默认开关，逐项启用功能并在前台/后台验证效果。
4. 需要 2FA 时：先用测试账号完成 TOTP 绑定，再对其他账号启用强制策略。
5. 亚洲语言环境（如中文）建议使用 `wp-clean-admin-zh_CN.mo` 翻译文件（已随包提供）。

## 3. 操作流程图

### 3.1 首次配置流程

```mermaid
flowchart TD
    A[启用插件] --> B[进入 WP Clean Admin → 设置]
    B --> C{选择选项卡}
    C -->|General| D[管理栏 / 仪表盘 / Logo 开关]
    C -->|Cleanup| E[菜单与小部件清理]
    C -->|Performance| F[emoji/心跳/缓存/数据库优化]
    C -->|Security| G[安全头 / REST / 登录保护 / 2FA]
    C -->|Diagnostics| H[查看系统信息与告警]
    D & E & F & G --> I[保存设置]
    I --> J[前台/后台验证]
    J -->|不符合预期| C
    J -->|符合预期| K[完成]
```

### 3.2 菜单定制流程（注意已知问题）

```mermaid
sequenceDiagram
    participant U as 管理员
    participant P as 设置页/定制器
    participant A as admin-ajax.php
    participant O as wpca_menu_customizer_settings
    U->>P: 打开菜单定制器
    P->>A: action=wpca_save_menu_customizer + _wpnonce + settings
    A->>A: nonce + manage_options 校验
    A->>O: 消毒后保存
    O-->>U: success=true
    Note over U,O: 当前版本渲染读取 wpca_settings 而非该键，
    保存结果可能不生效（OPT-001，P0）；修复前请谨慎依赖此功能
```

### 3.3 管理员启用 TOTP 双因素流程

```mermaid
sequenceDiagram
    participant U as 用户
    participant L as 登录页
    participant T as Login_TwoFactor
    U->>L: 输入账号密码
    L->>T: wp_authenticate_user 钩子
    T->>U: 渲染 2FA 表单（wpca_nonce）
    U->>T: 提交 TOTP 验证码
    T->>T: Login_TOTP::verify()
    T-->>L: 成功 → 登录；失败 → WP_Error
```

## 4. 设置页详细步骤

| 选项卡 | 操作步骤 | 关键配置项 | 生效范围 |
|--------|----------|------------|----------|
| General（常规） | 勾选/取消管理栏、仪表盘、WP Logo 开关 → 保存 | `general.clean_admin_bar` / `clean_dashboard` / `remove_wp_logo` | 后台界面 |
| Cleanup（清理） | 打开菜单简化与小部件移除 → 选择具体小部件/菜单项（按角色） → 保存 | `menu.remove_dashboard_widgets` / `simplify_admin_menu` / `dashboard_widgets` / `role_based_restrictions` | 后台菜单与仪表盘 |
| Performance（性能） | 逐项启用 emoji/心跳/数据库优化 → 设置缓存 TTL → 保存 | `performance.disable_emojis` / `clean_transients` / `optimize_database` / `cache_enabled` / `cache_expiration`（秒，≤0 回退 3600） | 前台请求与缓存 |
| Security（安全） | 启用安全头、REST 限制、登录保护 → 保存；2FA 需先完成用户绑定 | `security.hide_wp_version` / `disable_xmlrpc` / `restrict_rest_api` / `restrict_admin_access` | 登录与 HTTP 层 |
| Diagnostics（诊断） | 查看系统信息、运行诊断、按严重级别过滤告警 | `diagnostics.enable_diagnostics` / `auto_run_diagnostics` / `show_warnings` / `severity_filter` | 诊断面板 |

- 页面容器 ID：`wpca-settings-tabs`、`wpca-tabs-nav`、`wpca-tabs-content`；按钮 ID：`wpca-tab-general` / `wpca-tab-cleanup` / `wpca-tab-performance` / `wpca-tab-security` / `wpca-tab-diagnostics`；保存按钮 `wpca-save-button`，提示区 `wpca-save-message`。可用于浏览器自动化与问题定位。
- 提示：设置保存后由下一次请求通过 `wpca_get_settings()` 读取；无需手动清缓存，但如果使用对象缓存插件，请刷新其缓存以确保选项更新可见。

## 5. 菜单定制器操作

1. 进入菜单定制器，查看菜单树与管理栏节点。
2. 选择菜单项 → 设置隐藏、标题、图标或排序；可按分组组织。
3. 点击保存（AJAX action `wpca_save_menu_customizer`），提示区显示成功/失败。
4. 导出：点击导出获得 JSON；导入：粘贴 JSON 后提交。
5. **限制**：当前版本保存写入 `wpca_menu_customizer_settings`，渲染读取 `wpca_settings`，故“保存后不生效/导出导入不闭环”；且 4 个 AJAX action 受未默认启用的 `menu_customizer` 设置守卫，默认可能完全不可调用。详见 [`API接口文档.md`](API接口文档.md) §9 与 OPT-001（P0）。修复前建议仅用于试验，不依赖其作为唯一菜单管理方式。

## 6. 数据库与安全操作

### 6.1 数据库备份/恢复/删除

- 入口：数据库管理面板；操作包括备份、恢复、删除备份文件。
- 删除操作已加防护：仅允许文件名、`.sql` 扩展名白名单、`realpath` 目录边界校验（1.9.2 修复）。
- 卸载插件**不会**自动删除备份文件，请自行清理 `wpcleanadmin/backups/`（如存在）。

### 6.2 登录保护

| 功能 | 用户操作 | 注意 |
|------|----------|------|
| 图形验证码 | 登录页输入图片字符 | 需 PHP GD；缺失时图片不可生成，登录会被拒绝，需联系管理员 |
| 失败锁定 | 连续失败达到阈值后暂时锁定 | 阈值由 `Login_Attempts` 内部常量控制 |
| TOTP 2FA | 绑定验证器 App → 登录时输入 6 位动态码 | 请妥善保存恢复方式；表单 nonce 为 `wpca_nonce` |
| 登录页定制 | 设置 LOGO/背景/标题 | 由 `Login_Style*` 组件注入 |

### 6.3 REST 限制

`security.restrict_rest_api` 当前通过 `rest_authentication_errors` 过滤未登录请求；该过滤挂在脚本入队钩子上，纯 `/wp-json` 请求可能不触发（OPT-005，P1）。不要将此开关视为严格的 REST 禁用边界。

## 7. 保存、重置与导入导出

| 操作 | 结果 | 风险提示 |
|------|------|----------|
| 保存设置 | 写 `wpca_settings`；经递归消毒与 `wpca_update_settings` 过滤器 | 无 nonce/权限时不执行 |
| 重置设置 | 删除对应选项并回落默认 | 会丢失已有配置，请先导出备份 |
| 重置菜单 | 清空 `wpca_menu_items` / `wpca_menu_customizer_settings` | 不可撤销 |
| 导入菜单配置 | JSON 写入 `wpca_menu_customizer_settings` | 当前读键不一致（OPT-001），可能看不到效果 |

## 8. 文档版本控制

| 文档版本 | 适用插件版本 | 日期 | 维护人 | 变更摘要 |
|----------|--------------|------|--------|----------|
| 1.0 | 1.9.17 | 2026-11-25 | Tanox | 首版：角色权限表、操作流程图、五 Tab 详细步骤、数据库/安全操作与限制说明 |
