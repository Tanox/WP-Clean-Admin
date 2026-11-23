# WP Clean Admin 用户使用文档（T013）

> 版本基线 1.9.17　|　完整功能营销页见根 [`README.md`](../README.md)　|　本文聚焦**安装、配置与排障**。

## 安装

1. WordPress 后台 → 插件 → 安装插件 → 上传 `wp-clean-admin.zip`（Release 附件，CI 全绿自动发布）。
2. 激活插件；环境要求：WordPress ≥ 5.0、PHP ≥ 7.4、MySQL ≥ 5.6。

## 核心配置入口

- **设置页**：后台菜单 → WP Clean Admin → 设置，五个选项卡：General（常规）/ Cleanup（清理）/ Performance（性能）/ Security（安全）/ Diagnostics（诊断）。选项卡容器与按钮均带语义化 ID（`wpca-settings-tabs` / `wpca-tab-*`），便于调试定位。
- **设置存储**：全部配置存于 `wpca_settings` 选项；保存前经服务端递归消毒（`wpca_update_settings` 过滤器可供其他插件在入库前干预）。

## 常用功能速查

| 功能 | 开启位置 | 说明 |
|------|----------|------|
| 隐藏后台菜单 | Cleanup / 菜单管理 | 支持按角色限制（`role_based_restrictions`） |
| 清理仪表盘小部件 | Cleanup / 菜单管理 | 支持按小部件 ID 粒度选择（`dashboard_widgets`） |
| 精简管理栏 | General | `clean_admin_bar` 开关 |
| 禁用 emoji / 心跳 | Performance | 减少前台与后台请求 |
| 数据库备份/恢复/删除 | 数据库管理 | 删除操作含路径边界与扩展名白名单防护 |
| 登录保护（验证码 / 2FA / 限次） | Security | 验证码需 GD 扩展；2FA 为 TOTP |

## 排障

- **缓存异常**：删除 `wp-content/plugins/wp-clean-admin/cache/` 目录下文件即可（文件缓存为 JSON 格式，损坏文件读取时自动清理）。
- **登录验证码不显示**：检查 PHP GD 扩展（缺失时验证码图片生成会被安全跳过并拒绝未带验证码的登录）。
- **菜单恢复默认**：设置页重置操作会清空 `wpca_menu_items` / `wpca_menu_customizer_settings`。
- **性能选项不生效**：确认对应 `performance.*` 开关为开启；缓存 TTL 可通过 `performance.cache_expiration` 调整（秒，≤0 回退 3600）。

## 卸载

- 停用并删除插件即触发 `uninstall.php`：清理插件选项、瞬态与用户元数据（备份文件目录保留，需手动删除）。

---

*本文与插件版本同步更新；变更记录见根 `CHANGELOG.md`。*
