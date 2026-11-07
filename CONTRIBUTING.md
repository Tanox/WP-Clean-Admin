# WP Clean Admin 贡献与开发规范

> **状态**：权威（本项目开发规范的唯一来源）　|　**适用基线**：1.9.15　|　**生效日期**：2026-11-07
> **维护者**：Tanox　|　**仓库**：https://github.com/Tanox/WP-Clean-Admin

本文档定义 WP Clean Admin 的编码风格、命名约定、目录组织、提交信息格式、分支管理、代码审查流程、测试、版本管理与发布流程。**所有贡献者（含 AI 助手）在修改代码或文档前必须阅读本文**。

出现冲突时，优先级为：**代码实现 > 本规范 > `DEVELOPMENT.md` / `AGENTS.md` > `openspec/specs/` 规范文档 > 历史报告**。任何文档与代码不一致时，以代码为准并同步修正文档。

---

## 0. 文档体系与职责

| 文件 | 职责 | 权威性 |
|------|------|--------|
| `CONTRIBUTING.md`（本文） | 开发规范、流程、约定 | **唯一权威规范** |
| `README.md` / `README.en.md` | 面向用户的功能介绍、安装与使用 | 用户文档 |
| `DEVELOPMENT.md` | 开发环境快速上手（命令、原型预览） | 快速参考，不重复规范 |
| `AGENTS.md` | AI 助手工作指南、目录边界、OpenSpec 流程 | 助手指令 |
| `CHANGELOG.md` | 版本变更记录 | **唯一版本历史来源** |
| `docs/README.md` | 文档索引、文档编写格式、术语表 | 文档元规范 |
| `docs/tasks.md` / `docs/tasks-done.md` | 进行中 / 已归档任务 | 任务跟踪 |
| `docs/设计规范_20260808.md` | UI 设计系统（令牌、组件、交互） | 历史基线快照，见 §0.2 |
| `docs/审计报告_20260808.md` | 2026-08 代码审查记录 | 历史快照，不代表当前状态 |
| `openspec/` | OpenSpec 功能规范与变更提案 | 需求/设计层 |
| `prototype/` | 纯静态高保真原型 | 无后端，不参与运行时 |
| `archive/` | 归档的 legacy 死代码 | **只读，禁止引用/修改/加载** |

### 0.1 文档编写格式

- 语言：面向用户的文档用简体中文；`README.en.md` 为英文镜像，须与 `README.md` 功能列表和版本号同步。
- 文件名：中文文档使用 `名称_YYYYMMDD.md`（如 `审计报告_20260808.md`）；规范类文档使用小写连字符英文或稳定中文名，不带日期。历史文件保留原名，不重命名。
- `<!-- OPENSPEC:START -->` / `<!-- OPENSPEC:END -->` 标记**仅适用于 `openspec/` 目录下的规范与提案文档**；根目录与 `docs/` 下文档不使用该标记。
- Markdown 使用 ATX 标题（`#`）、四个空格或 `-` 列表、围栏代码块标注语言、表格对齐；**同一文档内章节编号不得重复**。
- 每份文档须标注版本或基线；版本号必须与 `wpcleanadmin/wp-clean-admin.php` 的 `Version:` 一致。

### 0.2 历史文档说明

`docs/审计报告_20260808.md` 与 `docs/设计规范_20260808.md` 基于 **1.8.3** 基线撰写，其中描述的 `prototype/php/`、`prototype/ui/` 目录、`USE_MOCK` 开关、双 UI 入口等均已不存在。这些文件作为历史快照保留，**不得据此判断当前状态**；当前结构以 §2 与 `prototype/` 实际文件为准。

---

## 1. 项目事实基线

