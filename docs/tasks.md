# WP Clean Admin 开发任务清单（进行中）

> 生成日期：2026-09-13 · 基准版本：1.8.12 · 维护：Tanox
> **最近更新：2026-11-05 — E-1 已定（modules 为权威体系，根 includes/ 115 个死文件归档至 archive/legacy-includes-1.9.12/）；E-2 更正（资源抽离作用于已归档死根代码，活设置页仍内联）；版本 1.9.13；根 includes/ 仅余 9 个活文件，>200 行仅 modules 10 个**
> 代码治理拆分规则见根 `AGENTS.md` 与全局用户规则（单文件 >200 行须拆分）。

已完成任务已归档至 `docs/tasks-done.md`。本文档仅保留**进行中 / 待办**任务。

---

## 一、当前状态概览

| 维度 | 状态 |
|------|------|
| 版本 | **1.9.13**（已发布，CI 含 phpcs/phpunit/php-lint） |
| 代码治理（>200 行拆分） | 根 `includes/` 死代码已归档（117 文件，仅余 9 活文件）；剩余 >200 行仅 `modules/**` 10 个（A-3，E-1 已定 modules 权威） |
| 双体系副本（modules/**） | E-1 已定：modules 为权威体系，根 `includes/` 旧类已归档为死代码，见 A-3 |
| 渲染类内联资源 | 更正：1.9.12 抽离作用于已归档的死根代码（根 `Settings_Scripts`），活设置页（`modules/admin/settings/`）仍内联输出，待重做，见 A-1 |
| 功能增强提案 | 16 任务全待办（openspec 提案，需单独规划） |
| 原型提案 | 剩归档 2 项 |

---

## 二、剩余任务清单

### A. 代码治理：拆分 >200 行文件（剩余 10，全部为 modules 副本，排除 stubs / 引导文件 2）

#### A-1. 根 `includes/` 业务类（已确认死代码并归档，1.9.13）
> 经核查：运行期仅 `modules/` 体系被引导（`wp-clean-admin.php:81` 实例化 `WPCleanAdmin\Modules\Core\Classes\Core`；旧 `load_legacy_modules()` 已禁用），根 `includes/` 旧类（含此前拆分过的 helpers / diagnostics / cache / database / menu-customizer / menu-manager / user-roles / core / ajax / reset / dashboard / permissions / resources 及渲染/任务 trait）**零引用、全部为死副本**。1.9.13 已将 115 个死文件归档至 `archive/legacy-includes-1.9.12/`，仅保留 9 个活根文件（autoload / wpca-core-functions(+4 子库) / wpca-wordpress-stubs / ajax/settings-ajax.php / class-wpca-helpers.php），见 `docs/tasks-done.md`。

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

#### A-3. 活体系拆分（modules/**，10 个）
`modules/` 经核查为运行期唯一权威体系（E-1 已定），`includes/` 根旧类已归档为死代码。现仅 `modules/**` 10 个文件超 200 行，需按组合 trait 范式拆分（消费类零改动）。清单：
helpers(606) · cache(540) · resources(353) · menu-customizer(303) · login(268) · menu-manager(246) · user-roles-data(234) · settings-styles(212) · settings(207) · login-two-factor(205)

- [ ] 按组合 trait 范式拆分上述 10 个 modules 文件（A-3 主体）

#### A-4. 排除项（不拆分）
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
- **E-1. 双体系去重策略**（已定，1.9.13）：`modules/` 为运行期唯一权威体系，根 `includes/` 旧类已归档为死代码（117 文件），不再合并。解锁 A-3。
- **E-2. 渲染内联资源抽离**（更正，1.9.13）：1.9.12 的抽离作用于 `includes/` 根 `Settings_Scripts`（现已归档为死代码），活设置页（`modules/admin/settings/class-wpca-settings-scripts.php`）仍内联输出 `<script>`、CSS 经 `Settings_Styles` 内联；资源抽离需重做于 modules 活代码方为有效。

---

## 三、优先级建议（更新于 2026-11-05）

| 优先级 | 范围 | 说明 |
|--------|------|------|
| P0 | A-3 | 拆分 modules/** 10 个 >200 行文件（E-1 已定，modules 权威，组合 trait 范式） |
| P1 | E-2 重做 | 将资源抽离重做于 modules 活设置页（替换内联输出） |
| P2 | A-3 | 双体系副本拆分（依赖 E-1） |
| P3 | B、C、D | 功能增强提案 16 任务；原型归档；测试覆盖率与静态分析（依赖 PHP 环境） |

---

## 四、统计摘要（2026-11-05）

- `>200` 行 PHP 文件：**10**（全部为 `modules/**` 活文件〔A-3〕，根 `includes/` 死代码已归档；已排除 stubs 与引导文件）
- 已完成代码拆分（归档）：根 `includes/` 活业务类 + core / ajax / install / login-captcha 等共 13+ 批次（1.8.14–1.9.10）
- 功能增强提案待办：**16**
- 原型提案剩余：**2**

---

## 五、进度更新日志

| 版本 | 提交 | 内容 |
|------|------|------|
| 1.9.13 | 本次 | E-1 已定：归档根 includes/ 115 个死代码文件至 archive/legacy-includes-1.9.12/（modules 权威）；清理 E-2 孤立资源 4 个；更正 E-2（抽离作用于死根代码，活设置页仍内联） |
| 1.9.12 | 上次 | E-2 抽离（注：作用于根死代码，1.9.13 更正）：menu-customization / settings-page-render 内联 CSS/JS 抽离至 assets；两文件降至 <200 |
| 1.9.11 | 上次 | A-2 渲染/任务 trait 再拆为子 trait（6 个，消费类零改动）；迁移 TASKS.md → docs/ |
| 1.9.10 | ea1391f | core-functions（380→14）拆 4 文件；menu-manager 角色限制再抽离 |

---

*本清单为过程性文档，随迭代更新；版本号变更见 `CHANGELOG.md`。已完成项见 `docs/tasks-done.md`。*
