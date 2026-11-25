# WP Clean Admin 已完成任务归档

> **适用基线**：1.9.18　|　**更新日期**：2026-11-23　|　**状态**：历史归档
> 归档日期：2026-11-05 · 最近补充：2026-11-23 · 维护：Tanox
> 本文件收录已从 `docs/tasks.md` 移出的**已完成**任务，保持主清单精简。
> 版本号变更见根 `CHANGELOG.md`；开发规范见根 `CONTRIBUTING.md`；文档索引见 `docs/README.md`。

---

## 一、代码治理：已完成的 >200 行拆分

拆分规则：单文件 >200 行的 PHP 源文件拆为职责 trait，主类 `require_once` 引入 + `use XxxTasks;`，公开 API（单例/方法签名）保持不变。

| 版本 | 提交 | 内容 | 行数变化 |
|------|------|------|----------|
| 1.8.14 | 08c03c2 | Helpers 拆 5 trait：Format / Env / Response / ErrorHandling / Log | 613→88 |
| 1.8.15 | 0b2e65a | Diagnostics 拆 5 trait：Registration / Runner / ServerCheck / ConflictCheck / SecurityCheck | 626→68 |
| 1.8.16 | ed23dc0 | Cache 拆 4 trait：Api / Memory / Database / File | 536→105 |
| 1.8.18 | 52cf07a | Database 拆 4 trait：Info / Backup / Restore / BackupList | 420→66 |
| 1.9.2 | 5a94990 | Error_Handler / Reset 拆分；缓存与备份安全修复 | — |
| 1.9.3 | b91d669 | Resources 拆 3 trait | 356→36 |
| 1.9.4 | 6e3810a | Permissions 拆 2 trait | 286→56 |
| 1.9.5 | cc3c4c8 | Dashboard 拆 3 trait | 282→53 |
| 1.9.6 | fcd44d6 | AJAX\Database 拆 3 trait | 241→42 |
| 1.9.7 | c8a1066 | AJAX\Cleanup 与 Install 各拆 3 trait | — |
| 1.9.8 | 7a4d95b | Core 与 LoginCaptchaTasks 各拆 2 trait（后者改为组合 trait） | 235→102 |
| 1.9.9 | f664ee3 | Menu_Manager / User_Roles / Menu_Customizer 拆为 trait | 418→138 / 411→109 / 528→100 |
| 1.9.10 | ea1391f | core-functions（380→14）拆 4 文件；menu-manager 角色限制再抽离 | 380→14 |
| 1.9.11 | c01b5ee | A-2 渲染/任务 trait 再拆为组合 trait（6 个，消费类零改动）；任务清单迁移至 `docs/` | — |
| 1.9.12 | b2dee36 | E-2 内联资源抽离（menu-customization / settings-page-render） | 两文件降至 <200 |
| 1.9.13 | 42fc534 | E-1 定案：归档根 `includes/` 115 个死代码文件至 `archive/legacy-includes-1.9.12/` | — |
| 1.9.14 | 5fd486d | 拆分 `modules/utilities/classes/` Helpers 与 Cache 为 trait | — |
| 1.9.14 | 本次 | 文档治理：建立 `CONTRIBUTING.md` / `docs/README.md`，重写 `DEVELOPMENT.md` / `AGENTS.md` / `modules/README.md`，统一版本与历史标注（纯文档变更，不提升插件版本） | — |
| 1.9.16 | 本次 | A-3 收官：拆分 `modules/**` 剩余 15 个 >200 行活文件，新增 30 个任务 trait、改写 14 个消费类；全仓 >200 行仅余 2 个排除项 | — |
| 1.9.17 | 本次 | E-2 闭环：恢复活设置页资源抽离（创建 `assets/css/wpca-settings.css`、抽离 `assets/js/wpca-settings.js`，改写 `Settings_Scripts::render_scripts` 由内联改为 `include`）；CI `php-lint` 增加 PHP 7.4 矩阵；版本号统一至 1.9.17 | — |

---

## 二、已闭环的收尾 / 决策项

- **A-0 已完成**：`class-wpca-helpers.php` 未提交拆分改动随 1.8.14 一并落地（dirty 收尾）。
- **A-4 已决策（排除）**：`wpca-wordpress-stubs.php` 为 autoload 加载的 IDE 空函数声明集，非业务逻辑，**不拆分**。

---

## 三、2026-11-23 归档（自 `docs/tasks.md` 移入）

### C. plugin-prototype 收尾（2 项，全部完成）

- [x] 将设计规范归档至 `openspec/specs/plugin-architecture/`（已在 `design.md` 确立为当前权威规范）
- [x] 校准提案中的 `1.8.3` 基线表述（提案均为 1.8.3 历史基线文档，已标注「不代表当前状态」；当前版本以根 `CHANGELOG.md` 1.9.17 为准）

