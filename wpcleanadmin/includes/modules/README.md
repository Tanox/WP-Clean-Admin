# WP Clean Admin 模块结构

> **适用基线**：1.9.16　|　**更新日期**：2026-11-07
> 本文描述 `includes/modules/` 的**运行期唯一权威体系**。完整目录组织、命名与拆分规范见根 [`CONTRIBUTING.md`](../../../CONTRIBUTING.md)，环境命令见根 `DEVELOPMENT.md`。

## 1. 目录结构（实际）

```
includes/modules/
├── admin/
│   ├── classes/       # 后台业务类：仪表盘、菜单管理、菜单定制、用户角色、登录相关
│   └── settings/      # 设置页、字段渲染、样式、校验
├── core/
│   └── classes/       # Core（入口）、Module_Loader（实例化清单）、Error_Handler
└── utilities/
    └── classes/       # Cache、Helpers、Resources、i18n
```

> 早期文档中出现的 `frontend/`、`admin/ajax/` 子目录**不存在**，请勿按该结构创建文件。

## 2. 加载机制

- 入口：`wp-clean-admin.php` 在 `plugins_loaded` 钩子实例化 `WPCleanAdmin\Modules\Core\Classes\Core`。
- `includes/modules/core/classes/class-wpca-module-loader.php` 通过 `safe_init()` 按清单加载 Settings、Error_Handler、`Admin\Classes\*`、`Utilities\Classes\*`；**清单是运行期可达性的判定依据**。
- 类文件由 `includes/autoload.php` 自动加载：类名 kebab-case 映射为 `class-wpca-<kebab>.php`，`Modules` 命名空间段映射到 `includes/modules/` 下对应目录。
- 根 `includes/` 下的 legacy 旧类已于 1.9.12 归档至 `archive/legacy-includes-1.9.12/`，**只读、不加载、不得引用**。

## 3. 添加新模块

1. 在对应域目录创建 `class-wpca-<kebab-case>.php`，命名空间为 `WPCleanAdmin\Modules\<域>\<层>`（如 `WPCleanAdmin\Modules\Admin\Classes`）。
2. 类名使用 PascalCase 且**禁止下划线**（`Menu_Manager` 会被 autoload 转成 `menu--manager` 而加载失败）。
3. 在 `class-wpca-module-loader.php` 的 `safe_init()` 清单中登记。
4. 文件超过 200 行时按 `CONTRIBUTING.md` §4.5 拆为任务 trait 或组合 trait，公开 API 保持不变。
5. 配套 PHPDoc、`@version`、单元测试，并执行 `CONTRIBUTING.md` §7.1 自检清单。

## 4. 命名与风格速查

| 对象 | 规则 |
|------|------|
| 类名 | PascalCase，无下划线 |
| 文件名 | `class-wpca-{kebab-case}.php` |
| 任务 trait | `class-wpca-{模块}-{职责}-tasks.php`，trait 名以 `Tasks` 结尾 |
| 命名空间 | `WPCleanAdmin\Modules\{Admin,Core,Utilities}\{Classes,Settings}` |
| 函数 / 变量 | `wpca_` 前缀 / snake_case |
| 常量 | `WPCA_` 全大写 |
| 文本域 | 统一 `WPCA_TEXT_DOMAIN`，禁止硬编码 `'wp-clean-admin'` |

> 早期版本「类名使用 PascalCase，如 `Menu_Manager`」的示例已废弃，下划线类名是历史缺陷来源。

## 5. 最佳实践

1. 单一职责；超 200 行按职责拆分。
2. 所有 AJAX/表单写操作：nonce + `current_user_can('manage_options')` 双校验。
3. 输入 `sanitize_*`、输出 `esc_*`、SQL `$wpdb->prepare()`。
4. 每个类与方法有 PHPDoc；关键逻辑用中文注释说明「为什么」。
5. 新功能配套单元测试；不得从 `archive/` 复制代码回本目录。

*模块结构自 1.8.0 引入，1.9.13 起定为运行期唯一权威体系；版本变更见根 `CHANGELOG.md`。*
