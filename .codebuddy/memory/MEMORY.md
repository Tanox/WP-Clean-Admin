# MEMORY.md — WPCleanAdmin 项目长期记忆

> **整理日期**：2026-11-22　|　**适用基线**：1.9.17
> **用途**：跨会话快速检索核心决策、背景与关键结论。标注「可信度」的条目均已由代码/提交/CI 核实；历史日期文件仅作过程记录，不作为当前事实依据。
> **权威规范**：`CONTRIBUTING.md`（开发规范唯一来源）；**版本历史**：`CHANGELOG.md`。

## 1. 核心决策（按主题）

| 决策 | 结论 | 时间 / 版本 | 可信度 |
|------|------|-------------|--------|
| E-1 架构定论 | `WPCleanAdmin\Modules\*` 为运行期唯一权威体系；旧根 `includes/` 的 115 个 legacy 类已归档至 `archive/legacy-includes-1.9.12/`，只读、不加载、不引用 | 2026-11-05 / 1.9.12–1.9.13 | 已证实（代码 + 提交） |
| E-2 资源抽离 | 活设置页内联 CSS/JS 抽至 `assets/`，由 `Settings_Styles` / `Settings_Scripts` 注入；渲染行为不变 | 2026-11-07 / 1.9.17 | 已证实（代码） |
| A-3 治理拆分 | 拆分 >200 行源文件为任务 trait / 组合 trait；**已收官**：现存超 200 行仅 2 个登记排除项（stubs 775、引导主文件 236） | 2026-11-07 / 1.9.17 | 已证实（原始行数复测） |
| CI 与发布 | `10e4993` 后 CI 全绿，`release.yml` 自动打 tag `v1.9.17` 并发布；phpcs 暂为非阻塞技术债 | 2026-11-08 | 已证实（tag + CI） |
| 安全基线 | AJAX 写操作 nonce + `manage_options` 双校验；输入消毒、输出转义、SQL `prepare`；缓存由 `serialize` 改 JSON；备份删除加 `realpath` 边界 | 1.9.2 起 | 已证实（代码审阅） |
| 版本源 | 插件头 `Version:` + `WPCA_VERSION` 常量；纯文档变更不提升版本，只写 `CHANGELOG.md` `[Unreleased]` | 现行 | 权威规范 §9 |

## 2. 关键事实速查（时效敏感）

| 事实 | 当前值 | 核实时效 | 来源 |
|------|--------|----------|------|
| 插件版本 | 1.9.17 | 2026-11-22 | `wp-clean-admin.php` |
| 最低 PHP / WordPress | 7.4 / 5.0 | 2026-11-22 | 插件头 / `composer.json` |
| 运行时入口 | `wp-clean-admin.php` → `plugins_loaded` → `Modules\Core\Classes\Core` | 2026-11-22 | 引导主文件 |
| 长期分支 | 仅 `main`（无 `develop`） | 2026-11-22 | `CONTRIBUTING.md` §6 |
| >200 行排除项 | `wpca-wordpress-stubs.php`、`wp-clean-admin.php` | 2026-11-22 复测 | 原始行数统计 |
| 原型 | `prototype/` 下 4 个平铺静态文件，无 PHP、无子目录 | 2026-11-22 | 目录实测 |
| 本机 PHP/Composer | 均不可用，无法 `php -l` / `phpunit` | 2026-11-07 复核 | 环境探测 |
| 推送方式 | github.com:443 直连失败；用一次性代理参数推送（见 §4） | 2026-11-08 复核 | 网络实测 |

## 3. 时间线（精简）

| 日期 | 关键事件 |
|------|----------|
| 2026-08-07 | AJAX nonce 字段统一、设置消毒、首批测试与 1.8.2 版本同步 |
| 2026-08-08 | 创建高保真原型与设计规范（历史基线 1.8.3） |
| 2026-08-12 | 大型审查：版本同步 1.8.4、原型 PHP 模板清理、目录边界确立 |
| 2026-09-13 | 1.9.0 修复命名空间冲突、2FA 绕过、权限校验等 P0；PHP 要求升至 7.4 |
| 2026-11-04 | 1.9.2 安全修复（路径遍历、反序列化、输入消毒）与首批 trait 拆分 |
| 2026-11-05 | E-1 归档 legacy 死代码；组合 trait 范式确立（1.9.11–1.9.13） |
| 2026-11-07 | 1.9.17：E-2 资源抽离、A-3 拆分收官、规范文档体系化 |
| 2026-11-08 | CI 全红根因链修复完成，CI 全绿并自动发布 v1.9.17 |
| 2026-11-22 | 规范文档与记忆文档整理（本次） |

## 4. 环境与协作约束（高可信，附鉴别方法）

- **无 PHP 运行时**：`where php` / `where composer` 失败；PHP 改动只能静态审阅 + CI 验证。提交信息标注「待 CI/PHP 环境验证」。
- **git 身份**：本机未配置 `user.name` / `user.email`；既有作者 `Sut <xepinchan@qq.com>`。用一次性参数 `git -c user.name="Sut" -c user.email="xepinchan@qq.com" commit ...`，不改配置。
- **推送代理**：github.com:443 直连超时；`git -c http.proxy=http://192.168.10.100:20172 -c https.proxy=... push origin main`。推送前先经代理 `fetch`，必要时 `rebase`。
- **并发会话**：外部轮次可能在工具调用之间改盘/提交甚至 amend 已推历史；动手前 `git log` / `git status` 实查，提交后 `git reflog` 核对 HEAD。
- **行数口径**：>200 行以**原始行数**为准；PowerShell 用 `(Get-Content $f).Count`，`Measure-Object -Line` 会跳过空行导致漏判。
- **文件现状**：`read_file` / `search_content` 偶有过期缓存；关键判断用 `Get-Content` 或 `git show HEAD:<path>` 复核。

## 5. 历史记录索引

`2026-08-07.md`、`2026-08-08.md`、`2026-08-12.md`、`2026-09-12.md`、`2026-09-13.md` 为**当时过程记录**，含已被后续决策覆盖的说法（如「双体系并存」「版本 1.8.x/1.9.0」）。查阅时须以本文 §1–§2 与权威规范为准；不删除原始文件，便于追溯。

## 6. 快速检索

| 想知道 | 看这里 |
|--------|--------|
| 编码/命名/目录/提交/分支/测试/发布规范 | `CONTRIBUTING.md` 对应章节 |
| 文档格式、术语、索引 | `docs/README.md` |
| 当前任务与技术债 | `docs/tasks.md` / `docs/tasks-done.md` |
| 版本变更历史 | `CHANGELOG.md` |
| OpenSpec 流程 | `openspec/AGENTS.md` |
| 本机 CI / 推送诊断 | 全局记忆 `ci_diagnostics.md` / `workflow_commits_push.md` |
