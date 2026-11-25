# 文档索引与编写约定

> **适用基线**：1.9.18　|　**更新日期**：2026-11-25
> 本文是 `docs/` 目录的索引与文档元规范。开发规范（编码、命名、提交、分支、审查）以根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) 为唯一权威来源，本文不重复其条文。
> 每份现行文档均含目录、适用基线、状态与文末版本控制表；历史快照除外。

---

## 1. 文档地图

### 1.1 根目录（权威文档）

| 文件 | 读者 | 职责 | 状态 |
|------|------|------|------|
| [`CONTRIBUTING.md`](../CONTRIBUTING.md) | 贡献者 / AI 助手 | **开发规范唯一来源**：编码风格、命名约定、目录组织、提交格式、分支管理、代码审查、测试、版本与发布 | 权威 |
| [`README.md`](../README.md) | 用户 | 功能介绍、安装、使用说明、FAQ | 用户文档 |
| [`README.en.md`](../README.en.md) | 英文用户 | `README.md` 的英文镜像，功能列表与版本号须同步 | 用户文档 |
| [`DEVELOPMENT.md`](../DEVELOPMENT.md) | 开发者 | 环境搭建、常用命令、原型预览（快速参考） | 快速参考 |
| [`AGENTS.md`](../AGENTS.md) | AI 助手 | 工作指南、目录边界、OpenSpec 变更流程 | 助手指令 |
| [`CHANGELOG.md`](../CHANGELOG.md) | 所有人 | **唯一版本变更历史来源** | 权威 |
| [`LICENSE`](../LICENSE) | 所有人 | GPLv2 或更高版本 | 权威 |

### 1.2 `docs/`（工程文档）

| 文件 | 职责 | 说明 |
|------|------|------|
| `README.md`（本文） | 文档索引、编写格式、术语与状态标注规则 | 元规范 |
| `README.en.md` | 本文的英文镜像（索引、格式约定与术语表） | 元规范（英） |
| `tasks.md` | 进行中 / 待办任务清单 | 仅保留未完成项 |
| `tasks-done.md` | 已完成任务归档 | 只增不改，保持主清单精简 |
| [`项目概述.md`](项目概述.md) / [`overview.en.md`](overview.en.md) | 背景、目标、技术栈选型、架构设计与目录速览 | 现行文档（中/英） |
| [`安装部署指南.md`](安装部署指南.md) / [`installation-guide.en.md`](installation-guide.en.md) | 环境依赖、安装配置、多环境方案与验证方法 | 现行文档（中/英） |
| [`功能模块说明.md`](功能模块说明.md) | 按模块的核心功能、业务流程图与关键逻辑 | 现行文档 |
| [`API接口文档.md`](API接口文档.md) / [`openapi.yaml`](openapi.yaml) | HTTP 接口：路径/方法/参数/响应/错误码/示例；OpenAPI 3.1 机器可读契约 | 现行文档 |
| [`user-guide.md`](user-guide.md) / [`user-guide.en.md`](user-guide.en.md) | 角色权限、操作流程图与详细步骤（含 T013 内容） | 现行文档（中/英） |
| [`FAQ与故障排除.md`](FAQ与故障排除.md) / [`faq-troubleshooting.en.md`](faq-troubleshooting.en.md) | 常见问题、排障矩阵、诊断方法与紧急恢复 | 现行文档（中/英） |
| [`优化建议.md`](优化建议.md) | 代码/架构/性能/UX 评估结论与 12 项优化建议（背景/方案/效果/优先级/实施状态） | 评估结论（12 项已于 1.9.18 实施） |
| [`api-reference.md`](api-reference.md) / [`api-reference.en.md`](api-reference.en.md) | 全局函数、过滤器、选项与核心类速查（T014） | 快速参考；HTTP 契约以上方 OpenAPI 文档为准 |
| [`临时文件管理规范.md`](临时文件管理规范.md) | 仓库临时文件目录、命名与清理约定 | 快速参考 |

### 1.3 其它目录

