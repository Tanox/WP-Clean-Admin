<!-- OPENSPEC:START -->
# 项目上下文

> **适用基线**：1.9.15　|　**更新日期**：2026-11-07
> 本文为 OpenSpec 需求/设计层的项目上下文。**开发规范（编码风格、命名、目录、提交、分支、审查）以根 `CONTRIBUTING.md` 为唯一权威来源；版本变更历史以根 `CHANGELOG.md` 为准**；两者与本文冲突时以权威来源为准。
> 架构定论：`WPCleanAdmin\Modules\*` 为运行期唯一权威体系，根 `includes/` legacy 类已归档至 `archive/`（只读）。

## 目的
WP Clean Admin 是一个全面的 WordPress 后台清理和优化插件，旨在帮助管理员清理和优化 WordPress 后台，提高网站性能和管理效率。

## 技术栈
- PHP 7.0+
- WordPress 5.0+
- MySQL 5.6+
- JavaScript (ES6+)
- CSS3

## 项目约定

### 代码风格
> 完整规范见根 `CONTRIBUTING.md` §3–§4，以下为摘要。

- 采用 WordPress 编码规范（WPCS 3.x，`phpcs.xml.dist`）
- 类名 PascalCase 且**禁止下划线**（下划线会被 autoload 转成双连字符而加载失败）
- 函数 / 变量 / 参数 snake_case，全局函数以 `wpca_` 为前缀
- 常量全大写下划线，以 `WPCA_` 为前缀
- 方法名沿用所在类既有风格，同类内保持一致
- 缩进 4 个空格；文件编码 **UTF-8 无 BOM**；行尾 Unix LF
- PHP 7.4+ 兼容，禁止 PHP 8.0+ 专有语法

### 架构模式
- 模块化设计：功能划分为独立模块，便于维护和扩展
- 单例模式：核心类和功能模块使用单例模式
- 钩子机制：深度集成 WordPress 钩子系统
- 面向对象设计：采用面向对象编程范式
- 依赖注入：通过核心类初始化和协调其他模块

### 测试策略
- 单元测试：对核心功能进行单元测试
- 集成测试：测试模块之间的集成
- 手动测试：验证用户界面和功能完整性
- 兼容性测试：测试不同 WordPress 版本和主题的兼容性
- 代码覆盖率：确保代码覆盖率不低于 80%

### Git 工作流程
> 完整规范见根 `CONTRIBUTING.md` §5–§7。

- **`main` 是唯一长期分支**，仓库不存在 `dev` / `develop` 分支
- 短生命周期分支：`feature/<简述>`、`fix/<简述>`、`docs/<简述>`、`refactor/<简述>`、`chore/<简述>`
- 涉及公开 API、数据库结构、安全策略的变更走 PR 流程；日常小改动可直接提交 `main` 并推送
- 提交消息格式：`<type>: <描述>`（type 见 `CONTRIBUTING.md` §5），如 `fix: 修复 AJAX nonce 校验`

## 领域上下文
WordPress 插件开发领域，专注于后台管理优化和性能提升。插件通过 WordPress 钩子系统扩展 WordPress 功能，无需修改核心文件。

## 重要约束
- 不修改 WordPress 核心文件
- 兼容 WordPress 5.0+ 和 PHP 7.0+
- 遵循 WordPress 安全最佳实践
- 保持插件体积小，加载速度快
- 遵循 WordPress 插件开发规范
- 提供完整的国际化支持

## 外部依赖
- WordPress 核心功能
- MySQL 数据库
- PHP 标准库
- WordPress 钩子系统
- WordPress 函数和类

## 目录结构

### 插件主目录结构（当前 1.9.15）
```
wpcleanadmin/
├── wp-clean-admin.php         # 引导主文件（常量、autoload、初始化钩子）
├── includes/
│   ├── autoload.php           # 主自动加载器
│   ├── wpca-wordpress-stubs.php      # CLI/PHPUnit 环境 WP 函数桩
│   ├── wpca-core-functions.php       # 核心函数库入口
│   ├── wpca-core-{admin,path,settings,util}-functions.php
│   ├── class-wpca-helpers.php        # 兼容层助手类
│   ├── ajax/                 # WPCleanAdmin\AJAX 处理器
│   └── modules/              # ★ 权威业务实现
│       ├── admin/            # classes / settings
│       ├── core/             # classes（Core、Module_Loader、Error_Handler）
│       └── utilities/        # classes（Cache、Helpers、Resources、i18n）
├── assets/css/ assets/js/    # 插件后台样式与脚本
├── languages/                # .pot / .po / .mo
├── tests/                    # PHPUnit 测试
├── composer.json  phpunit.xml.dist  phpcs.xml.dist
└── readme.txt  uninstall.php
```
> 完整目录约束见根 `CONTRIBUTING.md` §2；归档的 legacy 代码位于仓库根 `archive/`（只读）。