| 项 | 值 | 来源 |
|----|-----|------|
| 插件版本 | `1.9.15` | `wpcleanadmin/wp-clean-admin.php` 的 `Version:` 与 `WPCA_VERSION` |
| 最低 PHP | 7.4 | 插件头 `Requires PHP` / `composer.json` |
| 最低 WordPress | 5.0 | 插件头 `Requires at least` |
| Text Domain | `wp-clean-admin` | 插件头 / 常量 `WPCA_TEXT_DOMAIN` |
| 运行时入口 | `wp-clean-admin.php` → `plugins_loaded` → `WPCleanAdmin\Modules\Core\Classes\Core` | `wp-clean-admin.php:81,101` |
| 权威命名空间 | `WPCleanAdmin\Modules\*` | `class-wpca-module-loader.php` 实例化清单 |
| 自动加载 | `includes/autoload.php`（主）+ 主文件 fallback | 见 §3.2 |
| 测试 | PHPUnit 9，5 个测试文件 | `wpcleanadmin/tests/`、`phpunit.xml.dist` |
| CI | `.github/workflows/ci.yml` | lint / phpcs / phpunit 矩阵 |
| Release | `.github/workflows/release.yml` | CI 全绿后自动发布 GitHub Release（见 §9.4） |

**架构定论（E-1）**：`WPCleanAdmin\Modules\*` 为**唯一权威体系**。原 `includes/` 根目录下的 legacy 类（115 个文件）已于 1.9.12 归档至 `archive/legacy-includes-1.9.12/`，运行期不加载、不引用。根 `includes/` 仅保留 9 个活文件（见 §2）。禁止在 `archive/` 基础上继续开发。

---

## 2. 目录组织规范

```
仓库根/
├── wpcleanadmin/                 # 插件运行时代码（唯一可部署目录）
│   ├── wp-clean-admin.php        # 插件主文件：常量、autoload 注册、初始化钩子
│   ├── includes/
│   │   ├── autoload.php          # 主自动加载器
│   │   ├── wpca-wordpress-stubs.php      # CLI/PHPUnit 环境 WP 函数桩（禁止拆分）
│   │   ├── wpca-core-functions.php       # 核心函数库入口
│   │   ├── wpca-core-{admin,path,settings,util}-functions.php
│   │   ├── class-wpca-helpers.php        # 兼容层助手类
│   │   ├── ajax/                 # AJAX 命名空间处理器（当前仅 settings-ajax.php）
│   │   └── modules/              # ★ 权威业务实现（见下）
│   │       ├── admin/            # 后台：classes / settings
│   │       ├── core/             # 核心：classes（Core、Module_Loader、Error_Handler）
│   │       └── utilities/        # 工具：classes（Cache、Helpers、Resources、i18n）
│   ├── assets/css/ assets/js/    # 插件后台样式与脚本
│   ├── languages/                # .pot / .po / .mo
│   ├── tests/                    # PHPUnit 测试
│   ├── composer.json / phpunit.xml.dist / phpcs.xml.dist
│   ├── readme.txt / uninstall.php
├── prototype/                    # 纯静态原型：index.html / app.js / wpca-admin.css / wpca-components.css
├── docs/                         # 项目文档（索引、任务、设计/审计快照）
├── openspec/                     # OpenSpec 规范、提案、归档
├── archive/                      # 只读历史代码归档
├── .github/                      # CI、PR/Issue 模板、SECURITY、SUPPORT
├── CONTRIBUTING.md  DEVELOPMENT.md  AGENTS.md
├── README.md  README.en.md  CHANGELOG.md  LICENSE
```

### 2.1 硬约束

1. **`wpcleanadmin/` 只放插件运行时必需文件**。原型、文档、规范、项目级 README/CHANGELOG 一律放根目录对应目录。
2. **`archive/` 为只读归档**：不得 `require`、不得 `use`、不得复制回 `wpcleanadmin/`；如需参考实现，在 `modules/` 中重写。
3. `prototype/` 不含 PHP、不含构建产物、不依赖后端；四个文件平铺，禁止再建 `ui/`、`php/` 子目录。
4. 新增业务类一律落在 `includes/modules/{admin,core,utilities}/` 对应子目录，禁止在 `includes/` 根新增传统类文件。
5. 目录名全小写；PHP 文件名全小写、连字符分隔；**禁止中文文件名之外混入空格或大写**。

