# WP Clean Admin FAQ 与故障排除指南

> **适用基线**：1.9.18　|　**更新日期**：2026-11-25　|　**状态**：用户文档
> 前置阅读：[`安装部署指南.md`](安装部署指南.md)、[`user-guide.md`](user-guide.md)。本文列出**当前版本已知现象**；缺陷跟踪见 [`优化建议.md`](优化建议.md) 与 [`tasks.md`](tasks.md)。

## 目录

- [1. 常见问题（FAQ）](#1-常见问题faq)
- [2. 故障排除矩阵](#2-故障排除矩阵)
- [3. 诊断与日志方法](#3-诊断与日志方法)
- [4. 紧急恢复](#4-紧急恢复)
- [5. 提交问题前检查清单](#5-提交问题前检查清单)
- [6. 文档版本控制](#6-文档版本控制)

---

## 1. 常见问题（FAQ）

**Q1：插件激活后在哪里配置？**
后台 → WP Clean Admin → 设置，包含 General / Cleanup / Performance / Security / Diagnostics 五个选项卡。插件列表也有 Settings 快捷链接（`wp-clean-admin.php:218-233`）。

**Q2：为什么开启菜单定制后看不到变化？**
1.9.18 起该缺陷已修复：菜单定制统一以 `wpca_menu_customizer_settings` 为**唯一数据源**（`Menu_Customizer_Options` 负责读写，Tree/Render 通过构造器注入同一实例），4 个 AJAX action **无条件注册**（不再受默认未启用的 `settings.menu_customizer` 守卫限制），旧版 `wpca_settings.menu_customizer` 数据会在首次读取时幂等迁移。若仍无变化，请先确认浏览器 Network 中 `admin-ajax.php` 返回 `success:true`，并清空对象缓存后重试。

**Q3：设置保存后没有立即生效？**
设置写入 `wpca_settings`，由下一次请求通过 `wpca_get_settings()` 读取（1.9.18 起请求级记忆化，写路径自动失效缓存）。若站点使用对象缓存插件，请刷新其缓存。字段回显已在 1.9.18 修复：渲染优先读取嵌套 `wpca_settings[group][id]`，缺失时回退独立 option，表单 `name` 亦同步为 `wpca_settings[group][id]`（OPT-003）。

**Q4：登录验证码图片不显示？**
检查 PHP GD 扩展是否安装。缺失时图片生成被安全跳过，同时登录校验会拒绝未提供验证码的请求。安装 GD 后重启 Web 服务并重试（见 `modules/admin/classes/class-wpca-login-captcha.php`）。

**Q5：开启 2FA 后无法登录怎么办？**
使用验证器 App 的 6 位动态码；表单 nonce 字段为 `wpca_nonce`。若丢失绑定，需通过数据库清除该用户的 2FA user meta（先备份），或由管理员协助恢复；后续版本应提供恢复码（见 OPT-004/未来展望）。

**Q6：文件缓存异常或内容陈旧？**
1.9.18 起文件缓存目录已迁至 `wp-content/uploads/wpca-cache/`（含 `index.php` 与 `.htaccess` 防护），写入改为临时文件 + `rename()` 原子替换，清理交由 hourly cron 承担。删除该目录下的 `.cache` 文件即可清空；读取时损坏或旧序列化文件会被自动丢弃。频繁异常时切换 `performance` 缓存后端为 database/memory。旧目录 `wpcleanadmin/cache/` 可手动删除。

**Q7：为什么匿名 REST 请求仍可访问？**
1.9.18 起 REST 限制改在 `rest_api_init` 阶段注册 `rest_authentication_errors`，匿名请求返回 `403`（错误码 `rest_not_logged_in`），已登录用户不受影响（OPT-005）。请注意：该开关只拦截 REST API，**不要将其作为唯一安全边界**，需要彻底关闭时仍建议在服务器层额外限制。

**Q8：卸载插件后还有残留数据吗？**
`uninstall.php` 删除 `wpca_%` options、transients 与 usermeta，并在 1.9.18 补充清理 WP-Cron 事件与文件缓存目录（`uploads/wpca-cache`）。**备份文件与日志目录仍需手动清理**（`backups/` 与日志目录）。

**Q9：多站点（Multisite）能用吗？**
`Helpers::is_network_activated()` 提供网络激活检测；设置按站点存入各自 `wpca_settings`。请逐站验证角色、菜单与登录保护，不要假设网络级统一配置已实现。

**Q10：如何确认 AJAX 请求被正确鉴权？**
打开浏览器开发者工具 Network，检查 `admin-ajax.php` 请求：表单需含 `_wpnonce`（动作 `wpca_ajax_nonce`）；无 nonce 返回 `{"success":false,"data":"Invalid nonce"}`，权限不足返回 `Insufficient permissions`。

**Q11：仪表盘很慢？**
1.9.18 起统计结果改为 15 分钟 transient 缓存（`wpca_dashboard_stats`），命中缓存时不再执行 `information_schema` 求和与前导通配符 `LIKE '%transient%'` 扫描，孤儿 meta 统计改用索引友好的 `NOT EXISTS`（OPT-010）。首次访问仍会计算一次；如仍缓慢，可暂时关闭对应小部件。

**Q12：如何知道某个功能是否启用？**
查看 `wpca_settings` 选项（数据库 `wp_options` 或 `wpca_get_settings()`）；激活默认值见 [`安装部署指南.md`](安装部署指南.md) §4.2。

## 2. 故障排除矩阵

| 现象 | 可能原因 | 检查方法 | 处置 | 参考 |
|------|----------|----------|------|------|
| 设置页空白 / JS 报错 | 资源未加载（E-2 抽离后路径错误） | Network 检查 `assets/js/wpca-settings.js`、`assets/css/wpca-settings.css` 是否 200 | 确认插件目录名为 `wp-clean-admin`，重新上传完整包 | 安装部署 §2 |
| 保存 AJAX 返回 `Invalid nonce` | nonce 过期或未传 `_wpnonce` | Network 查看请求载荷 | 刷新设置页重新获取 nonce | API 文档 §2 |
| 保存 AJAX 返回 `Insufficient permissions` | 当前用户无 `manage_options` | 检查用户角色 | 使用管理员账号 | 用户指南 §1 |
| 菜单定制保存成功但无效 | 对象缓存未刷新 / 迁移期数据未写入 | 对比 `wpca_menu_customizer_settings` 与 `wpca_settings`；查看 Network 响应 | 1.9.18 已统一数据源并无条件注册 action；清空对象缓存后重试 | OPT-001 |
| 验证码不显示且无法登录 | GD 缺失 | `php -m | grep gd`（需 PHP 环境）或主机面板 | 安装 GD；紧急时数据库清除验证码开关 | FAQ Q4 |
| 文件缓存反复失效 | 目录不可写 / 旧序列化文件 | 检查 `uploads/wpca-cache/` 权限与文件内容是否 JSON | 赋写权限或切换缓存后端；旧 `wpcleanadmin/cache/` 可删除 | FAQ Q6 |
| REST 限制无效 | 开关未开启 / 服务器层另有放行 | 直接请求 `/wp-json` 观察状态码 | 1.9.18 起匿名请求返回 403；仍建议服务器层额外限制 | OPT-005 |
| 卸载后残留 `wpca_*` 数据 | 卸载未覆盖自定义键 | SQL `SELECT option_name FROM wp_options WHERE option_name LIKE 'wpca_%'` | 手动删除（先备份） | FAQ Q8 |
| 设置字段回显旧值 | 对象缓存未刷新 | 保存后刷新对比数据库值 | 1.9.18 已改为读取嵌套 `wpca_settings[group][id]`；清空缓存后重试 | FAQ Q3 |
| 同一字段在四个 Tab 重复出现 | 旧版本 `do_settings_sections` 每 Tab 重复调用 | 检查页面 HTML id 重复 | 1.9.18 已改为每 Tab 独立 page slug 唯一渲染 | OPT-009 |

## 3. 诊断与日志方法

1. **WordPress 调试**：在 `wp-config.php` 设置 `WP_DEBUG=true`（生产环境请勿长期开启），插件错误经 `Error_Handler` 与 `wpca_log()` 写入 PHP 错误日志。
2. **插件诊断页**：General/Diagnostics 选项卡提供系统信息与告警；`diagnostics.show_warnings` 与 `severity_filter` 控制展示。
3. **浏览器开发者工具**：检查控制台错误、Network 中 `admin-ajax.php` 请求与响应、设置页语义 ID（`wpca-settings-tabs` 等）。
4. **数据库核对**：`wp_options` 中查询 `wpca_%`；缓存项前缀 `wpca_cache_%`；用户元数据前缀 `wpca_`。
5. **自动加载冒烟测试**：`tests/PluginAutoloadSmokeTest.php` 检查类可加载（需 PHP 环境，由 CI 执行）。
6. **版本核对**：`wpcleanadmin/wp-clean-admin.php` 的 `Version:` 与 `WPCA_VERSION` 必须一致（当前 1.9.18）；版本历史见 `CHANGELOG.md`。

## 4. 紧急恢复

| 场景 | 步骤 |
|------|------|
| 插件导致后台不可用 | 将 `wp-content/plugins/wp-clean-admin` 目录改名（如 `wp-clean-admin.disabled`），WordPress 会自动停用；随后排查 |
| 数据库异常写入 | 从备份恢复 `wp_options` 中 `wpca_%` 记录；或执行 `DELETE FROM wp_options WHERE option_name LIKE 'wpca_%'`（**先备份**） |
| 登录被 2FA 锁定 | 通过数据库清除对应用户的 2FA user meta（先备份），或恢复站点备份 |
| AJAX 全面失败 | 检查 `admin-ajax.php` 是否可访问、nonce 动作串是否为 `wpca_ajax_nonce`、是否使用管理员账号 |
| 升级后异常 | 按 [`安装部署指南.md`](安装部署指南.md) §7 回滚插件目录与数据库 |

## 5. 提交问题前检查清单

- [ ] 插件版本与 `WPCA_VERSION` 一致（1.9.18），包完整（含 `includes/modules/`、`uninstall.php`）
- [ ] 已按 FAQ 与故障排除矩阵自查
- [ ] 记录 WordPress/PHP/MySQL 版本、浏览器、复现步骤、Network 响应体
- [ ] 明确区分“已知限制”（本文/OPT-*）与新问题
- [ ] 新问题按 [`tasks.md`](tasks.md) 格式登记（优先级、责任人、计划时间）

## 6. 文档版本控制

| 文档版本 | 适用插件版本 | 日期 | 维护人 | 变更摘要 |
|----------|--------------|------|--------|----------|
| 1.1 | 1.9.18 | 2026-11-25 | Tanox | OPT-001/003/005/007/010/012 落地后更新对应问答与矩阵条目（菜单定制、字段回显、REST 限制、文件缓存目录、仪表盘统计、卸载清理） |
| 1.0 | 1.9.17 | 2026-11-25 | Tanox | 首版：12 条 FAQ、故障排除矩阵、诊断方法、紧急恢复与提交清单 |
