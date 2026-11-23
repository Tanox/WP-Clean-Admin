<!-- OPENSPEC:START -->
# 开发者指南（T004）

> **定位**：快速上手入口 + 现有权威文档索引。`CONTRIBUTING.md` 已覆盖贡献流程/规范细节，本指南**不重复**其条款，只回答「新开发者 30 分钟上手」路径。

## ADDED Requirements

### Requirement: 架构认知路径
新开发者 SHALL 按以下顺序阅读：`CONTRIBUTING.md` §0–§4（规范）→ `docs/README.md`（文档索引）→ 本指南 §快速上手（模块地图）。

#### Scenario: 新增一个模块功能
- **WHEN** 开发者需要在 `modules/` 下新增能力
- **THEN** 按 §模块地图定位归属目录与命名空间，遵循类名↔文件名映射（CONTRIBUTING §3.2）与 trait 拆分范式（§4.5）

## 快速上手：模块地图

| 需求 | 位置 | 命名空间 |
|------|------|----------|
| 后台菜单/仪表盘/权限 | `modules/admin/classes/` | `WPCleanAdmin\Modules\Admin\Classes` |
| 设置页（字段/样式/脚本） | `modules/admin/settings/` | `WPCleanAdmin\Modules\Admin\Settings` |
| 缓存/资源/助手 | `modules/utilities/classes/` | `WPCleanAdmin\Modules\Utilities\Classes` |
| 核心启动/模块加载 | `modules/core/classes/` | `WPCleanAdmin\Modules\Core\Classes` |
| 全局函数库 | `includes/wpca-core-*-functions.php` | 全局函数（`wpca_*` 前缀） |

**权威体系说明**：`modules/` 为运行期唯一入口；根 `includes/` 仅余 autoload、函数库、stubs 等 9 个活文件，legacy 类已归档至 `archive/legacy-includes-1.9.12/`（只读，禁止引用）。

## 修改代码的三条硬规则

1. **单文件 ≤200 行**：超出按组合 trait 范式拆分（`class-wpca-xxx-tasks.php` + 主类 `use`；属性留主类，trait 只放方法）。
2. **错误码/日志/校验走 `Helpers`**：错误响应结构、nonce 校验、能力检查不得自造（见 `specs/error-handling/spec.md`）。
3. **版本联动**：改插件版本须同步 `wp-clean-admin.php` 头注释 + `WPCA_VERSION` + `readme.txt` Stable tag（CONTRIBUTING §9.2）。

## 本地验证（当前限制）

- 仓库 CI 提供 `php-lint`（含 PHP 7.4 矩阵）与 `composer test`（PHPUnit）；本机无 PHP 运行时，本地以静态审阅 + IDE 诊断替代，合并前以 CI 结果为准。

## 深入阅读

- 安全审计：`specs/security-audit/spec.md`
- 扩展开发：`specs/extension-api/spec.md` 与 `examples/`（示例代码）
- 原型对照：`prototype/index.html`（语义化 ID 规范见 CONTRIBUTING §3.3）

<!-- OPENSPEC:END -->