| 目录 | 内容 | 规则 |
|------|------|------|
| [`openspec/`](../openspec/) | 功能规范 `specs/`、变更提案 `changes/`、归档 `archive/`、`project.md`、`AGENTS.md` | 规范/提案文档须带 `OPENSPEC:START/END` 标记 |
| [`prototype/`](../prototype/) | 纯静态原型 4 文件：`index.html`、`app.js`、`wpca-admin.css`、`wpca-components.css` | 无 PHP、无子目录、不参与运行时 |
| [`archive/`](../archive/) | 1.9.12 归档的 legacy 死代码（`legacy-includes-1.9.12/`） | **只读**，禁止引用、修改、复制回插件 |
| [`.github/`](../.github/) | CI、PR/Issue 模板、`SECURITY.md`、`SUPPORT.md` | 流程配置 |

> 目录边界与「插件目录只放运行时代码」的硬约束见 `AGENTS.md` §2 与 `CONTRIBUTING.md` §2。

### 1.4 推荐阅读路径

| 读者 | 路径 |
|------|------|
| 站点管理员 | [`用户操作指南`](user-guide.md) → [`安装部署指南`](安装部署指南.md) → [`FAQ与故障排除`](FAQ与故障排除.md) |
| 开发者 / 扩展作者 | [`项目概述`](项目概述.md) → [`功能模块说明`](功能模块说明.md) → [`API接口文档`](API接口文档.md) + [`openapi.yaml`](openapi.yaml) |
| 贡献者 / AI 助手 | 根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) → 本文 → [`tasks.md`](tasks.md) → [`优化建议.md`](优化建议.md) |
| 英文读者 | [`README.en.md`](README.en.md) → [`user-guide.en.md`](user-guide.en.md) → [`installation-guide.en.md`](installation-guide.en.md) → [`faq-troubleshooting.en.md`](faq-troubleshooting.en.md) → [`api-reference.en.md`](api-reference.en.md) → [`overview.en.md`](overview.en.md) |

---

## 2. 文档编写格式（统一）

1. **标题**：ATX（`#`），章节连续编号，**同一文档内不得重复编号**；`##` 为一级章节，`###` 为子节。
2. **语言**：简体中文为主；英文文档采用 `<原文件名>.en.md` 命名（如 `user-guide.en.md`），与中文原文逐节对应。技术标识符（类名、函数名、路径、命令）使用行内代码反引号。
3. **路径引用**：使用仓库根相对路径，写成 `` `wpcleanadmin/includes/autoload.php:46` `` 形式（路径:行号）。禁止引用已不存在的路径（如 `prototype/ui/`、`prototype/php/`）。
4. **表格**：用于清单、映射、状态对照；表头与分隔行齐全，状态值统一为 `待办` / `进行中` / `已完成` / `阻塞`。
5. **任务清单**：使用 `- [ ]` / `- [x]`；`docs/tasks.md` 只放未完成项，完成项移入 `docs/tasks-done.md`。
6. **代码块**：必须标注语言（`php`、`bash`、`json`、`text`）；命令默认在仓库根执行，若需在 `wpcleanadmin/` 执行须显式写出 `cd wpcleanadmin`。
7. **OPENSPEC 标记**：仅 `openspec/` 目录下的规范与提案文档使用 `<!-- OPENSPEC:START -->` / `<!-- OPENSPEC:END -->`；根目录与 `docs/` 文档**不使用**。
8. **文件命名**：带日期的快照用 `名称_YYYYMMDD.md`；稳定文档用固定名。历史文件保留原名不重命名。
9. **编码**：UTF-8 无 BOM，行尾 LF，末尾保留换行。

---

## 3. 版本与状态标注（必须）

- 每份文档首部用引用块（`>`）标注：`适用基线`（当前插件版本）、`更新日期`、必要时加 `状态`（权威 / 历史快照 / 进行中）。
- 版本号必须与 `wpcleanadmin/wp-clean-admin.php` 的 `Version:` 一致；**禁止**在文档中写死旧版本作为「当前版本」。
- 历史文档中出现旧版本号（如 1.8.3）时，必须同时标注「历史基线快照，不代表当前状态」，并在 §1 索引中被标为历史快照。
- 提及具体提交时写短哈希（如 `5fd486d`）；版本变更历史只写入 `CHANGELOG.md`，其它文档引用而不复制。
- 状态术语统一：权威、快速参考、用户文档、助手指令、历史快照、进行中、待办、已完成、阻塞、只读。

---

## 4. 术语一致性

完整术语表见 `CONTRIBUTING.md` §10。摘要（左侧为统一用语，右侧为禁止使用的过时说法）：

