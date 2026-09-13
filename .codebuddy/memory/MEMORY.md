# MEMORY.md — WPCleanAdmin 长期记忆

> 跨会话稳定事实，按需更新并注明日期。

## 版本与文档同步约定（2026-09-13 确立）
- 版本号权威来源：`wpcleanadmin/wp-clean-admin.php` 的 `WPCA_VERSION` 常量（当前 **1.8.11**），插件头 `Version:`、README 徽章、根 `CHANGELOG.md` 须与之保持一致。
- autoload 命名约定（1.8.10 确立）：主命名空间类/trait 文件按 `includes/class-wpca-{kebab}.php` 加载；trait 名须用**无下划线驼峰**（如 `CleanupDatabaseTasks` → `class-wpca-cleanup-database-tasks.php`），含下划线类名会被解析为双连字符路径（如 `Cleanup_Database_Tasks` → `class-wpca-cleanup--database--tasks.php`）导致 fatal。拆分核心类时主类保留单例 + `use` 业务 trait，公开方法契约不变。
- **含下划线类名 autoload 不可达（1.8.19 发现，尚未修复）**：`includes/autoload.php:48-50` 与 `wp-clean-admin.php:51-55` 的 fallback 规则是「对非首字母大写插 `-`」+「`_`→`-`」两步，对含下划线类名叠加产出**双连字符**（`Menu_Customizer` → `menu--customizer`，实际文件 `class-wpca-menu-customizer.php`）→ 加载失败。受影响类：`Menu_Customizer`、`Menu_Manager`、`User_Roles`、`Error_Handler`、`Extension_API`（根版与 modules 版同名，均声明根命名空间）。`core.php:142` 与 `module-loader.php` 用 `class_exists`/`safe_init` 静默加载、失败不报错，故这些根版大文件（534/423/417/321 行）**疑似死代码**。**拆分这批类前必须先决策 E-1**：(a) 改 autoload 规则兼容下划线 or (b) 统一类名去下划线。**无下划线类（Database/Dashboard/Permissions/Resources/Reset/Cleanup/Cache/Helpers/Diagnostics 等）可达且活跃，可正常拆分。**
- **双体系（1.8.11 发现）**：`includes/modules/**` 是**活的平行类体系**，`autoload.php:67-82` 支持 `WPCleanAdmin\Modules\*` → `includes/modules/`。故 `includes/class-wpca-X.php`(namespace `WPCleanAdmin\X`) 与 `includes/modules/**/class-wpca-X.php`(namespace `WPCleanAdmin\Modules\...\X`) 是两套并行活类，行数常对应（helpers 614/614、cache 536/536、resources 356/356）；但 `modules/admin/classes/class-wpca-login.php` 是未拆旧 324 行副本（includes 根 Login 已拆为 116）。批量拆分须分别处理两套。
- 渲染类 trait 因内联 CSS/JS 仍 >200（如 SettingsPageRenderTasks 421），彻底达标需抽离 `assets/css|js` 并改 enqueue，列为专项。
- `includes/wpca-wordpress-stubs.php`(775) 是 autoload 加载的 IDE 空函数声明集，非业务逻辑，排除拆分。
- openspec 规范文件的「描述性当前版本」引用（项目概述、`WPCA_VERSION`、`@version`、当前 API 版本、示例插件头 `Version:`）须在发版时同步到当前版本；**历史变更表、提案基线、`见 X.Y.Z 修复记录`、架构设计中「X.Y.Z 确立」等历史引用禁止改动**。
- 原型 `prototype/` 无插件版本串，无需版本同步。
- `update_versions.php` / `update_versions.ps1` 为硬编码旧版（1.8.0→1.8.1）的过时批量工具，不宜直接运行；版本同步建议手工按文件精确替换，避免无关 diff 膨胀。
- **phpcs/CI 标准注册（1.8.12 修复）**：`wpcleanadmin/phpcs.xml.dist` 的 `<config name="installed_paths">` 用相对路径在 CI 中会覆盖 `composer-installer` 自动注册并解析失败，导致 `Referenced sniff ... does not exist`。`.github/workflows/ci.yml` 的 phpcs job 须用 `--config-set installed_paths` 注册**全部 4 个绝对路径**（`wpcs`/`phpcsutils`/`normalizedarrays`/`modernize`），否则 Universal/Modernize/NormalizedArrays 标准缺失。本地由 `composer.json:30` 的 dealerdirect installer 自动注册，勿在 ruleset 写相对 installed_paths。

## 目录边界（2026-09-13 确认）
- 插件运行时代码仅驻 `wpcleanadmin/`；原型 `prototype/`、文档 `docs/`、规范 `openspec/`、根级 README/CHANGELOG/AGENTS 等不放入 `wpcleanadmin/`。
- Community Health Files 驻 `.github/`，**禁止创建 `.github/readme.md`**（全局规则）。
- 仓库 README 默认中文 `README.md`；英文 `README.en.md`。
