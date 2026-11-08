# WP Clean Admin 开发指南（快速上手）

> **适用基线**：1.9.17　|　**更新日期**：2026-11-07
> 本文只提供环境搭建、常用命令与原型预览。**完整开发规范（编码风格、命名约定、目录组织、提交格式、分支管理、代码审查、版本与发布）见 [`CONTRIBUTING.md`](CONTRIBUTING.md)**，两者冲突时以 `CONTRIBUTING.md` 为准。

## 1. 环境要求

- PHP 7.4+（CI 矩阵：7.4 / 8.0 / 8.1 / 8.2 / 8.3）
- WordPress 5.0+
- Composer 2（依赖清单位于 `wpcleanadmin/composer.json`）
- 可选：Node.js（仅用于本地静态检查脚本，原型本身无构建步骤）

## 2. 目录速览

```
仓库根/
├── wpcleanadmin/          # 插件运行时代码（唯一可部署目录）
│   ├── wp-clean-admin.php # 引导主文件：常量、autoload、初始化钩子
│   ├── includes/
│   │   ├── autoload.php   # 主自动加载器
│   │   ├── wpca-*.php     # 核心函数库与 CLI 函数桩（9 个活文件）
│   │   ├── ajax/          # WPCleanAdmin\AJAX 处理器
│   │   └── modules/       # ★ 权威业务实现：admin / core / utilities
│   ├── assets/ languages/ tests/
│   └── composer.json  phpunit.xml.dist  phpcs.xml.dist  readme.txt  uninstall.php
├── prototype/             # 纯静态原型：index.html + app.js + 2 个 CSS
├── docs/ openspec/        # 工程文档 / OpenSpec 规范与提案
├── archive/               # 只读 legacy 归档（1.9.12 起，禁止引用）
└── CONTRIBUTING.md  AGENTS.md  README.md  CHANGELOG.md
```

> **架构定论**：`WPCleanAdmin\Modules\*` 是运行期唯一权威体系；根 `includes/` 旧类已归档至 `archive/`，不得在其上继续开发。完整目录约束见 `CONTRIBUTING.md` §2。

## 3. 常用命令

依赖与测试均在**插件子目录**执行：

```bash
cd wpcleanadmin
composer install          # 安装 PHPUnit / WPCS 等开发依赖
composer test             # PHPUnit（bootstrap: tests/bootstrap.php）
composer phpcs            # WordPress Coding Standards 检查
composer phpcbf           # 自动修复格式问题
```

全量语法检查（仓库根执行）：

```bash
find wpcleanadmin -type f -name '*.php' -print0 | xargs -0 -n1 php -l
```

Windows 无 PHP 运行时：本机无法执行上述命令时，按 `CONTRIBUTING.md` §8 在提交信息标注「待 CI/PHP 环境验证」，由 `.github/workflows/ci.yml` 校验。

## 4. 原型预览

原型是**纯静态、无后端、无构建步骤**的高保真 UI，直接用浏览器打开：

```
prototype/index.html
```

四个文件平铺在 `prototype/`：`index.html`（结构）、`wpca-admin.css`（WP 后台外壳）、`wpca-components.css`（组件与设计令牌）、`app.js`（选项卡切换、菜单树、排序、保存演示）。所有数据为静态 mock，保存操作仅前端反馈，**不发送 AJAX、不含 `USE_MOCK` 开关、不需要 nonce**。

- 设计令牌与组件类定义见 `docs/设计规范_20260808.md`（历史基线，路径映射以该文件 §7 当前版本为准）。
- 原型新增交互元素时，须按 `CONTRIBUTING.md` §3.3 使用语义化 `id`，并与插件渲染模板保持一致。
- 原型目录禁止放入 PHP 文件或 `ui/`、`php/` 子目录。

## 5. 添加一个新模块

1. 在 `wpcleanadmin/includes/modules/{admin,core,utilities}/` 对应子目录创建 `class-wpca-<kebab-case>.php`。
2. 命名空间使用 `WPCleanAdmin\Modules\<域>\<层>`；类名 PascalCase 且**不含下划线**（否则 autoload 会生成双连字符文件名而加载失败）。
3. 在 `includes/modules/core/classes/class-wpca-module-loader.php` 的实例化清单中登记（通过 `safe_init()`）。
4. 文件超过 200 行时按 `CONTRIBUTING.md` §4.5 拆为任务 trait。
5. 配套单元测试、PHPDoc、`@version`，并按 §7.1 自检清单逐项确认。

完整流程与审查要求见 `CONTRIBUTING.md` §5–§8。

## 6. 版本与发布

版本号唯一来源为 `wpcleanadmin/wp-clean-admin.php` 的 `Version:` 与 `WPCA_VERSION`。同步清单、语义与发布步骤见 `CONTRIBUTING.md` §9；变更记录写入根 `CHANGELOG.md`。

> 根目录 `update_versions.php` / `update_versions.ps1` 已废弃（硬编码 1.8.0→1.8.1），禁止使用。
