<!-- OPENSPEC:START -->
# WP Clean Admin AI 助手指令

> **适用基线**：1.9.16　|　**更新日期**：2026-11-07
> 本文档为 AI 助手提供工作指南与目录边界。**编码风格、命名约定、提交格式、分支管理、代码审查、测试与版本管理的完整规范以根 [`CONTRIBUTING.md`](CONTRIBUTING.md) 为唯一权威来源**；本文不重复其条文。

## 1. 何时参考本文件

- 涉及项目规划或提案（proposal / spec / change / plan）→ 同时打开 [`openspec/AGENTS.md`](openspec/AGENTS.md) 与对应 `openspec/specs/<功能>/spec.md`
- 引入新功能、破坏性变更、架构调整或重大性能/安全工作
- 需求模糊，需要权威规范作为参考
- 需要检查和整理 OpenSpec 文档
- 需要确认目录边界（哪些文件可以写进 `/wpcleanadmin`）

> 涉及编码与提交流程时，优先查阅 `CONTRIBUTING.md`；本文件与 `DEVELOPMENT.md` 仅提供流程与快速参考。

## 2. 目录边界约定（重要）

**`/wpcleanadmin` 为插件目录，只用于存放插件运行所需的代码文件**：

- `wp-clean-admin.php`（引导主文件）
- `includes/`（autoload、核心函数库、CLI 函数桩、`ajax/`、`modules/`）
- `assets/css/`、`assets/js/`
- `languages/`（.po / .mo / .pot）
- `tests/`、`composer.json`、`phpunit.xml.dist`、`phpcs.xml.dist`
- `readme.txt`、`uninstall.php`

**以下项目级文件禁止放入 `/wpcleanadmin`，统一放仓库根目录对应位置**：

| 内容 | 位置 |
|------|------|
| 原型（4 个静态文件，无 PHP、无子目录） | `prototype/`： `index.html`、`app.js`、`wpca-admin.css`、`wpca-components.css` |
| 工程文档与索引 | `docs/` |
| OpenSpec 规范与提案 | `openspec/` |
| 只读 legacy 归档 | `archive/`（`legacy-includes-1.9.12/`） |
| 项目级文档 | `README.md`、`README.en.md`、`CHANGELOG.md`、`DEVELOPMENT.md`、`CONTRIBUTING.md`、`AGENTS.md`、`LICENSE` |
| CI 与社区模板 | `.github/` |

> **规则**：非插件运行时必需文件一律不得写入 `/wpcleanadmin`；`archive/` 为只读归档，禁止 `require`、`use` 或复制回插件目录。

## 3. 项目概述

WP Clean Admin 是 WordPress 后台清理与优化插件，当前版本 **1.9.16**，最低 PHP 7.4 / WordPress 5.0，Text Domain `wp-clean-admin`。

- **运行期唯一权威体系**：`WPCleanAdmin\Modules\*`（`admin/` / `core/` / `utilities/`）
- **入口**：`wp-clean-admin.php` → `plugins_loaded` → `WPCleanAdmin\Modules\Core\Classes\Core` → `Module_Loader::safe_init()`
- **归档层**：`includes/` 根下的 legacy 类（115 个文件）已于 1.9.12 归档至 `archive/`，运行期不加载
- 目标：清理后台冗余菜单与功能、优化加载性能、增强安全性、提供菜单定制、简化数据库管理

## 4. OpenSpec 文档结构

```
openspec/
├── README.md              # 文档入口
├── project.md             # 项目上下文与技术栈约定
├── AGENTS.md              # OpenSpec 专用 AI 指令
├── CHANGELOG.md           # OpenSpec 文档自身变更记录
├── specs/                 # 功能规范（已构建内容）
│   ├── admin-cleanup/  api/  database-management/  diagnostics/
│   ├── menu-customization/  performance-optimization/
│   ├── permission-management/  plugin-architecture/  security-enhancement/
├── changes/               # 变更提案（待处理）
└── archive/               # 归档的旧提案与文档
```

> 版本变更历史以根 `CHANGELOG.md` 为唯一来源；`openspec/CHANGELOG.md` 仅记录 OpenSpec 文档自身的调整。

## 5. 工作流程

### 5.1 新增功能流程

1. 在 `openspec/changes/<变更名>/` 创建 `proposal.md`、`tasks.md`（必要时 `design.md` 与 `specs/` 增量）
2. 审批后在 `openspec/specs/<功能>/` 建立/更新规范
3. 按 `CONTRIBUTING.md` §2–§5 实施代码变更（目录、命名、拆分、提交格式）
4. 更新 `CHANGELOG.md`，必要时同步 `README.md` / `README.en.md` / `prototype/`
5. 归档变更提案至 `openspec/archive/`

### 5.2 修改现有功能流程

1. 查看相关功能的 `openspec/specs/` 规范
2. 按需更新规范文档
3. 实施代码变更并执行 `CONTRIBUTING.md` §7.1 自检清单
4. 更新 `CHANGELOG.md`

