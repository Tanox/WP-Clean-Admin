<!-- OPENSPEC:START -->
# OpenSpec 规范与提案指令

> **适用基线**：1.9.17　|　**更新日期**：2026-11-22　|　**状态**：助手指令（OpenSpec 流程）
> 本文仅规定 OpenSpec 需求/设计层文档的编写与变更流程。编码风格、命名、目录、提交、分支、审查、测试与版本发布以根 [`CONTRIBUTING.md`](../CONTRIBUTING.md) 为唯一权威来源；版本变更历史见根 [`CHANGELOG.md`](../CHANGELOG.md)；工程文档索引见 [`docs/README.md`](../docs/README.md)。

## 1. 适用范围与优先级

### 1.1 何时使用本文

- 新增功能、破坏性变更、架构调整或重大性能/安全工作 → 先走 `changes/` 提案流程
- 需求模糊、需要权威需求/设计依据 → 查 `specs/<功能域>/spec.md`
- 编写、审查或整理 OpenSpec 文档 → 遵循本文 §3 格式与 §4 流程

### 1.2 冲突优先级

从高到低：**代码实现** > `CONTRIBUTING.md` > `AGENTS.md`（根）/ `DEVELOPMENT.md` > **本文** > `openspec/specs/` 各规范 > 历史报告与归档文档。文档与代码不一致时以代码为准，并同步修正文档。

## 2. 目录与职责

```
openspec/
├── README.md              # 本目录索引（需求/设计层）
├── project.md             # 项目上下文与技术栈约定
├── AGENTS.md              # 本文：OpenSpec 流程指令
├── CHANGELOG.md           # 仅记录 OpenSpec 文档自身调整
├── specs/                 # 已确立的功能规范（9 个功能域）
│   ├── admin-cleanup/         ├── api/
│   ├── database-management/   ├── diagnostics/
│   ├── menu-customization/    ├── performance-optimization/
│   ├── permission-management/ ├── plugin-architecture/
│   └── security-enhancement/
├── changes/               # 进行中的变更提案
└── archive/               # 已归档提案与历史规范（只读）
```

- `specs/`：描述**应当如何**，是需求与验收依据；运行时实现以 `wpcleanadmin/includes/modules/`（权威体系）为准。
- `changes/`：一次变更一个目录，最小交付物为 `proposal.md` + `tasks.md`，必要时加 `design.md` 与 `specs/` 增量。
- `archive/`：只读历史，不得引用为当前事实，不得修改。

## 3. 文档格式硬约束

1. `openspec/` 下所有规范与提案文档必须包含 `<!-- OPENSPEC:START -->` / `<!-- OPENSPEC:END -->` 标记；该标记**不得**出现在根目录或 `docs/` 文档中。
2. 首部用引用块标注 `适用基线`（当前插件版本）、`更新日期`，必要时标注 `状态`（权威 / 历史快照 / 进行中）。
3. 章节用 ATX 标题连续编号，同一文档内不得重复编号；状态值统一为 `待办` / `进行中` / `已完成` / `阻塞`。
4. 规范类目录用小写连字符；带日期快照用 `名称_YYYYMMDD.md`（无时分）；历史文件保留原名。
5. 路径引用使用仓库根相对路径并写成 `` `wpcleanadmin/includes/modules/...` ``；禁止把已归档的 `includes/class-wpca-*.php` legacy 类写成现行实现。
6. 术语遵循 `CONTRIBUTING.md` §10：权威体系 / `modules/` 体系、归档层 / `archive/`、任务 trait、组合 trait、治理拆分、原型、引导主文件、版本源。
7. UTF-8 无 BOM、行尾 LF、末尾保留换行；表格与代码块格式遵循 `docs/README.md` §2。

## 4. 变更流程

### 4.1 新增功能 / 破坏性变更

1. 在 `changes/<变更名>/` 创建 `proposal.md`（变更原因、范围、影响、验收标准）与 `tasks.md`。
2. 涉及架构或复杂设计时补 `design.md`；涉及接口时补 `specs/<功能>/spec.md` 增量。
3. 提案确认后，将稳定内容写入或更新 `specs/<功能域>/spec.md`。
4. 按 `CONTRIBUTING.md` §2–§8 实施代码变更与自检。
5. 更新根 `CHANGELOG.md`；必要时同步 `README.md` / `README.en.md` / `prototype/`。
6. 提案完成后移入 `openspec/archive/`。

### 4.2 修改现有功能 / 修复缺陷

1. 查对应 `specs/<功能>/spec.md`，按需更新规范。
2. 实施变更并执行 `CONTRIBUTING.md` §7.1 自检清单。
3. 功能变化写入 `CHANGELOG.md`；**纯文档变更不提升插件版本**，仅记入 `[Unreleased]` 段（§9.3）。

## 5. 功能规范编写要求

每份 `specs/<功能>/spec.md` 至少包含：适用范围与优先级、功能需求清单（可验收）、数据流与关键流程、API/AJAX 契约（action、nonce、权限、请求与响应）、安全与性能约束、测试要点、相关文件（**只列 `modules/` 权威体系与现存根文件**）。历史设计基线如含已废弃结构，须在首部显式标注「历史快照，不代表当前状态」。

## 6. 交叉引用

| 需要查什么 | 去哪里 |
|------------|--------|
| 编码 / 命名 / 目录 / 提交 / 分支 / 审查 / 测试 / 发布 | `CONTRIBUTING.md` 对应章节 |
| 术语表与优先级定义 | `CONTRIBUTING.md` §0、§10 |
| 文档编写格式、状态值与索引 | `docs/README.md` §1–§4 |
| 当前任务与技术债 | `docs/tasks.md` / `docs/tasks-done.md` |
| 版本历史 | `CHANGELOG.md` |

## 7. 提交前检查（文档）

- [ ] OPENSPEC 标记、基线头、更新日期齐全且仅在本目录使用标记
- [ ] 未复制 `CONTRIBUTING.md` 的规范条文；交叉引用路径存在
- [ ] 未把归档类、旧原型目录或旧分支写成现行事实
- [ ] 版本号与 `wpcleanadmin/wp-clean-admin.php` 的 `Version:` 一致（或显式标注历史）
- [ ] 纯文档变更未提升插件版本，已写入 `CHANGELOG.md` `[Unreleased]`

<!-- OPENSPEC:END -->
