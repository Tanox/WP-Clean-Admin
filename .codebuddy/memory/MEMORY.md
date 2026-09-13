# MEMORY.md — WPCleanAdmin 长期记忆

> 跨会话稳定事实，按需更新并注明日期。

## 版本与文档同步约定（2026-09-13 确立）
- 版本号权威来源：`wpcleanadmin/wp-clean-admin.php` 的 `WPCA_VERSION` 常量（当前 **1.8.20**，2026-09-13 更新），插件头 `Version:`、README 徽章、根 `CHANGELOG.md` 须与之保持一致。
- autoload 命名约定（1.8.10 确立）：主命名空间类/trait 文件按 `includes/class-wpca-{kebab}.php` 加载；trait 名须用**无下划线驼峰**（如 `CleanupDatabaseTasks` → `class-wpca-cleanup-database-tasks.php`），含下划线类名会被解析为双连字符路径（如 `Cleanup_Database_Tasks` → `class-wpca-cleanup--database--tasks.php`）导致 fatal。拆分核心类时主类保留单例 + `use` 业务 trait，公开方法契约不变。
- **含下划线类名 autoload 与命名空间问题（1.8.19 发现，1.9.0 已修复核心部分）**：`includes/autoload.php` 与 `wp-clean-admin.php` 的 fallback 规则对含下划线类名会产出双连字符路径（`Menu_Customizer` → `menu--customizer`）。**v1.9.0（2026-09-13，提交 6651d8a）已修复**：`modules/admin/classes/**`（20 文件）与 `modules/utilities/classes/**`（4 文件）的 namespace 由误写的根命名空间 `WPCleanAdmin` 改正为 `WPCleanAdmin\Modules\Admin\Classes` / `WPCleanAdmin\Modules\Utilities\Classes`，消除与根版同名类的 FQCN 冲突（原为 Cannot declare class 致命错误）；同时移除 utilities/helpers.php 指向不存在文件的 stubs require；新增 `tests/PluginAutoloadSmokeTest.php` 对两套类体系关键类做 autoload 冒烟校验。根版类文件（`includes/class-wpca-*.php`）仍为独立活类（legacy 体系），两套并行设计保留。
- **双体系（1.8.11 发现）**：`includes/modules/**` 是**活的平行类体系**，`autoload.php:67-82` 支持 `WPCleanAdmin\Modules\*` → `includes/modules/`。故 `includes/class-wpca-X.php`(namespace `WPCleanAdmin\X`) 与 `includes/modules/**/class-wpca-X.php`(namespace `WPCleanAdmin\Modules\...\X`) 是两套并行活类，行数常对应（helpers 614/614、cache 536/536、resources 356/356）；但 `modules/admin/classes/class-wpca-login.php` 是未拆旧 324 行副本（includes 根 Login 已拆为 116）。批量拆分须分别处理两套。
- 渲染类 trait 因内联 CSS/JS 仍 >200（如 SettingsPageRenderTasks 421），彻底达标需抽离 `assets/css|js` 并改 enqueue，列为专项。
- `includes/wpca-wordpress-stubs.php`(775) 是 autoload 加载的 IDE 空函数声明集，非业务逻辑，排除拆分。
- openspec 规范文件的「描述性当前版本」引用（项目概述、`WPCA_VERSION`、`@version`、当前 API 版本、示例插件头 `Version:`）须在发版时同步到当前版本；**历史变更表、提案基线、`见 X.Y.Z 修复记录`、架构设计中「X.Y.Z 确立」等历史引用禁止改动**。
- 原型 `prototype/` 无插件版本串，无需版本同步。
- `update_versions.php` / `update_versions.ps1` 为硬编码旧版（1.8.0→1.8.1）的过时批量工具，不宜直接运行；版本同步建议手工按文件精确替换，避免无关 diff 膨胀。
- **phpcs/CI 标准注册（1.8.12 初修，1.8.20 更正）**：`wpcleanadmin/phpcs.xml.dist` 的 `<config name="installed_paths">` 用相对路径在 CI 中会覆盖并解析失败，导致 `Referenced sniff ... does not exist`，故已删除该配置。`.github/workflows/ci.yml` 的 phpcs job 用 `--config-set installed_paths` 注册**3 个绝对路径**：`vendor/wp-coding-standards/wpcs`（WordPress）、`vendor/phpcsstandards/phpcsutils`（Universal）、`vendor/phpcsstandards/phpcsextra`（**NormalizedArrays + Modernize**）。**重要：不存在 `phpcsstandards/normalizedarrays` / `phpcsstandards/modernize` 包（1.8.20 前 composer.json 误写导致 CI composer install 失败 exit 2），这两个标准由 `phpcsstandards/phpcsextra` 提供；且 `dealerdirect/phpcodesniffer-composer-installer` 并未列入 require-dev，故 CI 必须显式 `--config-set`。**

