<!-- OPENSPEC:START -->
# WP Clean Admin OpenSpec 文档

本文档包含 WP Clean Admin 项目的所有 OpenSpec 规范文档，用于指导项目开发和维护。

> **适用基线**：1.9.17　|　**更新日期**：2026-11-07
> 本目录仅承载**需求与设计层**规范。开发规范（编码、命名、目录、提交、分支、审查）以根 `CONTRIBUTING.md` 为唯一权威来源；版本变更历史以根 `CHANGELOG.md` 为准；工程文档索引见 `docs/README.md`。
> 下方目录树以本仓库实际文件为准；`archive/` 为只读历史归档，不得作为当前事实依据。

## 文档结构

```
openspec/
├── README.md              # 本文档
├── project.md             # 项目上下文和约定
├── AGENTS.md              # AI 助手指令
├── CHANGELOG.md           # 仅记录 OpenSpec 文档自身调整
├── specs/                 # 功能规范文档（9 个功能域）
│   ├── admin-cleanup/     # 后台清理规范
│   ├── api/               # API 接口规范
│   ├── database-management/ # 数据库管理规范
│   ├── diagnostics/       # 诊断规范
│   ├── menu-customization/  # 菜单定制规范
│   ├── performance-optimization/ # 性能优化规范
│   ├── permission-management/ # 权限管理规范
│   ├── plugin-architecture/ # 插件架构设计
│   └── security-enhancement/ # 安全增强规范
├── changes/               # 变更提案（待处理）
│   └── archive/           # 已完成的变更
└── archive/               # 归档的旧文档
```

## 快速开始

### 新增功能

1. 在 `changes/` 目录下创建变更文件夹
2. 编写 `proposal.md` 描述变更内容
3. 创建 `tasks.md` 规划实施任务
4. 审批后创建 `specs/` 目录下的规范文档

### 查看规范

- 查阅 `project.md` 了解项目整体上下文
- 查看 `specs/` 目录了解各功能模块的详细规范
- 参考 `AGENTS.md` 了解 AI 助手工作指南

## 文档约定

### 命名规范

- 规范文档使用小写字母和连字符
- 带日期快照用 `名称_YYYYMMDD.md`，历史文件保留原名
- 变更提案使用描述性名称

### 文档格式

`openspec/` 目录下的所有规范与提案必须包含 `<!-- OPENSPEC:START -->` 和 `<!-- OPENSPEC:END -->` 标记；根目录与 `docs/` 文档不得使用。

### 版本管理

插件版本语义与同步清单以根 `CONTRIBUTING.md` §9 为唯一权威；纯文档变更不提升插件版本，仅写入根 `CHANGELOG.md` 的 `[Unreleased]` 段。

## 相关链接

- GitHub: https://github.com/Tanox/WP-Clean-Admin
- 项目文档: https://github.com/Tanox/WP-Clean-Admin/tree/main/openspec

## 许可证

本文档遵循 GPLv2 或更高版本许可证。

<!-- OPENSPEC:END -->

