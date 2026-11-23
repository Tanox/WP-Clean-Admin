<!-- OPENSPEC:START -->
# Composer 依赖管理规范（T007）

> **范围**：`wpcleanadmin/composer.json` 的依赖声明、自动加载与脚本约定；依赖 T003（安全审计清单）。

## ADDED Requirements

### Requirement: 依赖分区
`require` 仅放运行期依赖（当前为空——插件不捆绑 vendor 库，保持 WP.org 打包零依赖）；开发工具（phpunit、phpcs、phpstan 等）SHALL 全部置于 `require-dev`。

#### Scenario: 发布打包
- **WHEN** CI 执行发布打包（release.yml）
- **THEN** 不安装/不携带 `require-dev` 依赖产物，包内无 `vendor/` 目录

### Requirement: 自动加载映射
PSR-4 映射 SHALL 与 CONTRIBUTING §3.2 类名→文件名映射一致：`WPCleanAdmin\` → `includes/`；新增 `modules/` 子命名空间时映射 SHALL 与 autoload.php 保持单一来源（以 `includes/autoload.php` 为准，composer autoload 仅作测试环境补充）。

#### Scenario: 命名空间冲突防护
- **WHEN** 新增类导致根与 modules 同名 FQCN
- **THEN** `PluginAutoloadSmokeTest` 冒烟用例须同步更新并通过（回归：1.9.0 Cannot declare class Fatal）

### Requirement: 脚本约定
`composer.json` scripts SHALL 至少提供：`test`（phpunit）、`lint`（php -l 全量）、`phpcs`、`phpcbf`、`phpstan`（T009 接入后）；CI 与本地验证 SHALL 统一经脚本调用，禁止散落裸命令。

#### Scenario: 本地全量自检
- **WHEN** 开发者完成一批改动
- **THEN** 依序执行 `composer lint && composer test && composer phpcs`（有 PHP 环境时）

### Requirement: 版本锁定与安全更新
开发依赖版本 SHALL 以 `composer.lock` 锁定；升级 dev 依赖 SHALL 独立提交并在提交信息标注动机，经 CI 全绿后合入。

#### Scenario: 锁文件与清单一致
- **WHEN** `composer.json` 依赖变更而 lock 未更新
- **THEN** CI 依赖一致性检查（T012 接入后）失败并阻塞合并

<!-- OPENSPEC:END -->