## 运行期阻塞缺陷（2026-09-13 审查 → 1.9.0 修复进展）
- **P0 已修复（v1.9.0，提交 6651d8a）**：modules 35→实为 24 个文件 namespace 冲突、helpers.php 假 require、2FA 认证绕过（根版+modules 版同步：改为返回 WP_Error 阻止认证+验证码表单链接）、modules 版 menu-customizer 4 个 AJAX 处理器补 nonce+manage_options、8 个生产文件 + 3 个测试文件补 ABSPATH 守卫、Requires PHP 7.0→7.4（插件头+composer.json）、移除 Network: true、新增 uninstall.php + WP.org readme.txt。版本 1.8.20→1.9.0。
- **仍未处理**：WPCS 门禁未收紧（phpcs.xml.dist 关 12 条规则、CI continue-on-error，属有意 PSR-4 混合风格）；备份落 web 可访问目录与恢复 SQL 白名单问题；部分 handler 整包 update_option 无字段白名单；本机无 PHP，v1.9.0 修复未经实机/PHPUnit 运行验证，需观察 CI 结果。

## 环境与协作约定（2026-09-13）
- **本机无 PHP / Composer / gh CLI**（Windows + PowerShell 环境），PHP 语法与 PHPUnit 结果只能靠 CI 验证；PowerShell 下用 `cd E:\Github\WPCleanAdmin`（不要用 `/e/...`）。`php -l` 只看语法，语言结构误加 `\` 前缀（如 `\array(`/`\isset(`）不报错但运行期 fatal，需人工搜。
- **判断"文件现状"须以磁盘/HEAD 为准**：`read_file`/`search_content` 可能返回过期缓存内容（1.8.20 轮次实测与 HEAD 不一致）。改用 `Get-Content <path>` 或 `git show HEAD:<path>` 复核，再决定是否改动。
- **CI 红灯先查"本地是否已修但未推送"**：`git status -sb` 看 `ahead N`；多轮 CI 失败常是修复已提交未 push 所致（1.8.9 bootstrap、1.8.20 composer.json 均如此）。
- **提交信息中文必须走 `-F` 文件**：本机执行命令行会经临时 `.ps1`（无 BOM）被 PowerShell 按 GBK 解析，`git commit -m "中文"` 会把中文写成乱码对象（1.8.20 轮次实测 `fcdf1de` 即是；`git log` 里同一终端下乱码版与 `-F` 版显示截然不同可判别）。改用 `write_to_file` 写 UTF-8 消息到 `.git/cb-msg.txt` + `git commit -F .git/cb-msg.txt`（`--amend` 同样用 `-F`）。

## 目录边界（2026-09-13 确认）
- 插件运行时代码仅驻 `wpcleanadmin/`；原型 `prototype/`、文档 `docs/`、规范 `openspec/`、根级 README/CHANGELOG/AGENTS 等不放入 `wpcleanadmin/`。
- Community Health Files 驻 `.github/`，**禁止创建 `.github/readme.md`**（全局规则）。
- 仓库 README 默认中文 `README.md`；英文 `README.en.md`。
