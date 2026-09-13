# WP Clean Admin 开发任务清单

> 生成日期：2026-09-13 · 撰写基准版本：1.8.12 · 维护：Tanox

本文档汇总项目当前开发进度与剩余任务，作为迭代排期依据。代码治理拆分规则见 `AGENTS.md` 与全局用户规则（单文件 >200 行须拆分）。

---

## 一、当前状态概览

| 维度 | 状态 |
|------|------|
| 版本 | 1.8.12（已发布，CI 含 phpcs/phpunit/php-lint） |
| 功能模块 | AGENTS.md 列 22 模块，标注「稳定」 |
| 代码治理（>200 行拆分） | 试点完成 5 核心类（core/admin/settings/login/diagnostics 部分）；Settings 拆为注册 + 渲染 trait |
| CI | phpcs 外部标准注册已修复（1.8.12），php-lint/phpunit 就绪 |
| 功能增强提案 | `openspec/changes/规范改进与功能增强计划/` 16 任务全待办 |
| 原型提案 | `openspec/changes/plugin-prototype/` 阶段一至五完成，剩归档 2 项 |

**进行中遗留（需立即收尾）**：
- `wpcleanadmin/includes/class-wpca-helpers.php` 存在**未提交拆分改动**（工作区 dirty）。拆分工作进行中但被搁置，需在重新评估后要么完成提交、要么回退，避免长期半成品滞留。

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件（41 个，去重后 40 实质）

按职责分类（行数为当前统计）：

#### A-0. 进行中收尾（P0）
- [ ] 处理 `class-wpca-helpers.php` 未提交拆分改动：完成拆分并提交，或 `git restore` 回退后重排期

#### A-1. 活业务类（根 `includes/`，18 个）— 按行数优先
| 文件 | 行数 | 拆分方向 |
|------|------|----------|
| class-wpca-diagnostics.php | 626 | 诊断逻辑 / 报告生成 / UI 渲染分离 |
| class-wpca-helpers.php | 614 | 工具函数抽离为纯函数模块（拆分中） |
| class-wpca-cache.php | 536 | 缓存驱动 / 读写逻辑分离 |
| class-wpca-menu-customizer.php | 534 | 定制规则 / 渲染 / 持久化分离 |
| class-wpca-menu-manager.php | 423 | 菜单树构建 / 操作 handler 分离 |
| class-wpca-database.php | 420 | DB 操作 / 备份 / 优化分离 |
| class-wpca-user-roles.php | 417 | 角色数据 / 能力映射 / UI 分离 |
| wpca-core-functions.php | 371 | 纯函数库抽离 |
| class-wpca-resources.php | 356 | 资源注册 / 加载 / 清理分离 |
| class-wpca-reset.php | 323 | 重置步骤编排 / 单步逻辑 |
| class-wpca-error-handler.php | 321 | 错误捕获 / 日志 / 通知分离 |
| class-wpca-dashboard.php | 282 | 仪表盘 widget 注册 / 渲染分离 |
| class-wpca-permissions.php | 255 | 权限检查 / 能力映射分离 |
| class-wpca-core.php | 236 | 核心引导 / 模块协调分离 |
| ajax/database-ajax.php | 240 | AJAX handler / 业务逻辑分离 |
| ajax/cleanup-ajax.php | 237 | AJAX handler / 业务逻辑分离 |
| class-wpca-ajax.php | 207 | 网关分发 / handler 注册分离 |
| settings/menu-customization.php | 337 | 设置字段 / 渲染 / 校验分离 |

#### A-2. 渲染类 trait（6 个，因内联 CSS/JS >200）
需抽离 `assets/css|js` 并改 `wp_enqueue_*` 才能彻底达标：
- [ ] class-wpca-settings-page-render-tasks.php (421)
- [ ] class-wpca-performance-resource-tasks.php (392)
- [ ] class-wpca-extension-registration-tasks.php (359)
- [ ] class-wpca-cleanup-content-tasks.php (333)
- [ ] class-wpca-login-two-factor-tasks.php (314)
- [ ] class-wpca-settings-registration-tasks.php (262)

#### A-3. 双体系副本（根 `includes/modules/**`，16 个）— 见决策项 E-1
`modules/` 是 `autoload.php` 支持的平行类体系（`WPCleanAdmin\Modules\*`），与 `includes/` 根同名类功能重叠。需先定迁移/去重策略，再批量拆分：
helpers(614) cache(536) resources(356) login(324) menu-manager(283) login-two-factor(262) menu-customizer(260) user-roles-data(257) settings-styles(250) error-handler(227) user-roles(226) dashboard(221) menu-customizer-tree(215) login-style(211) settings(211) settings-field-renderers(207)