| 统一用语 | 禁止 / 过时说法 |
|----------|-----------------|
| 权威体系（或 `modules/` 体系） | 新模块化架构（推荐使用）、双体系副本 |
| 归档层（或 `archive/`） | 旧架构类文件（向后兼容）、传统层 |
| 任务 trait / 组合 trait | 子模块、trait 套 trait |
| 治理拆分 | 泛指的「重构」 |
| 原型（4 个静态文件） | `prototype/ui/`、`prototype/php/`、双 UI 入口 |
| 引导主文件 | 核心类 `Core`（指 `wp-clean-admin.php` 时） |
| 版本源（插件头 `Version:` + `WPCA_VERSION`） | 各文件各自维护版本号 |

---

## 5. 文档更新同步清单

修改代码后，按下表检查文档是否需同步（详见 `CONTRIBUTING.md` §9.2）：

| 变更类型 | 必须更新的文档 |
|----------|----------------|
| 版本号变化 | 按 `CONTRIBUTING.md` §9.2 清单：`wp-clean-admin.php`、`readme.txt`、`README.md`/`README.en.md`、`CHANGELOG.md`、`prototype/index.html`、`AGENTS.md` §3、语言文件头、`CONTRIBUTING.md`/`docs/README.md`/`docs/tasks.md` 适用基线、改动 PHP 文件头 `@version` |
| 新增/变更英文镜像 | 对应中文原文的 `.en.md` 文件（含「适用基线」头与文末版本控制表）；新增中文文档时同步登记英文镜像状态（未翻译须在本文 §1.2 明确标注） |
| 目录结构变化 | `CONTRIBUTING.md` §2、`DEVELOPMENT.md`、`AGENTS.md` §2、本文 §1、`wpcleanadmin/includes/modules/README.md` |
| 新增/修改公开 API | `CHANGELOG.md`、对应 `openspec/specs/<功能>/spec.md` |
| 治理拆分完成 | `docs/tasks.md`（移出）→ `docs/tasks-done.md`（归档）、`CHANGELOG.md` |
| CI / 分支策略变化 | `.github/workflows/ci.yml`、`CONTRIBUTING.md` §6/§7、`docs/tasks.md` |
| 原型结构变化 | `prototype/index.html`、`DEVELOPMENT.md`、本文 §1.3 |

---

## 6. 已知历史偏差（本次治理已对齐）

| 位置 | 历史偏差 | 处置 |
|------|----------|------|
| `DEVELOPMENT.md` | 引用不存在的 `prototype/php/`、`prototype/ui/`、`USE_MOCK`、`Core::init_modules()` | 已按当前代码重写 |
| `AGENTS.md` | 两个 `## 3.`、§5.2 声称所有文档须带 OPENSPEC 标记、§7 模块表为已归档 legacy 类名 | 已修正 |
| `.github/PULL_REQUEST_TEMPLATE.md` | 引用当时不存在的 `CONTRIBUTING.md` | 已补建该文件 |
| `README.md` / `README.en.md` / `SECURITY.md` / `openspec/AGENTS.md` | 历史版本标注不一致（1.9.13 / 1.9.10 / 1.8.x / 1.8.5） | 2026-11-07 治理节点统一至 1.9.14，此后随版本继续同步（当前 1.9.18） |
| `wpcleanadmin/includes/modules/README.md` | 描述不存在的 `frontend/`、`admin/ajax/` 与 `Core::init_modules()` | 已按实际目录重写 |
| `.github/workflows/ci.yml` | 触发不存在的 `develop` 分支 | 已对齐为 `main` + PR |
| `docs/设计规范_20260808.md`、`docs/审计报告_20260808.md` | 基于 1.8.3 基线，描述已废弃的原型目录结构 | 已于 2026-11-25 删除（内容已失效且与现行原型不符），本索引同步移除引用 |

---

## 7. 文档版本控制

| 文档版本 | 适用插件版本 | 日期 | 维护人 | 变更摘要 |
|----------|--------------|------|--------|----------|
| 1.1 | 1.9.18 | 2026-11-25 | Tanox | 新增英文镜像章节与索引行（`README.en.md` 等 6 份）；移除已删除历史快照文档的索引与同步清单引用；登记 `临时文件管理规范.md`；`优化建议.md` 状态更新为已实施 |
| 1.0 | 1.9.17 | 2026-11-25 | Tanox | 首版：文档地图、编写格式、版本与状态标注、术语表、同步清单与历史偏差 |
