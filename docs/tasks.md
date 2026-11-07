# WP Clean Admin 开发任务清单（进行中）

> **适用基线**：1.9.15　|　**最近更新**：2026-11-07　|　**维护**：Tanox
> 本文仅保留**进行中 / 待办**任务；已完成任务归档至 [`tasks-done.md`](tasks-done.md)。
> 代码治理拆分规则、文档格式与版本同步清单见根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) §4.5 / §9.2，术语见 §10，文档索引见 [`docs/README.md`](README.md)。

---

## 一、当前状态概览

| 维度 | 状态 |
|------|------|
| 版本 | **1.9.15**（插件头 `Version:` 与 `WPCA_VERSION` 一致） |
| 架构 | `WPCleanAdmin\Modules\*` 为运行期唯一权威体系；根 `includes/` 仅余 9 个活文件，legacy 死代码（115 个）已归档至 `archive/legacy-includes-1.9.12/` |
| 代码治理（>200 行拆分） | 1.9.14 已拆分 `modules/` Helpers 与 Cache；当前仍有 **15 个 `modules/**` 文件 >200 行**（A-3，实测 2026-11-07）；排除项：函数桩与引导主文件 |
| 渲染类内联资源 | E-2 未闭环：活设置页 `modules/admin/settings/` 仍内联输出 CSS/JS，需重做于 modules 活代码 |
| 文档与规范 | 已建立权威体系：`CONTRIBUTING.md`（规范）+ `docs/README.md`（索引）；版本号已统一至 1.9.15；发布为 CI 全绿后自动发布 Release（`release.yml`，见 `CONTRIBUTING.md` §9.4） |
| 功能增强提案 | 16 任务全待办（`openspec/changes/规范改进与功能增强计划/`） |
| 原型提案 | 剩归档 2 项 |

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件

#### A-1. 根 `includes/` 业务类（已完成，1.9.13）

根 `includes/` 旧类为死代码，115 个文件已归档至 `archive/legacy-includes-1.9.12/`，仅保留 9 个活文件（autoload / 核心函数库 5 个 / 函数桩 / `ajax/settings-ajax.php` / `class-wpca-helpers.php`）。详见 `docs/tasks-done.md`。

#### A-2. 渲染 / 任务 trait（7 项，已完成 1.9.11–1.9.12）

拆分范式：原 trait 改为「组合 trait」（`use` 两个子 trait），消费类零改动，公开 API 不变。详见 `docs/tasks-done.md`。

#### A-3. 活体系拆分（`modules/**`，15 个，待办）

> 实测口径：2026-11-07 按 `wpcleanadmin/` 全量 PHP 文件行数统计，已排除 `wpca-wordpress-stubs.php`（函数桩）与 `wp-clean-admin.php`（引导主文件）。
> 范式见 `CONTRIBUTING.md` §4.5：主类 `require_once` + `use` 任务 trait；若原文件本身是 trait，改用组合 trait，消费类零改动。

| 文件（`wpcleanadmin/includes/modules/` 下） | 行数 |
|------|------|
| `utilities/classes/class-wpca-resources.php` | 356 |
| `admin/classes/class-wpca-menu-customizer.php` | 354 |
| `admin/classes/class-wpca-login.php` | 309 |
| `admin/classes/class-wpca-menu-manager.php` | 283 |
| `admin/classes/class-wpca-user-roles-data.php` | 257 |
| `admin/settings/class-wpca-settings-styles.php` | 250 |
| `admin/classes/class-wpca-login-two-factor.php` | 241 |
| `core/classes/class-wpca-error-handler.php` | 231 |
| `admin/classes/class-wpca-dashboard.php` | 222 |
| `admin/classes/class-wpca-menu-customizer-tree.php` | 215 |
| `admin/settings/class-wpca-settings.php` | 212 |
| `admin/classes/class-wpca-login-style.php` | 211 |
| `admin/settings/class-wpca-settings-field-renderers.php` | 207 |
| `admin/classes/class-wpca-user-roles.php` | 206 |
| `admin/classes/class-wpca-login-captcha.php` | 201 |

- [ ] 按上表逐个拆分（每个文件一个提交，附带行数变化与公开 API 不变说明）
- [x] `modules/utilities/classes/class-wpca-helpers.php`、`class-wpca-cache.php` — 1.9.14 已拆分，见 `tasks-done.md`

#### A-4. 排除项（不拆分，已登记）

- [x] `wpca-wordpress-stubs.php` — CLI/PHPUnit 函数桩集合
- [x] `wp-clean-admin.php` — 引导主文件

---

### B. 功能增强提案（T001–T016，全部待办）

