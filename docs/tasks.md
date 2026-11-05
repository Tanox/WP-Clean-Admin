# WP Clean Admin 开发任务清单（进行中）

> 生成日期：2026-09-13 · 基准版本：1.8.12 · 维护：Tanox
> **最近更新：2026-11-05 — E-2 解除（menu-customization / settings-page-render 内联资源抽离至 assets，两文件 <200）；版本推进至 1.9.12；剩余 >200 行收敛至 10 个（均属 modules 副本，受 E-1 阻塞）**
> 代码治理拆分规则见根 `AGENTS.md` 与全局用户规则（单文件 >200 行须拆分）。

已完成任务已归档至 `docs/tasks-done.md`。本文档仅保留**进行中 / 待办**任务。

---

## 一、当前状态概览

| 维度 | 状态 |
|------|------|
| 版本 | **1.9.12**（已发布，CI 含 phpcs/phpunit/php-lint） |
| 代码治理（>200 行拆分） | 根 `includes/` 活业务类与渲染/任务 trait 全部完成（含 E-2 内联资源抽离）；剩余仅 `modules/**` 副本（A-3，受 E-1 阻塞） |
| 双体系副本（modules/**） | 阻塞于 E-1（架构决策未定），见 A-3 |
| 渲染类内联资源 | 已解决（1.9.12 抽离至 assets 并由 `Settings_Scripts` 统一 enqueue），见 A-1 / A-2 |
| 功能增强提案 | 16 任务全待办（openspec 提案，需单独规划） |
| 原型提案 | 剩归档 2 项 |

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件（剩余 10，全部为 modules 副本，排除 stubs / 引导文件 2）

#### A-1. 根 `includes/` 活业务类（已全部完成）
| 文件 | 行数（原→现） | 状态 | 阻塞 |
|------|------|------|------|
| settings/menu-customization.php | 333 → 105 | 已完成（1.9.12，内联 CSS/JS 抽离至 assets） | — |

> 根 `includes/` 活业务类（helpers / diagnostics / cache / database / menu-customizer / menu-manager / user-roles / core / ajax / reset / dashboard / permissions / resources）连同渲染/任务 trait 均已拆分完成（1.8.14–1.9.12），见 `docs/tasks-done.md`。

#### A-2. 渲染 / 任务 trait（7 项：全部完成）
拆分策略：原 trait 改为「组合 trait」（`use` 两个子 trait），消费类（`class-wpca-*.php`）零改动；公开 API 不变。

| 文件 | 行数 | 拆分方向 | 状态 | 阻塞 |
|------|------|----------|------|------|
| class-wpca-settings-page-render-tasks.php | 362 → 123 | 容器渲染（内联 CSS/JS 抽离至 assets） | 已完成（1.9.12） | — |
| class-wpca-performance-resource-tasks.php | 324 | → Minify / Combine | 已完成（1.9.11） | — |
| class-wpca-extension-registration-tasks.php | 293 | → Core / Lifecycle | 已完成（1.9.11） | — |
| class-wpca-cleanup-content-tasks.php | 286 | → Posts / Shortcode | 已完成（1.9.11） | — |
| class-wpca-login-two-factor-tasks.php | 270 | → Secret / Auth | 已完成（1.9.11） | — |
| class-wpca-settings-registration-tasks.php | 232 | → Sections / Fields | 已完成（1.9.11） | — |
| class-wpca-reset-operations-tasks.php | 229 | → Settings / Plugin | 已完成（1.9.11） | — |

#### A-3. 双体系副本（根 `includes/modules/**`，10 个）— 阻塞于 E-1
`modules/` 是 `autoload.php` 支持的平行类体系（`WPCleanAdmin\Modules\*`），与 `includes/` 根同名类功能重叠。需先定迁移 / 去重策略（E-1），再批量拆分。清单：
helpers(606) · cache(540) · resources(353) · menu-customizer(303) · login(268) · menu-manager(246) · user-roles-data(234) · settings-styles(212) · settings(207) · login-two-factor(205)

- [ ] **决策 E-1**：以哪套为权威？是否合并？之后才能安全拆分 A-3

#### A-4. 排除项（已归档）
- [x] `wpca-wordpress-stubs.php` — 不拆分（见 `docs/tasks-done.md`）

---

### B. 功能增强提案（T001–T016，全部待办）
来源：`openspec/changes/规范改进与功能增强计划/`（需单独规划，非代码拆分范畴）

| 阶段 | 任务 | 状态 |
|------|------|------|
| 一（基础规范） | T001 错误处理规范 / T002 性能监控规范 / T003 安全审计清单 / T004 开发者指南 | 待办 |
| 二（功能模块规范） | T005 Elementor / T006 主题模板 / T007 Composer / T008 扩展 API 规范 | 待办 |
| 三（开发工具集成） | T009 PHPStan / T010 PHPCS / T011 自动化测试 / T012 CI/CD | 待办 |
| 四（文档与测试） | T013 用户文档 / T014 API 文档 / T015 视频教程 / T016 示例代码 | 待办 |

### C. plugin-prototype 收尾（2 项）
- [ ] 将设计规范归档至 `openspec/specs/plugin-architecture/`
- [ ] 校准 CHANGELOG（版本已远超 1.8.3，需以当前版本为准）

### D. 测试与静态分析（部分阻塞：无 PHP 运行时）
- [ ] 单元测试覆盖率达 80%+（提案目标）
- [ ] 集成 PHPStan / Psalm / Rector（T009–T010、T012）
- [ ] 在有 PHP 环境实跑 `composer test` 验证（当前环境无 php/composer，见项目记忆 `env_no_php_runtime.md`）

### E. 架构决策项（阻塞部分拆分）
- **E-1. 双体系去重策略**（阻塞 A-3）：需明确以 `includes/` 还是 `modules/` 为权威，是否合并。
- **E-2. 渲染内联资源抽离**（已解决，1.9.12）：`menu-customization.php` 与 `settings-page-render-tasks.php` 的内联 CSS/JS 已抽离至 `assets/` 并由 `Settings_Scripts` 统一 enqueue，两文件降至 <200 行。

---

## 三、优先级建议（更新于 2026-11-05）

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | 无（A-2 已全部完成） | A-2 渲染/任务 trait 与 E-2 内联资源均已解决（1.9.11–1.9.12） |
| P1 | E-1 | 决策双体系去重（modules 副本归属），解锁 A-3 |
| P2 | A-3 | 双体系副本拆分（依赖 E-1） |
| P3 | B、C、D | 功能增强提案 16 任务；原型归档；测试覆盖率与静态分析（依赖 PHP 环境） |

---

## 四、统计摘要（2026-11-05）

- `>200` 行 PHP 文件：**10**（全部为 `includes/modules/**` 副本〔A-3〕，受 E-1 阻塞；已排除 stubs 与引导文件）
- 已完成代码拆分（归档）：根 `includes/` 活业务类 + core / ajax / install / login-captcha 等共 13+ 批次（1.8.14–1.9.10）
- 功能增强提案待办：**16**
- 原型提案剩余：**2**

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.9.12 | 本次 | E-2 解除：menu-customization / settings-page-render 内联 CSS/JS 抽离至 assets（settings 页 JS 经 wp_localize_script 注入 saveMessage）；两文件降至 <200 |
| 1.9.11 | 上次 | A-2 渲染/任务 trait 再拆为子 trait（6 个，消费类零改动）；迁移 TASKS.md → docs/ |
| 1.9.10 | ea1391f | core-functions（380→14）拆 4 文件；menu-manager 角色限制再抽离 |

---

*本清单为过程性文档，随迭代更新；版本号变更见 `CHANGELOG.md`。已完成项见 `docs/tasks-done.md`。*
