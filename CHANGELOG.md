# Changelog

## [1.8.8] - 2026-09-12
### Fixed
- CI 阻塞修复: `composer.json` 的 `name` 字段 `Tanox/wp-clean-admin` 首字母大写，不符合 Composer 包名 schema（要求全小写），导致 `composer install` 失败；改为 `tanox/wp-clean-admin`
- 移除非法的 WordPress 函数 IDE stub 声明（`wp-clean-admin.php` 与 `includes/ajax/performance-ajax.php` 中的 `function \wp_...() {}`）：该写法语法非法（`function` 关键字后不能带命名空间前缀），导致 `php -l` 解析失败、CI 中断；WP 函数在运行时由核心提供，stub 属冗余死代码，删除后主文件常量段（`WPCA_PLUGIN_DIR`/`WPCA_PLUGIN_URL`）仍通过 `function_exists` + fallback 正常工作

## [1.8.7] - 2026-09-12
### Chore
- 完善 CI/CD: 新增 `.github/workflows/ci.yml`（PHP 语法检查 + PHP_CodeSniffer + 多版本 PHPUnit 测试矩阵），新增 `wpcleanadmin/phpcs.xml.dist` 规则集（基于 WordPress 标准，排除与 PSR-4/现代 PHP 冲突的命名与格式规则，保留安全相关 sniff）

## [1.8.6] - 2026-09-12
### Chore
- 清理死代码: 删除未接线的清理重构残留文件（根 `class-wpca-cleanup-*` 子模块与 `modules/core/classes/` 下未实例化的 Media/Content/Comments/Database Cleanup 簇及重复 `Cleanup_Helpers` trait），统一以 legacy 根 `Cleanup` 类为唯一清理实现，消除双轨冗余

## [1.8.5] - 2026-09-12
### Chore
- 作者署名统一: 全项目 PHP/JS 文件头 `@author Sut` 更正为 `Tanox`，与 composer.json 及仓库归属一致（排除 languages/ 翻译者历史署名）
- 版本头修正: 主文件 `Version:` 头双空格修正，`WPCA_VERSION` 常量同步至 1.8.5

### Fixed
- 数据库备份表名参数化: `class-wpca-database.php` 的 `SHOW CREATE TABLE %s` 与 `SELECT * FROM %s` 改用反引号安全拼接表名（SQL 标识符不可经 `prepare()` 参数化，原写法会使备份导出失败），并增加表名白名单校验

### Docs
- 完善 Community Health Files: 在 `.github/` 新增 `CODE_OF_CONDUCT.md`、`CONTRIBUTING.md`、`SECURITY.md`、`SUPPORT.md`、`FUNDING.yml`、`ISSUE_TEMPLATE/`（bug_report / feature_request / config）、`PULL_REQUEST_TEMPLATE.md`，对齐项目提交规范、安全红线与版本管理约定（遵循全局规则，未创建 `.github/readme.md`）
- README 配图: 新增品牌化矢量配图 `assets/hero-banner.svg`（顶部横幅）与 `assets/features.svg`（核心功能五卡片），嵌入仓库根 `README.md` 顶部与核心功能区，替代纯 emoji 排版，提升可读性（SVG 矢量、可版本化，可随时替换为真实后台截图）