来源：`openspec/changes/规范改进与功能增强计划/`（需单独规划，非代码拆分范畴）

| 阶段 | 任务 | 状态 |
|------|------|------|
| 一（基础规范） | T001 错误处理规范 / T002 性能监控规范 / T003 安全审计清单 / T004 开发者指南 | 待办 |
| 二（功能模块规范） | T005 Elementor / T006 主题模板 / T007 Composer / T008 扩展 API 规范 | 待办 |
| 三（开发工具集成） | T009 PHPStan / T010 PHPCS / T011 自动化测试 / T012 CI/CD | 待办 |
| 四（文档与测试） | T013 用户文档 / T014 API 文档 / T015 视频教程 / T016 示例代码 | 待办 |

> 注：T004 开发者指南、T013 用户文档、T003 安全审计清单的基础部分已由 `CONTRIBUTING.md` / `README.md` / `docs/README.md` 覆盖，提案收尾时应先做差距复核，避免重复建设。

### C. plugin-prototype 收尾（2 项）

- [ ] 将设计规范归档至 `openspec/specs/plugin-architecture/`
- [ ] 校准提案中的 `1.8.3` 基线表述（当前 1.9.15，以根 `CHANGELOG.md` 为准）

### D. 测试与静态分析（部分阻塞：无 PHP 运行时）

- [ ] 单元测试覆盖率达 80%+（提案目标）
- [ ] 集成 PHPStan / Psalm / Rector（T009–T010、T012）
- [ ] 在有 PHP 环境实跑 `composer test` 验证（当前环境无 php/composer）
- [ ] 将 CI 中 `phpcs` 步骤的 `continue-on-error: true` 改为阻塞门禁，并补齐 PHP 7.4 语法 lint、WordPress 版本矩阵、发布打包与 pot 同步校验（见 `CONTRIBUTING.md` §7.2）

### E. 架构决策项

- **E-1 双体系去重策略（已定，1.9.13）**：`modules/` 为运行期唯一权威体系，根 `includes/` 旧类归档为只读死代码（115 文件），不再合并。已解锁 A-3。
- **E-2 渲染内联资源抽离（未闭环）**：1.9.12 的抽离作用于已归档的死根代码（`Settings_Scripts`），活设置页 `modules/admin/settings/class-wpca-settings-scripts.php` 仍内联输出 `<script>`、CSS 经 `Settings_Styles` 内联；需在 modules 活代码重做方为有效。

---

## 三、优先级建议（更新于 2026-11-07）

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | A-3 | 拆分 `modules/**` 15 个 >200 行文件（组合 trait 范式，公开 API 不变） |
| P1 | D + E-2 | 建立阻塞式 CI 门禁与 PHP 运行时验证；重做活设置页资源抽离 |
| P2 | B、C | 功能增强提案 16 任务（先复核与现有文档的差距）；原型归档 |

---

## 四、统计摘要（2026-11-07）

- `>200` 行 PHP 文件：**15 个待办**（全部为 `modules/**` 活文件）+ 2 个登记排除项（函数桩、引导主文件）
- 已完成代码拆分批次：见 `docs/tasks-done.md`
- 功能增强提案待办：**16**
- 原型提案剩余：**2**

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.9.15 | 本次 | 新增自动发布工作流 `release.yml`（CI 全绿自动发布 Release）并更新发布规范（`CONTRIBUTING.md` §9.4） |
| 1.9.14 | `5fd486d` | 拆分 `modules/` Helpers 与 Cache 为 trait |
| 1.9.14 | 本次 | 文档治理：建立 `CONTRIBUTING.md` / `docs/README.md`，重写 `DEVELOPMENT.md` / `AGENTS.md`，统一版本号与历史文档标注（纯文档变更，不提升插件版本） |
| 1.9.13 | `42fc534` | E-1 已定：归档根 `includes/` 115 个死代码文件至 `archive/legacy-includes-1.9.12/`（modules 权威）；清理 E-2 孤立资源 4 个；更正 E-2 描述 |
| 1.9.12 | `b2dee36` | E-2 抽离（作用于根死代码）：menu-customization / settings-page-render 内联 CSS/JS 抽离至 assets |
| 1.9.11 | `c01b5ee` | A-2 渲染/任务 trait 再拆为子 trait（6 个，消费类零改动）；迁移 `TASKS.md` → `docs/` |
| 1.9.10 | `ea1391f` | core-functions（380→14）拆 4 文件；menu-manager 角色限制再抽离 |

---

*本清单为过程性文档，随迭代更新；版本号变更见根 `CHANGELOG.md`。已完成项见 `docs/tasks-done.md`。*