### OpenSpec 目录结构
```
openspec/
├── project.md              # 项目约定
├── specs/                  # 当前规范 - 已构建的内容
│   └── [capability]/       # 单个聚焦功能
│       ├── spec.md         # 需求和场景
│       └── design.md       # 技术模式
├── changes/                # 提案 - 应该变更的内容
│   ├── [change-name]/
│   │   ├── proposal.md     # 原因、内容、影响
│   │   ├── tasks.md        # 实施清单
│   │   ├── design.md       # 技术决策（可选）
│   │   └── specs/          # 增量变更
│   │       └── [capability]/
│   │           └── spec.md # ADDED/MODIFIED/REMOVED
│   └── archive/            # 已完成的变更
└── archive/                # 归档的旧文档
```

## 核心功能模块

### 1. 核心模块 (Core)
- 插件初始化和模块协调
- 版本管理和激活/停用处理
- 默认设置初始化

### 2. 设置管理模块 (Settings)
- 管理插件的所有设置
- 提供设置页面和表单处理
- 设置验证和保存

### 3. 仪表盘模块 (Dashboard)
- 优化 WordPress 仪表盘
- 提供插件的主要管理界面
- 显示系统信息和统计数据
- 隐藏不需要的仪表盘小工具

### 4. 清理模块 (Cleanup)
- 提供数据库、媒体、评论和内容清理功能
- 清理过期的transients
- 清理孤儿元数据
- 清理过期的cron事件
- 清理孤儿媒体和未使用的媒体
- 清理垃圾评论、回收站评论和未批准评论
- 清理未使用的短代码和空帖子

### 5. 性能优化模块 (Performance)
- 优化 WordPress 网站性能
- 禁用不必要的功能（Emojis、XML-RPC、REST API、Heartbeat）
- 优化数据库
- 清理Transients
- 提供缓存清理功能
- 提供性能统计信息

### 6. 权限管理模块 (Permissions)
- 管理用户权限和访问控制
- 过滤用户权限
- 检查用户功能访问权限
- 限制管理后台访问
- 限制特定管理页面访问

### 7. 用户角色模块 (User_Roles)
- 管理用户角色和权限
- 允许创建、编辑和删除角色
- 提供角色权限编辑器

### 8. 菜单管理模块 (Menu_Manager)
- 管理 WordPress 后台菜单结构
- 提供菜单信息和结构

### 9. 菜单定制模块 (Menu_Customizer)
- 自定义 WordPress 后台菜单和管理栏
- 自定义菜单顺序
- 自定义菜单项目（隐藏、修改标题、图标和位置）
- 自定义子菜单项目（隐藏、修改标题）
- 自定义管理栏项目（隐藏、修改标题）
- 提供设置导出/导入功能

### 10. 登录页面模块 (Login)
- 自定义 WordPress 登录页面
- 自定义登录页面样式
- 增强登录页面安全性

### 11. 数据库管理模块 (Database)
- 管理和优化 WordPress 数据库
- 清理数据库
- 优化数据库
- 备份数据库
- 恢复数据库

### 12. 资源管理模块 (Resources)
- 管理和优化 WordPress 资源
- 优化 CSS 和 JavaScript 加载
- 处理静态资源

### 13. 重置模块 (Reset)
- 提供插件设置的重置功能
- 允许重置特定模块或全部设置

### 14. AJAX 模块 (AJAX)
- 处理插件的 AJAX 请求
- 提供 AJAX 回调函数

### 15. 国际化模块 (i18n)
- 处理插件的国际化和本地化
- 加载翻译文件

### 16. 扩展 API 模块 (Extension_API)
- 提供插件扩展 API
- 允许第三方扩展插件功能

### 17. Composer 集成模块 (Composer)
- 提供 Composer 依赖管理
- 处理依赖冲突和版本管理

### 18. Elementor 集成模块 (Elementor)
- 集成和优化 Elementor 页面构建器
- 清理 Elementor 缓存和数据

### 19. 主题模板模块 (Theme_Templates)
- 管理和优化主题模板
- 检测和清理未使用的主题文件

## 安全设计

