# MEMORY.md — WPCleanAdmin 项目长期记忆

> **整理日期**：2026-11-26　|　**适用基线**：1.9.18
> **用途**：跨会话快速检索架构、决策、限制与待办。标注「已证实」的条目均有代码 / 提交 / CI 旁证；历史日期文件仅作过程记录，不作为当前事实依据。
> **权威来源**：开发规范 `CONTRIBUTING.md`；版本历史 `CHANGELOG.md`；未完成任务 **唯一来源** `docs/tasks.md`。

## 1. 架构说明

| 层次 | 事实 | 依据 |
|------|------|------|
| 运行期入口 | `wp-clean-admin.php` → `plugins_loaded` → `WPCleanAdmin\Modules\Core\Classes\Core` → `Module_Loader::safe_init()` | 引导主文件 |
| 唯一权威体系 | `WPCleanAdmin\Modules\*`（`admin/` / `core/` / `utilities/`），任务类普遍采用「主类 `require_once` + `use` 任务 trait」组合范式 | `includes/modules/` 实测 |
| 归档层 | `archive/legacy-includes-1.9.12/` 含 115 个 legacy 类，只读、不加载、不 `require`、不复制回插件目录 | E-1 决策 / `AGENTS.md` §2 |
| 目录边界 | 插件运行文件仅放 `wpcleanadmin/`；原型（4 个静态文件）、文档、OpenSpec、CI 模板放仓库根对应目录 | `AGENTS.md` §2 |
| 设置数据源 | `wpca_settings`（按 group 嵌套，如 `wpca_settings[security][restrict_rest_api]`）；菜单定制独立且唯一键 `wpca_menu_customizer_settings`，旧键 `wpca_settings.menu_customizer` 首次读取时幂等迁移、不删旧数据 | OPT-001 / OPT-003 |
| 设置读取 | `wpca_get_settings()` 请求级记忆化（`$GLOBALS['wpca_settings_cache']`），`wpca_update_settings()` 写后失效；测试改选项后须显式 `wpca_flush_settings_cache()` | OPT-008 |
| 缓存 | `Cache` 统一分发 memory / database / file 三后端；file 目录 `wp-content/uploads/wpca-cache/`（防护文件 + 原子写入），过期项由 hourly cron `wpca_cache_cleanup` 回收 | OPT-006 / OPT-007 |
| 对外接口 | 仅 AJAX（4 个菜单定制 action 无条件注册，统一 `WPCleanAdmin\AJAX\Response` DTO）；**无 `wpca/v1` REST 路由** | OPT-001 / OPT-002 |
| 发布 | CI workflow 成功 → `release.yml` 读取插件头版本 → 打 tag 并上传 `wp-clean-admin-v<版本>.zip` | `release.yml` / v1.9.18 已发布 |

## 2. 核心决策（按主题）

| 决策 | 结论 | 时间 / 版本 | 可信度 |
|------|------|-------------|--------|
| E-1 架构定论 | `Modules\*` 为运行期唯一权威体系；legacy 死代码归档至 `archive/legacy-includes-1.9.12/`，只读 | 2026-11-05 / 1.9.12–1.9.13 | 已证实（代码 + 提交） |
| E-2 资源抽离 | 设置页内联 CSS/JS 抽至 `assets/`，由 `Settings_Styles` / `Settings_Scripts` 注入，渲染行为不变 | 2026-11-07 / 1.9.17 | 已证实（代码） |
| A-3 治理拆分 | >200 行源文件拆为任务 trait / 组合 trait；1.9.18 复核后当前 **0 待办 + 3 个登记排除项**（stubs 774、引导主文件 236、测试桩 264） | 2026-11-07 / 1.9.16 → 2026-11-26 / 1.9.18 | 已证实（原始行数复测） |
| OPT-001 单一数据源 | 菜单定制读写统一到 `wpca_menu_customizer_settings`，4 个 AJAX action 无条件注册，旧键幂等迁移 | 2026-11-26 / 1.9.18 | 已证实（CI 全绿） |
| OPT-002 响应契约 | 统一 `Response` DTO，删除 3 个死 handler；JSON 结构种类 3→1 | 2026-11-26 / 1.9.18 | 已证实（代码 + CI） |
| OPT-007 缓存迁出 | 文件缓存从插件目录迁至 `uploads/wpca-cache/`，插件目录不再需要写权限 | 2026-11-26 / 1.9.18 | 已证实（代码） |
| OPT-008 请求级记忆化 | `wpca_get_settings()` 缓存 + 写路径失效；测试必须显式 flush，否则出现跨用例污染 | 2026-11-26 / 1.9.18 | 已证实（CI 失败后修复） |
| 质量门禁策略 | 覆盖率与 PHPCS 门禁先 `continue-on-error: true` 取基线，达标并清零后再转阻塞；避免阻塞自动发布 | 2026-11-26 / 1.9.18 | 已证实（CI 结果） |
| 版本源 | 插件头 `Version:` + `WPCA_VERSION` 唯一权威；纯文档/配置变更不提升版本，写 `[Unreleased]`；`@version` 必须同步、`@since` 不动 | 现行（§4.2 / §9） | 权威规范 |