---

## 3. 命名约定

### 3.1 总表

| 对象 | 规则 | 正确示例 | 错误示例 |
|------|------|----------|----------|
| 命名空间 | `WPCleanAdmin\Modules\<域>\<层>`；根层仅 `WPCleanAdmin`、`WPCleanAdmin\AJAX` | `WPCleanAdmin\Modules\Admin\Classes` | `WPCleanAdmin\Modules\Admin\Classess` |
| 类名 | PascalCase，**禁止下划线** | `MenuManager` | `Menu_Manager` |
| 文件名 | `class-wpca-{kebab-case}.php` | `class-wpca-menu-manager.php` | `class-wpca-menu--manager.php` |
| 任务 trait | `class-wpca-{模块}-{职责}-tasks.php`，trait 名 PascalCase + `Tasks` | `DashboardWidgetsTasks` | `dashboard_widgets_tasks` |
| 组合 trait | 原 trait 文件保留文件名，内部仅 `use` 子 trait | 见 §4.5 | — |
| 全局函数 | `wpca_` 前缀 + snake_case | `wpca_get_settings()` | `getSettings()` |
| 变量 / 参数 | snake_case | `$menu_items` | `$menuItems` |
| 常量 | `WPCA_` 全大写下划线 | `WPCA_TEXT_DOMAIN` | `wpcaTextDomain` |
| 方法 | 项目现状为 snake_case（`get_dashboard_stats()`）；**同一类内保持一致**，新增方法沿用所在类风格 | — | 混用 camelCase |
| 文本域 | 统一使用常量 `WPCA_TEXT_DOMAIN` | `__('Save', WPCA_TEXT_DOMAIN)` | `__('Save', 'wp-clean-admin')` |
| option / transient | `wpca_` 前缀 | `wpca_settings` | `settings` |
| AJAX action | `wpca_` 前缀 + snake_case；nonce 字段固定 `_wpnonce`，nonce 名 `wpca_ajax_nonce` | `wpca_get_database_info` | `getInfo` |
| CSS 类 | `wpca-` 前缀 + BEM 风格 | `.wpca-menu-item__header` | `.menuItem` |
| HTML id | `wpca-` 前缀 + 语义化名称，全站唯一 | `#wpca-settings-submit` | `#div1` |

### 3.2 类名 → 文件名映射（自动加载器规则）

`includes/autoload.php` 的逻辑：

1. 仅处理以 `WPCleanAdmin\` 开头的类。
2. 去掉前缀后按 `\` 拆分，最后一段为类名，其余为目录路径（小写）。
3. 类名转 kebab-case：`preg_replace('/(?<!^)[A-Z]/', '-$0', $basename)` → `strtolower` → `_` 替换为 `-`。
4. 若首段为 `Modules`，映射到 `includes/modules/<剩余目录>/class-wpca-<kebab>.php`；若首段为 `AJAX`，映射到 `includes/ajax/<kebab>-ajax.php`；否则映射到 `includes/class-wpca-<kebab>.php`。

> **禁止在类名中使用下划线**：`Menu_Manager` 会被转换为 `menu--manager`（双连字符），导致文件名永远匹配不上，类静默不可加载（历史缺陷 E-1）。含下划线类已全部归档。

### 3.3 语义化 ID 规范

原型与插件输出的 HTML 中，可交互容器与关键区域必须带语义化 `id`，便于调试定位与自动化测试：

- 命名：`wpca-<区域>-<用途>`，如 `#wpca-tabs-nav`、`#wpca-tabs-content`、`#wpca-settings-submit`。
- 表单控件 `id` 必须与 `<label for>` 一致；重复渲染的元素用 `data-*` 属性而非重复 `id`。
- 禁止无语义编号（`id="div1"`）、禁止在同一页面重复 `id`。
- 新增交互组件时同步在 `prototype/index.html` 与插件渲染模板中使用相同的 id 命名，保证两端可对照。