### 输入验证
- 所有用户输入必须经过验证：使用 WordPress 验证函数
- 验证类型：字符串、数字、邮箱、URL 等
- 验证函数：`sanitize_text_field()`, `sanitize_email()`, `sanitize_url()`, `absint()` 等

### 输出转义
- 所有输出到页面的内容必须转义：防止 XSS 攻击
- 转义函数：`esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()` 等

### SQL 注入防护
- 使用 `$wpdb->prepare()` 处理所有 SQL 查询：防止 SQL 注入
- 参数绑定：将用户输入作为参数绑定，而不是直接插入 SQL

### CSRF 防护
- 使用 nonce 验证所有表单提交：防止 CSRF 攻击
- 函数：`wp_nonce_field()`, `wp_verify_nonce()`

### 权限检查
- 所有管理操作必须检查权限：防止未授权访问
- 函数：`wpca_current_user_can()`, `current_user_can()`

### 安全头部
- 添加安全 HTTP 头部：增强网站安全性
- 头部：X-Frame-Options, X-XSS-Protection, X-Content-Type-Options, Strict-Transport-Security, Content-Security-Policy

## 兼容性

### WordPress 版本兼容性
- 最低版本：WordPress 5.0+
- 兼容性检查：使用 `function_exists()` 检查函数是否存在
- 功能降级：根据 WordPress 版本提供不同功能

### PHP 版本兼容性
- 最低版本：PHP 7.0+
- 兼容性检查：使用 `function_exists()` 检查函数是否存在
- 语法兼容：使用兼容 PHP 7.0+ 的语法

### 浏览器兼容性
- 支持浏览器：Chrome, Firefox, Safari, Edge
- 兼容性处理：使用兼容的 HTML, CSS, JavaScript
- 响应式设计：支持不同屏幕尺寸

### 主题兼容性
- 兼容性处理：与主流 WordPress 主题兼容
- 样式隔离：使用独特的 CSS 类名，避免样式冲突
- 钩子集成：提供钩子供主题扩展

## 部署和维护

### 插件安装
- 自动安装：通过 WordPress 后台安装
- 手动安装：上传 ZIP 文件或 FTP 上传
- 命令行安装：使用 WP CLI 安装

### 版本管理
- 自动更新：支持 WordPress 自动更新
- 手动更新：上传新版本 ZIP 文件
- 版本控制：使用 Git 进行版本控制

### 错误处理
- 日志记录：使用 `wpca_log()` 函数记录日志
- 异常处理：使用 `try/catch` 块处理异常
- 管理通知：使用 `wpca_admin_notice()` 函数显示通知

### 数据迁移
- 设置迁移：支持设置的导入和导出
- 数据库迁移：支持数据库备份和恢复
- 升级迁移：处理版本升级时的数据迁移

## 开发指南

> **本节为摘要，完整且权威的规范见根 `CONTRIBUTING.md`**（编码风格 §4、命名 §3、目录 §2、提交 §5、分支 §6、审查 §7、测试 §8、版本 §9）。以下仅列与本文上下文相关的要点。

### 代码规范
- 遵循 WordPress 编码规范（WPCS 3.x）；PHP 7.4+ 兼容
- 类名 PascalCase 且禁止下划线；函数/变量 snake_case（全局函数 `wpca_` 前缀）
- 常量 `WPCA_` 全大写；文件编码 UTF-8 无 BOM、行尾 LF

### 命名空间
- 根 `WPCleanAdmin`；AJAX 处理器 `WPCleanAdmin\AJAX`
- **运行期权威体系** `WPCleanAdmin\Modules\{Admin,Core,Utilities}\{Classes,Settings}`
- 根 `includes/` 仅保留自动加载器、核心函数库、函数桩与兼容层助手类

### 自动加载
- 由 `includes/autoload.php` 注册（类名 kebab-case → `class-wpca-<kebab>.php`）；引导主文件另注册一个 fallback
- `Modules` 命名空间段映射到 `includes/modules/` 对应目录；`AJAX` 段映射到 `includes/ajax/<kebab>-ajax.php`
- 类名含下划线会生成双连字符文件名导致类不可加载，属历史缺陷，禁止使用
- `composer.json` 的 PSR-4 声明 `WPCleanAdmin\ => includes/` 仅用于开发工具，运行时以插件自带 autoloader 为准

### 测试
- 编写单元测试；提案目标为覆盖率 ≥ 80%（当前未达成，见 `docs/tasks.md` D 节）
- 提交前运行 `composer test`、`composer phpcs` 与全量 `php -l`
- 本机无 PHP 运行时不得声称测试通过，须标注「待 CI/PHP 环境验证」