## 3. 关键事实速查（时效敏感）

| 事实 | 当前值 | 核实时效 | 来源 |
|------|--------|----------|------|
| 插件版本 | **1.9.18** | 2026-11-26 | `wp-clean-admin.php` |
| 已发布 tag | `v1.9.18`（commit `43e6549`） | 2026-11-26 | `git ls-remote --tags` + Release workflow |
| CI 状态 | workflow 成功；5 个 PHPUnit 矩阵 + lint 全绿；PHPCS / Coverage Gate 非阻塞失败 | 2026-11-26 | GitHub Actions API |
| 最低 PHP / WordPress | 7.4 / 5.0 | 2026-11-26 | 插件头 / `composer.json` |
| 运行时入口 | `wp-clean-admin.php` → `plugins_loaded` → `Core` | 2026-11-26 | 引导主文件 |
| 长期分支 | 仅 `main`（无 `develop`） | 2026-11-26 | `CONTRIBUTING.md` §6 |
| >200 行排除项 | 3 个（见 §2 A-3） | 2026-11-26 复测 | 原始行数统计 |
| 未完成任务 | 12 项（P0×3 / P1×4 / P2×5） | 2026-11-26 | `docs/tasks.md` |
| 英文镜像 | 6 份已交付；`功能模块说明.md` / `优化建议.md` 待译 | 2026-11-26 | `docs/` 实测 |
| 本机 PHP/Composer | 均不可用，无法 `php -l` / `phpunit` | 2026-11-26 复核 | 环境探测 |
| 推送方式 | github.com:443 直连失败；一次性代理参数推送（见 §7） | 2026-11-26 复核 | 网络实测 |

## 4. 已知限制（当前版本实际存在，非待办）

1. **2FA 无恢复码**：丢失绑定只能通过数据库清除 user meta 或由管理员恢复（FAQ Q5）。
2. **REST 限制不是安全边界**：仅拦截 REST API；彻底关闭仍需在服务器层限制（FAQ Q7）。
3. **验证码依赖 GD**：缺失时图片生成被安全跳过，登录校验会拒绝无验证码请求（FAQ Q4）。
4. **卸载不清理备份与日志**：`uninstall.php` 清 options / transients / usermeta / cron / 缓存目录，但 `backups/` 与日志目录需手动删除（FAQ Q8）。
5. **多站点未做统一配置验证**：设置按站点存各自 `wpca_settings`，需逐站验证（FAQ Q9）。
6. **两个 CI 门禁非阻塞失败**：PHPCS 存量告警、覆盖率低于 70%；不影响 workflow 成功与自动发布，但需完成 `TASK-DOC-012A/012B` 才能转阻塞。
7. **仪表盘无 AJAX 懒加载骨架**：首屏仍同步渲染（`TASK-DOC-010A`）。
8. **对外接口仍是 AJAX**：没有新增 REST 路由，`docs/openapi.yaml` 描述的是契约而非已暴露端点。
9. **本机无 PHP 运行时**：所有 PHP 改动只能静态审阅 + CI 验证，不可声称本地测试通过。

## 5. 后续待办（唯一来源：`docs/tasks.md`）

> 禁止在本文件或其他文档维护第二份任务清单；本表仅为跨会话速览，**状态与项数以 `docs/tasks.md` 为准**。变更任务先改 `docs/tasks.md`，再按需回写这里。

