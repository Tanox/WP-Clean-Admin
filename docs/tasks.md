# WP Clean Admin 开发任务清单（进行中）

> **适用基线**：1.9.17　|　**最近更新**：2026-11-08　|　**维护**：Tanox
> 本文仅保留**进行中 / 待办**任务；已完成任务归档至 [`tasks-done.md`](tasks-done.md)。
> 代码治理拆分规则、文档格式与版本同步清单见根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) §4.5 / §9.2，术语见 §10，文档索引见 [`docs/README.md`](README.md)。

---

## 一、当前状态概览

| 维度 | 状态 |
|------|------|
| 版本 | **1.9.17**（插件头 `Version:` 与 `WPCA_VERSION` 一致） |
| 架构 | `WPCleanAdmin\Modules\*` 为运行期唯一权威体系；根 `includes/` 仅余 9 个活文件，legacy 死代码（115 个）已归档至 `archive/legacy-includes-1.9.12/` |
| 代码治理（>200 行拆分） | 1.9.14 已拆分 `modules/` Helpers 与 Cache；**A-3 已收官（1.9.16）**：15 个 >200 行活文件全部按组合 trait 范式拆分；实测全仓仅余 2 个登记排除项（函数桩 774、引导主文件 236） |
| 渲染类内联资源 | E-2 已闭环（1.9.17）：活设置页内联资源抽离至 `assets/`（CSS 恢复 `wpca-settings.css`、JS 抽离 `wpca-settings.js`，由 `Settings_Styles`/`Settings_Scripts` include 注入，消除 40 行内联 JS） |
| 文档与规范 | 已建立权威体系：`CONTRIBUTING.md`（规范）+ `docs/README.md`（索引）；版本号已统一至 1.9.17；发布为 CI 全绿后自动发布 Release（`release.yml`，见 `CONTRIBUTING.md` §9.4） |
| 功能增强提案 | 16 任务全待办（`openspec/changes/规范改进与功能增强计划/`） |
| 原型提案 | 剩归档 2 项 |

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件

#### A-1~A-4. 代码治理拆分（全部完成，已归档至 `tasks-done.md`）

- A-1 根 `includes/` 死代码归档（115 文件，1.9.13）、A-2 渲染/任务 trait（1.9.11–1.9.12）、A-3 活体系 `modules/**` 拆分（15 文件，1.9.16）、A-4 排除项（函数桩/引导主文件）均已完成。
- 详细行数变化、提交与拆分前基线见 `docs/tasks-done.md`。



---

### B. 功能增强提案（T001–T016，全部待办）

来源：`openspec/changes/规范改进与功能增强计划/`（**需单独规划**：T001–T016 为 9 周/4 阶段独立规范与文档工程，含视频教程、示例代码等，超出本轮代码治理范畴；收尾应先做差距复核避免与 `CONTRIBUTING.md` 等现有文档重复）

| 阶段 | 任务 | 状态 |
|------|------|------|
| 一（基础规范） | T001 错误处理规范 / T002 性能监控规范 / T003 安全审计清单 / T004 开发者指南 | 待办 |
| 二（功能模块规范） | T005 Elementor / T006 主题模板 / T007 Composer / T008 扩展 API 规范 | 待办 |
| 三（开发工具集成） | T009 PHPStan / T010 PHPCS / T011 自动化测试 / T012 CI/CD | 待办 |
| 四（文档与测试） | T013 用户文档 / T014 API 文档 / T015 视频教程 / T016 示例代码 | 待办 |

> 注：T004 开发者指南、T013 用户文档、T003 安全审计清单的基础部分已由 `CONTRIBUTING.md` / `README.md` / `docs/README.md` 覆盖，提案收尾时应先做差距复核，避免重复建设。

### C. plugin-prototype 收尾（2 项）

- [x] 将设计规范归档至 `openspec/specs/plugin-architecture/`（已在 `design.md` 确立为当前权威规范）
- [x] 校准提案中的 `1.8.3` 基线表述（提案均为 1.8.3 历史基线文档，已标注「不代表当前状态」；当前版本以根 `CHANGELOG.md` 1.9.17 为准）

### D. 测试与静态分析（部分阻塞：无 PHP 运行时）