### 文档
- 文档与代码同步更新
- 为每个功能模块编写规范文档
- 为 API 函数编写文档
- 为类和方法编写 PHPDoc 注释

## 贡献指南

### 贡献方式
- Fork 仓库
- 创建功能分支
- 提交代码
- 创建 Pull Request

### 代码要求
- 遵循项目编码规范
- 编写测试用例
- 更新文档
- 遵循提交信息格式

### 审查流程
- 代码审查
- 测试验证
- 合并到 develop 分支
- 定期发布到 main 分支

## 版本历史

> **历史存档（截至 1.8.1）**：以下条目为早期版本记录，保留作为上下文，**不代表当前版本**。
> 完整、持续更新的版本变更历史以根 `CHANGELOG.md` 为唯一来源（当前 1.9.15）。

### [1.8.1] - 2026-05-17
- 修复了诊断模块 AJAX 类引用错误
- 添加了完整的诊断模块翻译文件（支持中文和英文）
- 在 openspec 中添加了诊断模块的完整规范文档
- 优化了模块化架构的一致性和完整性

### [1.8.0] - 2026-01-02
- 新增了主题模板模块
- 增强了Elementor集成功能
- 优化了Composer依赖管理
- 完善了扩展API
- 修复了多处安全漏洞
- 优化了性能
- 更新了语言文件
- 完善了文档

### [1.7.15] - 2025-11-29
- 修复了后台菜单隐藏功能的兼容性问题
- 修复了数据库优化功能的错误
- 优化了插件的加载性能
- 改进了登录页面样式
- 更新了语言文件
- 优化了代码结构

### [1.7.14] - 2025-11-20
- 新增了用户界面自定义功能
- 新增了菜单组创建功能
- 修复了仪表盘小工具隐藏功能
- 修复了设置页面的错误
- 优化了后台界面的响应式设计
- 改进了插件的错误处理机制

### [1.7.13] - 2025-11-10
- 新增了数据库备份和恢复功能
- 新增了登录尝试限制功能
- 修复了性能优化模块的计时问题
- 修复了菜单定位功能的错误
- 优化了插件的资源加载
- 改进了用户体验

### [1.7.12] - 2025-11-01
- 新增了安全增强功能
- 新增了 XML-RPC 禁用选项
- 修复了后台清理功能的错误
- 修复了用户权限管理功能
- 优化了插件的代码结构
- 改进了文档

### [1.7.11] - 2025-10-20
- 新增了菜单定位功能
- 新增了性能优化模块
- 修复了数据库管理功能的错误
- 修复了登录页面样式问题
- 优化了插件的加载速度
- 改进了界面设计

### [1.7.10] - 2025-10-10
- 新增了数据库管理功能
- 新增了用户权限管理系统
- 修复了后台清理功能的兼容性问题
- 修复了仪表盘小工具隐藏功能
- 优化了插件的整体性能
- 改进了错误处理

### [1.7.9] - 2025-09-25
- 新增了仪表盘小工具隐藏功能
- 新增了后台头部和底部清理功能
- 修复了菜单隐藏功能的错误
- 修复了插件更新检查功能
- 优化了插件的设置界面
- 改进了用户体验

### [1.7.8] - 2025-09-15
- 新增了后台菜单隐藏功能
- 新增了登录页面优化功能
- 修复了插件的兼容性问题
- 修复了语言文件加载问题
- 优化了插件的代码结构
- 改进了文档

### [1.7.7] - 2025-09-05
- 新增了插件设置页面
- 新增了多语言支持
- 修复了插件的初始化问题
- 修复了资源加载错误
- 优化了插件的性能
- 改进了界面设计

### [1.7.6] - 2025-08-25
- 新增了自动加载功能
- 新增了插件核心架构
- 修复了插件的缓存问题
- 修复了文件路径问题
- 优化了插件的代码组织
- 改进了开发流程

### [1.7.5] - 2025-08-15
- 首次发布 WP Clean Admin 插件
- 实现了基本的后台清理功能
- 支持 WordPress 5.0+
- 修复了初始版本的一些小问题
- 优化了插件的初始架构
- 改进了用户体验

### [1.0.0] - 2025-08-01
- 项目初始版本
- 核心功能设计
- 框架代码实现

## 许可证

WP Clean Admin 插件遵循 GPLv2 或更高版本许可证。

<!-- OPENSPEC:END -->