| 分类 | 任务 | 说明 |
|------|------|------|
| P0 | VERIFY-005 / VERIFY-001 / VERIFY-002 | 工具链实跑与覆盖率、AJAX nonce 四态、菜单定制闭环实测（VERIFY-005 已部分由 CI 完成，待按 CI 结果更新状态） |
| P1 | VERIFY-003 / VERIFY-004 / TASK-DOC-012A / TASK-DOC-012B | REST 匿名请求、字段回显实测；覆盖率与 PHPCS 门禁转阻塞 |
| P2 | TASK-DOC-010A / TASK-DOC-014 / B-T015 / VERIFY-006 / VERIFY-008 | 仪表盘懒加载、剩余 2 份英文镜像、视频录制、多站点复核与大表压测 |

## 6. 时间线（精简）

| 日期 | 关键事件 |
|------|----------|
| 2026-09-13 | 1.9.0 修复命名空间冲突、2FA 绕过、权限校验等 P0；PHP 要求升至 7.4 |
| 2026-11-04 | 1.9.2 安全修复（路径遍历、反序列化、输入消毒）与首批 trait 拆分 |
| 2026-11-05 | E-1 归档 legacy 死代码；组合 trait 范式确立（1.9.11–1.9.13） |
| 2026-11-07 | 1.9.17：E-2 资源抽离、A-3 拆分收官 |
| 2026-11-08 | CI 全红根因链修复，自动发布 v1.9.17 |
| 2026-11-25 | 系统性文档体系完善（项目概述 / 安装部署 / 功能说明 / API + OpenAPI / FAQ / 优化建议 / 用户指南） |
| 2026-11-26 | **1.9.18**：OPT-001~012 落地、>200 行治理复核、英文镜像 6 份、视频脚本；CI 修复后全绿并自动发布 v1.9.18；使用说明与变更记录补强（本次） |

## 7. 环境与协作约束

- **无 PHP 运行时**：`where php` / `where composer` 失败；PHP 改动只能静态审阅 + CI 验证，提交信息标注「待 CI/PHP 环境验证」。
- **git 身份**：本机未配置 `user.name` / `user.email`；用一次性参数 `git -c user.name="Tanox" -c user.email="xepinchan@qq.com" commit ...`，不改配置。
- **推送代理**：github.com:443 直连超时；`git -c http.proxy=http://192.168.10.100:20172 -c https.proxy=http://192.168.10.100:20172 push origin main`。
- **并发会话**：外部轮次可能在工具调用之间改盘 / 提交；动手前和提交前都实查 `git log` / `git status`，提交只用 `git add` 指定文件，避免 `git add -A` 捕获他人改动或临时产物。
- **行数口径**：>200 行以**原始行数**为准；`Measure-Object -Line` 跳过空行会漏判。
- **临时文件**：一律放 `tmp/`（已 gitignore）并在会话结束清理；禁止在仓库根散落探针脚本 / 输出（曾出现 `$null` 垃圾文件）。
- **PowerShell 陷阱**：`node -e` 外层用双引号、内层用单引号；`$null` 会被变量展开，删除该文件名需用 `String.fromCharCode(36)+'null'` 或删除工具传字面量。

## 8. 历史记录索引

`2026-08-07.md`、`2026-08-08.md`、`2026-08-12.md`、`2026-09-12.md`、`2026-09-13.md` 为**当时过程记录**，含已被后续决策覆盖的说法（如「双体系并存」「版本 1.8.x/1.9.0」）。查阅须以本文件 §1–§5 与权威规范为准；不删除原始文件，便于追溯。

## 9. 快速检索

| 想知道 | 看这里 |
|--------|--------|
| 编码 / 命名 / 目录 / 提交 / 分支 / 测试 / 发布规范 | `CONTRIBUTING.md` 对应章节 |
| 文档格式、术语、索引、中英镜像对照 | `docs/README.md`；英文索引 `docs/README.en.md` |
| 当前任务与技术债（唯一来源） | `docs/tasks.md` / `docs/tasks-done.md` |
| 已完成任务归档 | `docs/tasks-done.md` |
| 版本变更历史（按新增/修改/修复分组） | `CHANGELOG.md` |
| 功能现状与流程图 | `docs/功能模块说明.md` |
| 用户操作 / 安装 / 常见问题 | `docs/user-guide.md` / `docs/安装部署指南.md` / `docs/FAQ与故障排除.md` |
| OpenSpec 流程 | `openspec/AGENTS.md` |
| 本机 CI / 推送诊断 | 全局记忆 `ci_diagnostics.md` / `workflow_commits_push.md` |