### D. 测试与静态分析（完成项）

- [x] CI `php-lint` 增加 PHP 7.4 语法检查矩阵（§7.2，复盖最低支持版本）（1.9.17 完成）

---

## 四、2026-11-25 15:59:52 +08:00 归档（自 `docs/tasks.md` 移入）

> 本轮核对后，以下项目确认已完成或已闭环，从进行中清单移出。版本号变更统一见 `CHANGELOG.md`。

### 已完成项

- [x] **A-1~A-4 代码治理拆分**：115 个 legacy 死代码归档、任务/组合 trait 拆分、A-3 收官（15 文件）、2 个登记排除项；详见本文 §一与 §二。
- [x] **B-T001~T014 / T016 功能增强提案**：基础规范 4 项、功能模块规范 4 项、开发工具集成 4 项（配置为已完成，实跑见 VERIFY-005）、文档与示例 T013/T014/T016；T015 视频教程未完成，仍留在 `tasks.md`。
- [x] **C plugin-prototype 收尾**：2 项已归档（见本文 §三）。
- [x] **D 已完成项**：CI `php-lint` PHP 7.4 矩阵（见本文 §三）；其余测试/静态分析实跑仍为 VERIFY-005。
- [x] **E-1 双体系去重策略**：`modules/` 为唯一权威，legacy 归档只读（1.9.13）。
- [x] **E-2 渲染内联资源抽离**：设置页 CSS/JS 抽至 `assets/`（1.9.17 闭环）。
- [x] **2026-11-25 系统性文档批次**：新增项目概述、安装部署、功能模块、API 接口 + OpenAPI、FAQ、优化建议，扩充用户指南、文档索引、API 速查与 CHANGELOG；相对链接校验通过（纯文档变更，不提升插件版本）。

### 归档说明

- 已完成任务移出后，`docs/tasks.md` 仅保留 20 项未完成/待验证任务（P0×4 / P1×11 / P2×5）。
- 本归档时间为 **2026-11-25 15:59:52 +08:00**，与 `docs/tasks.md` 本次更新时间戳一致。

---

## 五、2026-11-25 01:03:44 +08:00 归档（自 `docs/tasks.md` 移入）

> 本轮完成 12 项优化建议（OPT-001~012）的代码落地与文档同步，并交付英文文档镜像与视频教程制作稿。版本号变更统一见 `CHANGELOG.md`。

### 5.1 优化建议实施（OPT-001~012）

- [x] **OPT-001（P0）菜单定制器单一数据源 + AJAX 无条件注册**：`Menu_Customizer_Options` 成为 `wpca_menu_customizer_settings` 的唯一读写入口，Tree / Render / Customize-Tasks 经构造器注入同一实例；`Menu_Customizer::init()` 移除 `settings['menu_customizer']` 守卫，4 个 AJAX action 无条件注册；提供 legacy 幂等迁移。默认注册数 0→4。
- [x] **OPT-002（P1）统一 AJAX 响应 DTO**：新增 `WPCleanAdmin\AJAX\Response`（`includes/ajax/response-ajax.php`），4 个菜单定制 handler 全部迁移；`sanitize_settings()` 递归消毒下沉至 `Helpers`，`AJAX\Settings` 仅保留静态代理；删除 3 个从未注册的 settings handler。JSON 结构种类 3→1。
- [x] **OPT-003（P1）设置字段回显修正**：`render_field()` 改为嵌套优先（`wpca_settings[group][id]`），缺失回退独立 option，`array_key_exists` 保证 0/false 回显；`name` 属性同步为 `wpca_settings[group][id]`；新增 `group` 字段标识与 `esc_textarea`/`checked`/`selected` 测试桩。
- [x] **OPT-004（P1）唯一全局错误处理器**：`Helpers::register_error_handler()` 降级为 deprecated no-op，`custom_error_handler()` 返回 `false` 不再吞错；`Error_Handler` 增加 `private static $registered` 防重复注册。
- [x] **OPT-005（P1）REST 限制注册点修正**：改由 `rest_api_init`（priority 1）注册 `rest_authentication_errors`，匿名请求返回 403（`rest_not_logged_in`），已登录与既有错误透传；新增 `tests/RestApiRestrictionTest.php`。
- [x] **OPT-006（P1）数据库缓存分批 + cron 兜底**：`LIMIT 500` 分批查询与删除，hourly 事件 `wpca_cache_cleanup` 负责过期回收，停用/卸载时 unschedule。
- [x] **OPT-007（P1）文件缓存迁 uploads + 原子写入**：目录迁至 `wp_upload_dir()['basedir']/wpca-cache`（含 `index.php` 与 `.htaccess`），写入改为临时文件 + `rename()`，清理改由 cron 限频 + `FilesystemIterator` 承担。
- [x] **OPT-008（P2）设置读取请求级记忆化**：`wpca_get_settings()` 使用 `$GLOBALS['wpca_settings_cache']`，新增 `wpca_flush_settings_cache()`，`wpca_update_settings()` 写后失效缓存。
- [x] **OPT-009（P1）设置页 Tab 唯一渲染 + WAI-ARIA**：4 个 Tab 改用独立 settings page slug（`wp-clean-admin-general|cleanup|performance|security`）；补齐 `tablist`/`tab`/`aria-selected`/`aria-controls`/`tabpanel`；保存提示改为 `role="status" aria-live="polite"`；内联 `<style>` 迁至 `assets/css/wpca-settings.css`。
- [x] **OPT-010（P2）仪表盘统计 transient 化**：15 分钟 transient（`wpca_dashboard_stats`）缓存统计结果，transients 改前缀聚合，孤儿 meta 统计改 `NOT EXISTS`。
- [x] **OPT-011（P1）前端注入与反馈改造**：`wpca-dashboard.js`、`wpca-settings.js`、`wpca-main.js` 移除全部 `alert()` 与 `.html()` 插值，改用 jQuery DOM API `.text()`；新增页面内状态/进度提示。未转义插值点 4→0。
- [x] **OPT-012（P2，工程治理）部分闭环**：新增覆盖率门禁工具 `tools/check-coverage.php`（Clover 解析，默认阈值 70%）、`composer test:coverage` 脚本与 CI `coverage` 作业（首次 `continue-on-error: true` 仅报基线）；`docs/openapi.yaml`（OpenAPI 3.1）补齐并与 `API接口文档.md` 同步；`uninstall.php` 补 cron unschedule 与缓存目录清理。PHPCS 阻塞化与覆盖率实测基线未完成，已拆分为 TASK-DOC-012A/012B 留在 `tasks.md`。

