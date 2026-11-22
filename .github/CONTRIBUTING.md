# 贡献指南（GitHub 入口）

> **适用基线**：1.9.17　|　**更新日期**：2026-11-22　|　**状态**：快速参考
> 本文只提供 GitHub 仓库特有说明与规范索引。**所有开发规范（编码、命名、目录、提交、分支、审查、测试、版本与发布）以根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) 为唯一权威来源**，本文不重复其条文。

## 1. 规范索引

| 主题 | 权威位置 |
|------|----------|
| 文档优先级、术语表、格式 | `CONTRIBUTING.md` §0、§10 |
| 项目事实基线与架构定论 E-1 | `CONTRIBUTING.md` §1 |
| 目录组织与硬约束 | `CONTRIBUTING.md` §2 |
| 命名约定、语义化 ID、类名→文件名映射 | `CONTRIBUTING.md` §3 |
| 编码风格、安全基线、>200 行拆分范式 | `CONTRIBUTING.md` §4（安全基线为 §4.4） |
| 提交信息格式 | `CONTRIBUTING.md` §5 |
| 分支管理与直推 `main` 条件 | `CONTRIBUTING.md` §6 |
| 提交前自检、PR 流程、审查要点 | `CONTRIBUTING.md` §7 |
| 测试命令与 CI 矩阵 | `CONTRIBUTING.md` §8 |
| 版本同步清单、SemVer、自动发布 | `CONTRIBUTING.md` §9 |

## 2. GitHub 仓库特有约定

- 唯一长期分支为 `main`；不存在 `develop`。短生命周期分支用 `feature/<简述>`、`fix/<简述>`、`docs/<简述>`、`refactor/<简述>`、`chore/<简述>`。
- PR 标题遵循 `CONTRIBUTING.md` §5；描述填写 [`.github/PULL_REQUEST_TEMPLATE.md`](PULL_REQUEST_TEMPLATE.md)。
- CI 由 `.github/workflows/ci.yml` 执行 `php-lint` / `phpcs` / `phpunit`；`main` 全绿后由 `release.yml` 自动发布（见 `CONTRIBUTING.md` §9.4）。`phpcs` 当前非阻塞，审查者须手动确认其输出。
- 安全要求见 `CONTRIBUTING.md` §4.4「安全基线（强制）」；漏洞报告见 [`.github/SECURITY.md`](SECURITY.md)。
- Community Health Files 驻 `.github/`；**禁止创建 `.github/readme.md`**。

## 3. 环境与快速命令

最低 PHP 7.4 / WordPress 5.0。依赖与命令在插件子目录执行：

```bash
cd wpcleanadmin
composer install
composer test      # PHPUnit
composer phpcs     # WPCS 检查
composer phpcbf    # 自动修复格式
```

本机无 PHP/Composer 运行时，不得声称测试通过；按 `CONTRIBUTING.md` §7.1 在提交信息标注「待 CI/PHP 环境验证」。

## 4. 变更提案

重大功能、破坏性变更或架构调整，先在 `openspec/changes/` 建立提案（`proposal.md` + `tasks.md`），流程见 [`openspec/AGENTS.md`](../openspec/AGENTS.md)。文档索引见 [`docs/README.md`](../docs/README.md)。