### 5.3 修复 Bug 流程

1. 定位模块（优先 `wpcleanadmin/includes/modules/`）
2. 查看对应规范
3. 修复并补充回归测试
4. 更新 `CHANGELOG.md`

## 6. 文档约定

### 6.1 命名与格式

- `openspec/specs/` 与 `changes/` 下目录使用小写字母和连字符
- 带日期的快照文档：`名称_YYYYMMDD.md`（如 `审计报告_20260808.md`）；历史文件保留原名不重命名
- **OPENSPEC 标记的适用范围**：仅 `openspec/` 目录下的规范与提案文档必须包含 `<!-- OPENSPEC:START -->` / `<!-- OPENSPEC:END -->`；根目录与 `docs/` 下文档不使用该标记
- 每份文档须在首部标注适用基线版本与更新日期；版本号必须与插件头 `Version:` 一致
- 章节编号连续，同一文档内不得重复编号；状态值统一为 `待办` / `进行中` / `已完成` / `阻塞`
- 完整格式约定见 [`docs/README.md`](docs/README.md)

### 6.2 版本管理

- MAJOR：不兼容的公开 API 变更；MINOR：向后兼容的新功能；PATCH：向后兼容的修复、重构拆分
- 版本号唯一来源：`wp-clean-admin.php` 的 `Version:` 与 `WPCA_VERSION`
- **纯文档/规范变更不提升插件版本**，仅写入 `CHANGELOG.md` 的 `[Unreleased]` 段
- 同步清单与发布步骤见 `CONTRIBUTING.md` §9

### 6.3 术语一致性

统一使用「权威体系 / `modules/` 体系」「归档层 / `archive/`」「任务 trait / 组合 trait」「治理拆分」「原型」「引导主文件」「版本源」；禁用「新模块化架构（推荐）」「双体系副本」「旧架构类文件（向后兼容）」等过时说法。完整术语表见 `CONTRIBUTING.md` §10。

## 7. 代码规范索引

以下规范**统一由 `CONTRIBUTING.md` 定义**，本文仅列索引：

| 主题 | 位置 |
|------|------|
| 编码风格、PHP 7.4 兼容、文件头、PHPDoc、安全基线 | `CONTRIBUTING.md` §4 |
| 命名约定、类名→文件名映射、语义化 ID | `CONTRIBUTING.md` §3 |
| 目录组织与硬约束 | `CONTRIBUTING.md` §2 / 本文 §2 |
| 单文件 >200 行拆分与组合 trait 范式 | `CONTRIBUTING.md` §4.5 |
| 提交信息格式 | `CONTRIBUTING.md` §5 |
| 分支管理 | `CONTRIBUTING.md` §6 |
| 代码审查流程与自检清单 | `CONTRIBUTING.md` §7 |
| 测试与 CI | `CONTRIBUTING.md` §8 |

**AI 助手执行要点**：

1. 拆分超 200 行文件用「主类 `require_once` + `use` 任务 trait」或「组合 trait」范式，公开 API 保持不变；`wpca-wordpress-stubs.php` 与引导主文件为登记在案的排除项。
2. 类名禁止下划线（autoload 会产生双连字符文件名导致加载失败）。
3. 本机无 PHP 运行时不得声称测试通过，须在提交信息标注「待 CI/PHP 环境验证」。
4. 提交后按项目约定执行 `git push origin main` 同步远端。
5. 不得修改或引用 `archive/` 内任何文件。

## 8. 核心功能域

下表按**权威体系的功能域**列出，具体类与状态以 `wpcleanadmin/includes/modules/core/classes/class-wpca-module-loader.php` 的实例化清单和 `docs/tasks.md` 为准。

| 功能域 | 位置 | 说明 |
|--------|------|------|
| 核心引导 / 错误处理 | `modules/core/classes/` | `Core`、`Module_Loader`、`Error_Handler` |
| 设置管理 | `modules/admin/settings/` | 设置页、字段渲染、样式、校验 |
| 后台功能 | `modules/admin/classes/` | 仪表盘、菜单管理、菜单定制、用户角色、登录相关 |
| 工具 | `modules/utilities/classes/` | 缓存、助手、资源、i18n |
| AJAX 处理器 | `includes/ajax/` | `WPCleanAdmin\AJAX` 命名空间 |
| 核心函数库 | `includes/wpca-core*-functions.php` | `wpca_get_settings()` 等全局函数 |

## 9. 重要链接

- GitHub: https://github.com/Tanox/WP-Clean-Admin
- 开发规范: [`CONTRIBUTING.md`](CONTRIBUTING.md)
- 文档索引: [`docs/README.md`](docs/README.md)
- 任务清单: [`docs/tasks.md`](docs/tasks.md)
- OpenSpec: https://github.com/Tanox/WP-Clean-Admin/tree/main/openspec
- WordPress.org: 待发布

## 10. 许可证

WP Clean Admin 遵循 GPLv2 或更高版本许可证（见 [`LICENSE`](LICENSE)）。

<!-- OPENSPEC:END -->