### 5.2 文档与治理

- [x] **TASK-DOC-013 英文文档镜像**：新增 `docs/README.en.md`、`user-guide.en.md`、`installation-guide.en.md`、`faq-troubleshooting.en.md`、`api-reference.en.md`、`overview.en.md` 共 6 份英文镜像；`docs/README.md` 增加英文索引行、阅读路径与同步清单条目。
- [x] **B-T015 视频教程制作稿**：新增 `docs/video-tutorial.md`（5 集完整分镜脚本、逐字旁白、字幕与配音规范、素材清单、发布要求与视频索引表）；录制与剪辑待资源，已拆出为剩余任务。
- [x] **文档状态同步**：`docs/FAQ与故障排除.md`、`docs/优化建议.md`、`docs/api-reference.md`、`docs/项目概述.md`、`docs/安装部署指南.md`、`docs/user-guide.md`、`docs/API接口文档.md`、`docs/功能模块说明.md`、`docs/README.md` 按已落地事实改写；`docs/README.md` 移除已删除历史快照文档的失效引用。
- [x] **VERIFY-007（>200 行口径复核）**：以实际行数复核发现历史统计「仅余 2 个登记排除项」不准确——`class-wpca-cache-file-tasks.php`（242 行）与 `class-wpca-settings-fields.php`（203 行）超限但未登记，`tests/wp-polyfill.php`（264 行）从未计入。已拆分前两者（新增 `CacheFileCleanupTasks`、`SettingsFieldsRenderTasks`、`Settings_Field_Definitions` 三个模块），并在 `CONTRIBUTING.md` §4.5 登记第 3 个排除项与明确统计口径。当前 **0 待办 + 3 个登记排除项**（函数桩 774、引导主文件 236、测试函数桩 264）。
- [x] **回归测试补充**：新增 `tests/ErrorHandlerTest.php`、`tests/RestApiRestrictionTest.php`、`tests/ResponseTest.php`、`tests/MenuCustomizerOptionsTest.php`、`tests/SettingsFieldsEchoTest.php`；测试桩 `wp-polyfill.php` 补 option/action/filter 内存存储、`WP_Error`、`wp_send_json` 捕获与转义函数。

### 5.3 归档说明

- 已完成任务移出后，`docs/tasks.md` 仅保留 **12 项**未完成/待验证任务（P0×3 / P1×4 / P2×5），其中阻塞 2 项。
- 本归档时间为 **2026-11-25 01:03:44 +08:00**，与 `docs/tasks.md` 本次更新时间戳一致。

---

*归档文件，只读；如需恢复某项请移回 `docs/tasks.md`。*
