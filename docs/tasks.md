# WP Clean Admin 开发任务清单（进行中）

> **适用基线**：1.9.17　|　**最近更新**：2026-11-23　|　**维护**：Tanox
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
| 功能增强提案 | **15 完成 / 1 阻塞（T015 视频教程）**（2026-11-23 批量落地，见 `openspec/changes/规范改进与功能增强计划/tasks.md`） |
| 原型提案 | 已全部完成（归档至 `tasks-done.md`） |

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件

#### A-1~A-4. 代码治理拆分（全部完成，已归档至 `tasks-done.md`）

- A-1 根 `includes/` 死代码归档（115 文件，1.9.13）、A-2 渲染/任务 trait（1.9.11–1.9.12）、A-3 活体系 `modules/**` 拆分（15 文件，1.9.16）、A-4 排除项（函数桩/引导主文件）均已完成。
- 详细行数变化、提交与拆分前基线见 `docs/tasks-done.md`。



---

### B. 功能增强提案（T001–T016，15 完成 / 1 阻塞）

来源：`openspec/changes/规范改进与功能增强计划/`。2026-11-23 批量落地：先做差距复核（T003/T004 定位为现有文档的执行检查表与索引，不重复建设），随后交付 6 份规范（T001/T002/T003/T004/T006/T007）+ 3 份工具配置（T009 phpstan.neon.dist、T010 phpcs.xml.dist 既有、T011 phpunit 既有）+ 3 份文档/示例（T013 `docs/user-guide.md`、T014 `docs/api-reference.md`、T016 `examples/sample-extension.php`）。

| 阶段 | 任务 | 状态 |
|------|------|------|
| 一（基础规范） | T001 错误处理 / T002 性能监控 / T003 安全审计 / T004 开发者指南 | 已完成 |
| 二（功能模块规范） | T005 Elementor / T006 主题模板 / T007 Composer / T008 扩展 API | 已完成 |
| 三（开发工具集成） | T009 PHPStan / T010 PHPCS / T011 自动化测试 / T012 CI/CD | 配置完成；实跑验证阻塞于 PHP 环境（见 §D） |
| 四（文档与测试） | T013 用户文档 / T014 API 文档 / T015 视频教程 / T016 示例代码 | T013/T014/T016 已完成；**T015 阻塞**（需视频制作资源） |

> 注：T015 为唯一未完成项，需要录屏/剪辑工具链，非代码仓库可交付；待资源到位后在 `openspec/changes/规范改进与功能增强计划/tasks.md` 闭环。

### C. plugin-prototype 收尾（已完成，已归档至 `tasks-done.md`）

### D. 测试与静态分析（阻塞：无 PHP 运行时）

- [ ] 单元测试覆盖率达 80%+（提案目标；待 PHP 环境补测试用例后统计）
- [ ] 在有 PHP 环境实跑 `composer test` / `composer phpstan` / `composer phpcs` 验证（配置已就绪：phpunit.xml.dist / phpstan.neon.dist / phpcs.xml.dist）
- [ ] 将 CI 中 `phpcs` 步骤的 `continue-on-error: true` 改为阻塞门禁（遵循既定「规则集调优前不阻塞」，避免破坏 release 自动发布；待 PHP 环境适配规则集后启用）
- [ ] 补齐 WordPress 版本矩阵、发布打包与 pot 同步校验（§7.2，待 PHP 环境）
- [x] ~~CI `php-lint` 增加 PHP 7.4 语法检查矩阵~~（已完成，归档至 `tasks-done.md`）

### E. 架构决策项

- **E-1 双体系去重策略（已定，1.9.13）**：`modules/` 为运行期唯一权威体系，根 `includes/` 旧类归档为只读死代码（115 文件），不再合并。已解锁 A-3。
- **E-2 渲染内联资源抽离（已闭环，1.9.17）**：活设置页 `modules/admin/settings` 内联资源已抽离至 `assets/`（CSS 恢复 `wpca-settings.css`、JS 抽离 `wpca-settings.js`），由 `Settings_Styles`/`Settings_Scripts` include 注入，消除 40 行内联 JS

---

## 三、优先级建议（更新于 2026-11-23）

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | D | 测试与静态分析实跑验证（配置已全部就绪：phpunit / phpstan / phpcs；阻塞于无 PHP 环境；`phpcs` 阻塞门禁待规则集调优后启用） |
| P1 | B-T015 | 视频教程（需录屏/剪辑资源，唯一未完成项） |
| P2 | 无 | B 其余 15 项、C、E 均已完成 |

---

## 四、统计摘要（2026-11-23）

- `>200` 行 PHP 文件：**0 个待办**（A-3 于 1.9.16 收官）+ 2 个登记排除项（函数桩 774、引导主文件 236）
- 渲染内联资源（E-2）：**0 待办**（1.9.17 闭环，资源抽离至 `assets/`）
- 已完成代码拆分批次：见 `docs/tasks-done.md`
- 功能增强提案：**15 完成 / 1 阻塞**（T015 视频教程待资源）
- 原型提案剩余：**0**（C 已归档）

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.9.17 | 本次 | 功能增强提案批量落地（T001–T014/T016：6 规范 + 3 工具配置 + 3 文档/示例）；C 项与 D 已完成项归档；文档更新（纯文档变更，不提升插件版本） |
| 1.9.17 | 上次 | E-2 闭环（恢复 assets CSS + 抽离 JS，消除 settings-scripts 内联）；CI 增加 PHP 7.4 语法 lint；版本号同步至 1.9.17 |
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