#### A-4. 排除项
- [x] `wpca-wordpress-stubs.php` (775) — autoload 加载的 IDE 空函数声明集，非业务逻辑，**不拆分**

---

### B. 功能增强提案（T001–T016，全部待办）

来源：`openspec/changes/规范改进与功能增强计划/`

| 阶段 | 任务 | 描述 | 状态 |
|------|------|------|------|
| 一（基础规范） | T001 | 完善错误处理规范文档 | 待办 |
| | T002 | 添加性能监控规范 | 待办 |
| | T003 | 编写安全审计清单 | 待办 |
| | T004 | 更新开发者指南 | 待办 |
| 二（功能模块规范） | T005 | Elementor 优化模块规范 | 待办 |
| | T006 | 主题模板优化模块规范 | 待办 |
| | T007 | Composer 依赖管理规范 | 待办 |
| | T008 | 扩展 API 接口规范 | 待办 |
| 三（开发工具集成） | T009 | 集成 PHPStan 静态分析 | 待办 |
| | T010 | 配置 PHPCS 代码检查 | 待办（phpcs 已就绪，待纳入标准） |
| | T011 | 设置自动化测试流程 | 待办 |
| | T012 | 完善 CI/CD 流水线 | 待办（基础已具备） |
| 四（文档与测试） | T013 | 编写用户使用文档 | 待办 |
| | T014 | 编写 API 参考文档 | 待办 |
| | T015 | 创建视频教程 | 待办 |
| | T016 | 完善示例代码 | 待办 |

---

### C. plugin-prototype 收尾（剩 2 项）

来源：`openspec/changes/plugin-prototype/tasks.md` 阶段六
- [ ] 将设计规范归档至 `openspec/specs/plugin-architecture/`
- [ ] 更新 CHANGELOG 至 1.8.3 条目（注：当前版本已远超，需校准）

---

### D. 测试与静态分析
- [ ] 单元测试覆盖率达 80%+（提案目标）
- [ ] 集成 PHPStan / Psalm / Rector（提案 T009–T010、T012）
- [ ] 在有 PHP 环境实跑 `composer test` 验证原型骨架（plugin-prototype 阶段四遗留）

---

### E. 架构决策项（阻塞部分拆分）
- **E-1. 双体系去重策略**：`includes/class-wpca-*` 与 `includes/modules/**/class-wpca-*` 功能重叠并存。`plugin-prototype` 提案建议「旧 `class-wpca-*` 逐步迁移至模块化架构」。需明确：以哪套为权威？是否合并？此决策阻塞 A-3 全部 16 个副本的拆分方式。
- **E-2. 渲染 trait 达标方式**：A-2 的 6 个渲染类抽离 `assets/` 是否本期执行，需与前端资源加载策略对齐。

---

## 三、优先级建议

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | A-0、E-1 | 收尾 helpers 半成品；定双体系去重策略（阻塞 A-3） |
| P1 | A-1 高行数项 | diagnostics/cache/menu-customizer/menu-manager/database/user-roles（>400 行优先） |
| P2 | A-2、D | 渲染 trait 抽离 assets；测试覆盖率与静态分析 |
| P3 | A-3、B、C | 双体系副本拆分；功能增强提案 16 任务；原型归档 |

---

## 四、统计摘要

- `>200` 行 PHP 文件：**41**（活业务 18 / 渲染 trait 6 / 双体系副本 16 / 排除 stubs 1）
- 功能增强提案待办：**16**
- 原型提案剩余：**2**
- 已完成代码拆分：核心类 5（core/admin/settings/login/diagnostics 试点）+ Settings 注册/渲染拆分

---

*本清单为过程性文档，随迭代更新；版本号变更见 `CHANGELOG.md`。*

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.8.14 | 08c03c2 | Helpers（613→88）拆 5 trait：Format / Env / Response / ErrorHandling / Log |
| 1.8.15 | 0b2e65a | Diagnostics（626→68）拆 5 trait：Registration / Runner / ServerCheck / ConflictCheck / SecurityCheck |
| 1.8.16 | ed23dc0 | Cache（536→105）拆 4 trait：Api / Memory / Database / File |

- **A-0 已完成**：helpers 的 dirty 收尾随 1.8.14 一并落地。
- **A-1 剩余活业务类**：menu-customizer(534) / menu-manager(423) / database(420) / user-roles(417) / core-functions(371) / resources(356) / reset(323) / error-handler(321) / dashboard(282) / permissions(255) / core(236) / ajax(207) / database-ajax(240) / cleanup-ajax(237) / settings/menu-customization(337)
