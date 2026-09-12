# 贡献指南（Contributing Guide）

感谢你考虑为 **WP Clean Admin** 做出贡献！本文件说明参与本项目开发的流程与规范。

## 目录

- [项目简介](#项目简介)
- [开发环境](#开发环境)
- [分支策略](#分支策略)
- [提交信息规范](#提交信息规范)
- [编码规范](#编码规范)
- [版本管理](#版本管理)
- [测试](#测试)
- [Pull Request 流程](#pull-request-流程)
- [文档与规范](#文档与规范)

## 项目简介

WP Clean Admin 是一个 WordPress 后台清理与优化插件，遵循 GPL-2.0-or-later 许可证。
插件运行时代码位于 `wpcleanadmin/`，项目级文档（原型、规范、CHANGELOG 等）位于仓库根目录的
`prototype/`、`openspec/`、`docs/`。详见根目录 `AGENTS.md` 的目录边界约定。

> 注意：**请勿在 `.github/` 目录下创建 `readme.md`**，避免与仓库根目录 `README.md` 重复或产生误导。
> 其他 Community Health Files（CODE_OF_CONDUCT、CONTRIBUTING、SECURITY、SUPPORT、FUNDING 等）可放此目录。

## 开发环境

- PHP >= 7.0
- 本地 WordPress 开发环境（如 Local、WP_ENV、Laravel Valet 等）
- Composer（用于开发依赖与 PSR-4 自动加载）

```bash
composer install        # 安装 phpunit / wpcs 等开发依赖
composer test           # 运行单元测试
composer phpcbf         # 自动修复代码风格
composer phpcs          # 检查代码风格
```

## 分支策略

| 分支          | 用途                         |
| ------------- | ---------------------------- |
| `main`        | 生产环境代码                 |
| `develop`     | 集成分支                     |
| `feature/*`   | 新功能开发                   |
| `fix/*`       | 修复分支                     |
| `hotfix/*`    | 紧急生产修复                 |
| `release/*`   | 发布准备                     |

请在 `develop` 或对应的 `feature/*` / `fix/*` 分支上工作，再向 `main` 或 `develop` 发起 PR。

## 提交信息规范

提交信息须遵循以下格式（不超过 72 字符）：

```
<type>: <description>

[可选正文]

[可选页脚]
```

类型（type）取值：

| 类型      | 描述                 |
| --------- | -------------------- |
| `feat`    | 新增功能             |
| `fix`     | 修复 bug             |
| `docs`    | 文档更新             |
| `style`   | 代码风格调整         |
| `refactor`| 代码重构             |
| `test`    | 测试相关             |
| `chore`   | 构建/依赖/配置变更   |
| `perf`    | 性能优化             |
| `ci`      | CI/CD 配置变更       |
| `revert`  | 回滚提交             |

示例：

```
feat: 新增媒体清理任务模块

- 从 class-wpca-cleanup 抽离媒体清理逻辑
- 复用 Cleanup_Helpers trait

Closes #123
```

## 编码规范

- 遵循 [WordPress 编码规范](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- 使用 PSR-4 自动加载，命名空间前缀 `WPCleanAdmin\` 映射到 `includes/`
- 类名使用 `PascalCase`，方法名使用 `camelCase`，函数/变量使用 `snake_case`
- 所有类与方法必须有 PHPDoc 注释（含 `@param` / `@return`）
- 源文件单文件超过 200 行时按职责拆分为更小模块（仅代码文件，文档不拆）
- 主要 DOM 容器添加语义化 `id`，便于调试与测试
- 提交前确保 `composer phpcs` 与 `composer test` 通过

### 安全红线

- 所有用户输入必须校验与清理（sanitize/escape）
- 数据库查询使用 `$wpdb->prepare()`（SQL 标识符不可参数化，须白名单校验）
- 所有表单与 AJAX 请求使用 nonce 校验
- 检查权限 `current_user_can()`
- 输出到页面的内容必须转义（如 `esc_html()`）
- 代码与日志中不得包含密钥、Token

## 版本管理

本项目采用语义化版本（SemVer，MAJOR.MINOR.PATCH）：

- `PATCH`：修复、文档、重构、样式、配置、依赖等任意修改
- `MINOR`：新功能、向后兼容的变更
- `MAJOR`：破坏性变更

版本号权威来源：插件主文件 `wpcleanadmin/wp-clean-admin.php` 的 `WPCA_VERSION` 常量。
每次修改均须同步升级最小版本号，并同步更新 `package.json`（若存在）、`README` 徽章与页脚、
`CHANGELOG.md`、以及被改动文件的头注释 `// path vX.Y.Z`。

## 测试

- 单元测试基于 PHPUnit，配置见 `phpunit.xml.dist`，引导文件 `tests/bootstrap.php`
- 新增功能或修复 bug 时，请补充或更新对应测试
- 本地运行：`composer test`

## Pull Request 流程

1. `git add` → 提交（遵循提交规范）→ `git push`
2. 向 `main` 或 `develop` 创建 PR
3. PR 标题格式同提交规范；PR 描述包含：变更总结 + 动机 + 测试说明
4. 等待 CI 检查（`test` + `lint` + `build`）通过后由维护者审查合并

PR 审查清单：

- [ ] 测试已添加或更新
- [ ] 无 `console.log` / `debugger` 语句
- [ ] 代码风格（phpcs）全部通过
- [ ] `CHANGELOG.md` 已更新
- [ ] 版本号已同步

## 文档与规范

重大功能、破坏性变更或架构调整，请先在 `openspec/changes/` 下提交变更提案
（`proposal.md` + `tasks.md`），审批后补充 `openspec/specs/` 规范文档。
详见 `AGENTS.md` 的工作流程章节。