---

## 4. 编码风格

### 4.1 通用

- 遵循 **WordPress Coding Standards (WPCS 3.x)**，`phpcs.xml.dist` 为规则集来源，命令见 §8。
- PHP **7.4+** 兼容：禁止使用 PHP 8.0+ 专有语法（命名参数、联合类型声明、match、`?->`、构造器属性提升）。已使用 `string`/`bool`/`?int`/`: void` 等 7.4 兼容写法可以保留。
- 缩进 **4 个空格**，禁用 Tab；行尾 **LF**；文件编码 **UTF-8 无 BOM**；文件末尾保留一个换行。行尾由仓库根 `.gitattributes`（`* text=auto eol=lf`）强制，兼容 Windows 下 `core.autocrlf=true` 环境。
- 每个 PHP 文件开头必须有 `ABSPATH` 守卫：`if ( ! defined( 'ABSPATH' ) ) { exit; }`。
- WordPress 全局函数调用统一加 `\` 前缀（`\get_option()`、`\wp_send_json_success()`），避免被命名空间内同名函数遮蔽。
- 禁止在命名空间内声明与 WordPress 全局函数同名的空函数桩；CLI 需要桩时统一使用 `includes/wpca-wordpress-stubs.php`。

### 4.2 文件头模板

```php
<?php
/**
 * WPCleanAdmin <模块名> <职责>
 *
 * @package WPCleanAdmin
 * @version 1.9.15
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
```

`@version` 必须等于当前插件版本；修改文件时应同步更新（见 §9）。

### 4.3 PHPDoc

- 每个类、trait、方法必须有 PHPDoc；方法须标注 `@param`（含类型）、`@return`；无返回值用 `@return void`。
- 复杂逻辑用中文行内注释说明「为什么」，不要复述「做了什么」。
- 公开 API 变更必须在 PHPDoc 与 `CHANGELOG.md` 中同时说明。

### 4.4 安全基线（强制）

| 场景 | 要求 |
|------|------|
| 所有 AJAX / 表单写操作 | 先 `wp_verify_nonce($_POST['_wpnonce'], 'wpca_ajax_nonce')`，再 `current_user_can('manage_options')`，失败即 `wp_send_json_error` 并 `return` |
| 输入 | `wp_unslash()` + `sanitize_text_field()` / `sanitize_key()` / `absint()` 等，按类型选择 |
| 输出 | `esc_html()` / `esc_attr()` / `esc_url()`；**禁止直接 `echo __()`**，使用 `esc_html__()` 或 `esc_html( __() )` |
| SQL | 变量一律 `$wpdb->prepare()`；表名/列名等标识符用 `$wpdb->prefix` 白名单，不能用 `%s` 包裹标识符 |
| 危险比较 | 令牌/验证码用 `hash_equals()`；随机数用 `random_int()` |
| 文件上传/路径 | `realpath()` 前缀校验 + 随机数文件名 + 存放于 `wp_upload_dir()` 私有目录并加 `.htaccess`/`index.php` 保护 |
| 危险函数 | 禁止 `eval`、`unserialize` 处理外部数据、`shell_exec`、`exec`、`extract`（除非 `EXTR_SKIP` 且无法避免） |

### 4.5 单文件行数与拆分范式

- **单个 PHP 源文件不得超过 200 行**（含注释与空行）。超出必须拆分。
- 排除项：`wpca-wordpress-stubs.php`（函数桩集合）、`wp-clean-admin.php`（引导主文件）应在任务清单中显式列为排除项，不计入达标统计。
- 标准拆分：主类 `require_once` 引入 trait 文件 + `use XxxTasks;`，公开 API（单例与方法签名）保持不变。

```php
// 主类
require_once __DIR__ . '/class-wpca-dashboard-widgets-tasks.php';
class Dashboard {
    use DashboardWidgetsTasks;
    // 仅保留单例、构造、init
}
```

- 当被拆分对象本身已是 trait 时，采用**组合 trait** 范式：拆为若干子 trait，原文件保留原文件名，内部只写 `require_once` + `use 子 trait;`，消费类零改动。
- trait 文件命名 `class-wpca-<模块>-<职责>-tasks.php`，命名空间与主类一致；跨 trait 调用的私有方法在组合后仍属同一类，可直接使用。
- 拆分后须在 `CHANGELOG.md` 记录行数变化，并确认公开 API 未变。

---

## 5. 提交信息格式

采用 Conventional Commits，格式：

```
<type>: <简短描述>

- <可选正文要点 1>
- <可选正文要点 2>
```

- `type` 取值：`feat`（新功能）、`fix`（bug 修复）、`docs`（文档）、`refactor`（重构）、`perf`（性能）、`style`（格式）、`test`（测试）、`chore`（构建/依赖/配置）、`ci`（CI）。
- 描述用简体中文或英文均可，但**同一仓库保持一致**；首行不超过 72 字符，不加句号。
- 正文要点用 `- ` 列表，说明「改了什么」「为什么」，避免粘贴 diff。
- 破坏性变更在正文首行标注 `BREAKING CHANGE: <说明>`，并提升 MAJOR 版本。
- 一次提交只做一件事；拆分、版本 bump、文档更新可合并为一次 `refactor` 提交，但不得与功能修复混在一起。
- 示例：`refactor: 拆分 Dashboard 类为 3 个 trait 并升级至 1.9.5`

---

## 6. 分支管理

- **`main` 是唯一长期分支**，始终保持可发布状态；`origin/main` 为唯一远端基线。
- 仓库**不存在 `develop` 分支**；任何文档/CI 中出现 `develop` 均应视为过时并修正。
- 短生命周期分支（按需创建，合并后删除）：

| 分支 | 用途 | 示例 |
|------|------|------|
| `feature/<简述>` | 新功能 | `feature/menu-export` |
| `fix/<简述>` | bug 修复 | `fix/ajax-nonce` |
| `docs/<简述>` | 文档批量更新 | `docs/dev-spec` |
| `refactor/<简述>` | 重构/拆分 | `refactor/split-dashboard` |
| `chore/<简述>` | 依赖、配置、版本 | `chore/bump-1.9.15` |

- 单人维护的日常小改动（含治理拆分）允许直接提交 `main`，但**每个提交必须可独立回滚**、必须通过 §7 自检清单、且提交后执行 `git push origin main` 同步远端。
- 涉及公开 API、数据库结构、安全策略或大规模目录调整的变更，**必须**走 PR 流程（§7）。
- 禁止强制推送 `main`；禁止在 `main` 上使用 `git reset --hard` 等破坏性操作。
- 归档 `archive/` 不参与任何分支的构建与测试。

---

## 7. 代码审查流程

### 7.1 提交前自检清单（必须逐项确认）

- [ ] `cd wpcleanadmin && composer test` 通过（本机无 PHP 运行时则在提交信息标注「待 CI/PHP 环境验证」）
- [ ] `composer phpcs` 无新增错误
- [ ] 全量 PHP 语法检查：`find wpcleanadmin -name '*.php' -print0 | xargs -0 -n1 php -l`
- [ ] 新增/修改的 PHP 文件 ≤ 200 行（排除项已登记）
- [ ] 所有新公开方法、类、trait 有 PHPDoc；文件头 `@version` 已更新
- [ ] 写操作具备 nonce + capability 双校验；输出已转义；SQL 已 `prepare`
- [ ] 文本域使用 `WPCA_TEXT_DOMAIN`，未新增硬编码 `'wp-clean-admin'`
- [ ] 未从 `archive/` 引用任何文件；未在 `wpcleanadmin/` 放入非运行时文件
- [ ] 版本号、`CHANGELOG.md`、README 版本标注一致（见 §9）
- [ ] 提交信息符合 §5 格式；原型/文档与代码同步更新

### 7.2 PR 流程

1. 从 `main` 创建短分支，按 §5 提交。
2. 推送分支并创建 PR，**标题遵循 `<type>: <描述>`**，填写 `.github/PULL_REQUEST_TEMPLATE.md`（变更总结、动机、类型、测试说明）。
3. CI 自动运行 `.github/workflows/ci.yml`：`php-lint`（PHP 8.1）、`phpcs`、`phpunit`（PHP 7.4/8.0/8.1/8.2/8.3 矩阵）；`main` 上全部 job 成功后由 `.github/workflows/release.yml` 自动发布 Release（见 §9.4）。
   > 当前 `phpcs` 步骤为 `continue-on-error: true`（非阻塞）；该状态属技术债，已在 `docs/tasks.md` 登记整改，整改前**审查者必须手动确认 phpcs 输出**。
4. 审查者核对 §7.1 清单，重点关注：公开 API 兼容性、安全基线、与 `openspec/specs/` 对应功能规范的一致性、文档同步。
5. 至少一名维护者批准后合并到 `main`；合并后删除来源分支并 `git push origin main`（若为直推则跳过 PR 步骤）。
6. 功能变更需在 `CHANGELOG.md` 追加条目；破坏性变更须同步 `openspec/` 规范并提升 MAJOR。

### 7.3 审查要点（Reviewer）

- 拒绝：从 `archive/` 复制代码回运行时、类名含下划线、单文件 >200 行且未登记排除、缺少 nonce/权限校验、硬编码文本域、文档与代码版本不一致。
- 要求：拆分保持公开 API 不变；组合 trait 保持消费类零改动；新增 HTML 元素带语义化 `id`。

---

## 8. 测试规范

- 依赖安装在**插件子目录**执行：`cd wpcleanadmin && composer install`。
- 命令：`composer test`（PHPUnit）、`composer phpcs`（WPCS）、`composer phpcbf`（自动修复格式）。
- 测试文件：`wpcleanadmin/tests/*Test.php`，bootstrap 为 `tests/bootstrap.php`；类名与文件同名，方法名 `test_<行为>`。
- 新增业务模块须配套单元测试；AJAX 处理器须覆盖「无 nonce / 错 nonce / 无权限 / 有权限」四态。
- 覆盖率配置见 `phpunit.xml.dist`；CI 的 PHP 矩阵见 §7.2。
- 本机无 PHP/Composer 运行时时，不得伪造「测试通过」；应在提交信息与 `CHANGELOG.md` 标注「待 CI/PHP 环境验证」，并依靠 CI 结果确认。

---

## 9. 版本管理与发布流程

### 9.1 单一来源

版本号唯一来源是 `wpcleanadmin/wp-clean-admin.php` 的插件头 `Version:` 与紧随其后的 `define('WPCA_VERSION', ...)`。两处必须始终相同。**禁止**在其它文件中硬编码版本数字作为回退值。

### 9.2 变更时须同步的文件

| 文件 | 字段 |
|------|------|
| `wpcleanadmin/wp-clean-admin.php` | `Version:` 头 + `WPCA_VERSION` 常量 |
| `wpcleanadmin/readme.txt` | `Stable tag`、`== Changelog ==`、`Upgrade Notice` |
| `README.md` / `README.en.md` | 首段版本标注 |
| `CHANGELOG.md` | 顶部新增版本小节 |
| `prototype/index.html` | 顶部注释版本 + 页脚 `版本 X` |
| `AGENTS.md` | §3 项目概述版本 |
| `wpcleanadmin/languages/*.pot` / `*.po` | `Project-Id-Version` |
| `CONTRIBUTING.md` / `docs/README.md` / `AGENTS.md` / `docs/tasks.md` | 首部「适用基线」 |
| 本次改动的所有 PHP 文件 | 文件头 `@version` |

### 9.3 版本号语义

- **MAJOR**：不兼容的公开 API 变更、目录/架构破坏性调整。
- **MINOR**：向后兼容的新功能。
- **PATCH**：向后兼容的 bug 修复、重构拆分、依赖与配置调整。
- **纯文档/规范变更不提升插件版本**，仅在 `CHANGELOG.md` 的 `[Unreleased]` 段记录（避免运行时版本无意义跳动）。
- 根目录 `update_versions.php` / `update_versions.ps1` 硬编码 `1.8.0 → 1.8.1`，**已废弃，禁止使用**；版本更新按 §9.2 清单执行。

### 9.4 发布流程（CI 全绿自动发布）

**发布由 `.github/workflows/release.yml` 自动完成**：`workflow_run` 监听 `CI` 工作流，当其在 `main` 分支上**全部 job 结论为 success** 时自动触发。流程为：

1. 读取插件头 `Version:` 作为发布版本（读取失败即失败，不发布）。
2. 幂等检查：tag `v<版本>` 已存在则跳过（同版本不重复发布）。
3. `git archive` 打包 `wpcleanadmin/` 为 `wp-clean-admin-v<版本>.zip`（目录重命名为 `wp-clean-admin`；`.gitattributes` 的 `export-ignore` 自动排除 `tests/`、`.github/`、`docs/`、`openspec/`、`prototype/`、`archive/`、`composer.*` 等非运行时文件）。
4. 用 `gh release create v<版本> --generate-notes` 创建 GitHub Release 并上传 zip。

人工职责（发布前完成，无需手动打 tag / 传包）：

1. 按 §9.2 bump 版本并更新 `CHANGELOG.md`（将 `[Unreleased]` 归入新版本小节）。
2. 运行 §7.1 全部自检项。
3. 推送 `main`，等待 CI 全绿——**CI 失败则不会发布**（`phpcs` 当前非阻塞，不影响发布门禁；整改后应纳入）。
4. 生成翻译模板（`wp i18n make-pot`）并补齐 `zh_CN` 翻译（在 bump 同一提交内完成）。
5. 在 GitHub Release 页面核对自动生成的发布说明与资产，必要时编辑补充。

> 新增其他工作流时，若其结论也应作为发布门禁，需扩展 `release.yml` 的 `workflow_run` 监听列表或改用合并门禁 job；仅监听 `CI` 是当前约定。

---

## 10. 术语表（统一用语）

| 统一术语 | 含义 | 禁用/过时说法 |
|----------|------|---------------|
| 权威体系 / `modules/` 体系 | `WPCleanAdmin\Modules\*` 运行期唯一实现 | 「新模块化架构（推荐）」「双体系副本」 |
| 归档层 / `archive/` | 1.9.12 归档的 legacy 死代码，只读 | 「旧架构类文件（向后兼容）」「传统层仍可用」 |
| 任务 trait | `*-tasks.php` 中按职责抽离的 trait | 「模块」「子模块」 |
| 组合 trait | 原 trait 保留文件名、内部 `use` 子 trait 的范式 | 「二次拆分」「trait 套 trait」 |
| 治理拆分 | 单文件 >200 行按职责拆 trait 的工作 | 「重构（泛指）」 |
| 原型 | `prototype/` 下 4 个静态文件 | 「`prototype/ui/`、`prototype/php/`」「高保真版/旧版双入口」 |
| 引导主文件 | `wpcleanadmin/wp-clean-admin.php` | 「核心类 Core」 |
| 版本源 | 插件头 `Version:` + `WPCA_VERSION` | 「各文件各自维护版本号」 |

---

## 11. 附录：快速索引

| 我想… | 看这里 |
|-------|--------|
| 搭环境、跑命令、预览原型 | `DEVELOPMENT.md` |
| 查目录边界与 AI 助手流程 | `AGENTS.md` |
| 查文档索引与格式约定 | `docs/README.md` |
| 查当前任务与已知技术债 | `docs/tasks.md` |
| 查 UI 设计令牌与组件 | `docs/设计规范_20260808.md`（历史基线，结构以代码为准） |
| 查功能需求规范 | `openspec/specs/<功能>/spec.md` |
| 查版本变更 | `CHANGELOG.md` |