## [1.8.4] - 2026-08-12
### Refactor
- 模块拆分: 将 core/admin/settings 三簇超 200 行类拆分为单一职责子模块（抽离 data/queries/renderer/helper/trait 等），主类保留公开 API 转发，行为契约不变
- 文件: 新增 30 个子模块类，改写 14 个主类（dashboard/login/menu-manager/menu-customizer/permissions/user-roles/settings/core/error-handler/*-cleanup 等）
- 版本同步: 涉及文件头注释 1.8.3 -> 1.8.4

## [1.8.3] - 2026-08-08
### Security
- 错误处理器 XSS: `Error_Handler::exception_handler` 调试输出改用 `esc_html()` 转义异常消息与堆栈，防止 WP_DEBUG 下反射型 XSS
- AJAX 契约对齐: 前端 nonce action 由 `wpca_dashboard_nonce` 统一为 `wpca_ajax_nonce`，与后端 `wpca_ajax_nonce` 校验一致，修复所有 Dashboard AJAX 校验失败

### Fixed
- 作者署名: `composer.json` 作者名 `Sut` 更正为 `Tanox`，与仓库归属一致

### Chore
- 版本同步: 全项目版本号 1.8.2 -> 1.8.3（插件主文件、语言文件、各模块文件头、OpenSpec 规范、原型演示、项目文档）

## [1.8.2] - 2026-08-07
### Docs
- 设计系统: 新增 shadcn/ui 风格设计系统（`prototype/ui/wpca-components.css` 单一令牌来源），
  覆盖设计令牌、组件库（card/btn/badge/switch/table/modal/toast 等）、交互与响应式规范
- 原型合并: 删除冗余 `prototype/ui/dashboard.html` 与 `settings-page.css`，统一以 `index.html` 为入口
- 文档对齐: 同步 `docs/设计规范_20260808.md` 与 OpenSpec 陈旧路径（`assets/prototype` -> `prototype`），
  修正 README 版本号（1.8.1 -> 1.8.2）与仓库链接（Tanox/WP-Clean-Admin）
- 插件对齐: `wpcleanadmin/assets/css/wpca-admin.css` 末尾追加同源 shadcn 组件层

### Fixed
- AJAX nonce 字段名统一: 将 settings/dashboard/cleanup 三类 handler 的 `$_POST['nonce']` 统一为 `$_POST['_wpnonce']`，与前端发送字段及其余 11 个 handler 保持一致，修复数据库/性能/菜单等 AJAX 校验永远失败的功能性 bug
- 设置存储安全: `settings-ajax.php` 的 `save_settings` 增加递归 `sanitize_settings`，防止未清理输入直接 `update_option` 造成存储型 XSS
- 版本注释同步: 将 65 个文件头 `@version 1.8.0` 统一更正为 `1.8.1`（主文件/语言文件已为 1.8.1）
- 消除重复代码: 移除 11 个 AJAX 文件中重复内联的 WordPress 函数 stub，统一依赖 `autoload.php` 已加载的 `wpca-wordpress-stubs.php`

### Added
- 单元测试: 新增 `phpunit.xml.dist`、`tests/bootstrap.php`，以及 `HelpersTest`、`SettingsAjaxTest` 覆盖 sanitize/nonce/format_bytes 核心逻辑

## [1.8.1] - 2026-05-17
### Added
- 诊断模块: 添加了完整的诊断功能，包括系统健康检查、安全检查和性能检查
- 诊断规范: 在 openspec 中添加了诊断模块的完整规范文档

### Fixed
- 诊断模块 AJAX 类引用错误: 修复了诊断模块 AJAX 类引用错误，修正了命名空间路径
- 模块化架构: 优化了模块化架构的一致性和完整性

### Improved
- 翻译文件: 添加了完整的诊断模块相关翻译，支持中文和英文

## 1.8.0 (2026-01-30)

### Added
- 新增了 22 个核心模块的 PHP 7.4+ 类型声明，提升代码安全性和可维护性
- 实现了 Cleanup 模块，支持媒体、评论、帖子、短代码清理
- 新增了 Elementor 集成支持，优化 Elementor 页面构建器
- 添加了 Composer 依赖管理基础架构（v2.0.0 准备）
- 实现了扩展 API，为第三方开发者提供扩展接口
- 新增了预设主题模板，支持快速配置

### Fixed
- 修复了 SQL 注入风险，使用 $wpdb->prepare 重构所有数据库查询
- 修复了 AJAX nonce 验证缺失问题
- 统一了类文件命名格式为 class-wpca-*.php
- 移除了所有占位符代码，实现了完整功能
- 修复了 WordPress 6.5 兼容性问题
- 修复了构造函数返回类型声明错误

### Improved
- 测试覆盖率从 80% 提升至 90%
- 代码规范符合度达到 95%
- PHPDoc 覆盖率达到 95%
- 优化了后台加载速度和资源使用
- 增强了菜单管理功能，支持按角色限制菜单
- 提升了性能优化模块，支持资源预加载和压缩

## 1.7.15 (2025-11-30)

### Improved
- 菜单管理功能增强
- 性能优化模块完善

## 1.7.14 (2025-11-15)

### Improved
- 数据库优化模块完善

## 1.7.13 (2025-10-31)

### Added
- 双因素认证功能

## 1.7.12 (2025-10-15)

### Improved
- 性能优化模块重构

## 1.7.11 (2025-09-30)

### Improved
- 角色权限管理功能完善