- [ ] 单元测试覆盖率达 80%+（提案目标）
- [ ] 集成 PHPStan / Psalm / Rector（T009–T010、T012）
- [ ] 在有 PHP 环境实跑 `composer test` 验证（当前环境无 php/composer）
- [x] CI `php-lint` 增加 PHP 7.4 语法检查矩阵（§7.2，复盖最低支持版本）
- [ ] 将 CI 中 `phpcs` 步骤的 `continue-on-error: true` 改为阻塞门禁（遵循既定「规则集调优前不阻塞」，避免破坏 release 自动发布；待 PHP 环境适配规则集后启用）
- [ ] 补齐 WordPress 版本矩阵、发布打包与 pot 同步校验（§7.2，待 PHP 环境）

### E. 架构决策项

- **E-1 双体系去重策略（已定，1.9.13）**：`modules/` 为运行期唯一权威体系，根 `includes/` 旧类归档为只读死代码（115 文件），不再合并。已解锁 A-3。
- **E-2 渲染内联资源抽离（已闭环，1.9.17）**：活设置页 `modules/admin/settings` 内联资源已抽离至 `assets/`（CSS 恢复 `wpca-settings.css`、JS 抽离 `wpca-settings.js`），由 `Settings_Styles`/`Settings_Scripts` include 注入，消除 40 行内联 JS

---

## 三、优先级建议（更新于 2026-11-08）

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | D | CI 阻塞门禁与测试治理（`phpcs` 阻塞化、单元测试覆盖率、`composer test` 实跑均阻塞于无 PHP 环境；`php-lint` 已覆盖 7.4） |
| P1 | B | 功能增强提案 T001–T016（独立规范/文档/工具工程，需单独规划；先做差距复核避免与现有文档重复） |
| P2 | 无（C、E-2 已于 1.9.17 完成） | C plugin-prototype 收尾、E-2 内联资源抽离均已完成 |

---

## 四、统计摘要（2026-11-08）

- `>200` 行 PHP 文件：**0 个待办**（A-3 于 1.9.16 收官）+ 2 个登记排除项（函数桩 774、引导主文件 236）
- 渲染内联资源（E-2）：**0 待办**（1.9.17 闭环，资源抽离至 `assets/`）
- 已完成代码拆分批次：见 `docs/tasks-done.md`
- 功能增强提案待办：**16**（B，需单独规划）
- 原型提案剩余：**0**（C 已于 1.9.17 完成）

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.9.17 | 本次 | E-2 闭环（恢复 assets CSS + 抽离 JS，消除 settings-scripts 内联）；CI 增加 PHP 7.4 语法 lint；版本号同步至 1.9.17 |
| 1.9.16 | 上次 | A-3 收官：拆分 `modules/**` 剩余 15 个 >200 行活文件（新增 30 trait、改写 14 消费类），全仓 >200 行仅余 2 个排除项；版本号同步至 1.9.16 |
| 1.9.15 | 上次 | 新增自动发布工作流 `release.yml`（CI 全绿自动发布 Release）并更新发布规范（`CONTRIBUTING.md` §9.4） |
| 1.9.14 | `5fd486d` | 拆分 `modules/` Helpers 与 Cache 为 trait |
| 1.9.14 | 本次 | 文档治理：建立 `CONTRIBUTING.md` / `docs/README.md`，重写 `DEVELOPMENT.md` / `AGENTS.md`，统一版本号与历史文档标注（纯文档变更，不提升插件版本） |
| 1.9.13 | `42fc534` | E-1 已定：归档根 `includes/` 115 个死代码文件至 `archive/legacy-includes-1.9.12/`（modules 权威）；清理 E-2 孤立资源 4 个；更正 E-2 描述 |
| 1.9.12 | `b2dee36` | E-2 抽离（作用于根死代码）：menu-customization / settings-page-render 内联 CSS/JS 抽离至 assets |
| 1.9.11 | `c01b5ee` | A-2 渲染/任务 trait 再拆为子 trait（6 个，消费类零改动）；迁移 `TASKS.md` → `docs/` |
| 1.9.10 | `ea1391f` | core-functions（380→14）拆 4 文件；menu-manager 角色限制再抽离 |

---

*本清单为过程性文档，随迭代更新；版本号变更见根 `CHANGELOG.md`。已完成项见 `docs/tasks-done.md`。*
