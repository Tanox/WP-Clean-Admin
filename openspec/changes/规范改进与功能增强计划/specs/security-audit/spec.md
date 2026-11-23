<!-- OPENSPEC:START -->
# 安全审计清单（T003）

> **定位**：本清单是 `CONTRIBUTING.md` §4.4「安全基线（强制）」的**执行检查表**，不重复基线条款本身。每次发布前（Release 流程 §9.4 触发时）与涉及输入/文件/AJAX 的 PR 审查（§7.3）逐项核对。历史漏洞以根 `CHANGELOG.md` 安全条目为回归用例。

## ADDED Requirements

### Requirement: 发布前审计
每次 Release 前 SHALL 逐项核对下方检查表；任一「强制」项不通过即阻塞发布。

#### Scenario: 发布审计通过
- **WHEN** 全部强制项核对通过
- **THEN** 在发布 PR 描述中附勾选记录（核对日期 + 审计人）

## 审计检查表

### 1. 输入与输出（强制）
- [ ] 所有 `$_POST/$_GET/$_REQUEST` 读取前经 `wp_unslash`，入库前经递归消毒（复用 `AJAX\Settings::sanitize_settings` / `Helpers::sanitize_array`，禁止各处自写）
- [ ] 所有输出经 `esc_html` / `esc_attr` / `esc_url` / `wp_kses`；属性值禁止裸 echo
- [ ] 设置保存链路（含菜单项、管理栏 title）保存前递归消毒（回归：1.9.2 修复的存储型 XSS）

### 2. AJAX 与权限（强制）
- [ ] 每个 `wp_ajax_*` 注册对应 nonce 校验 + `current_user_can( 'manage_options' )`（统一网关，禁止裸 handler）
- [ ] nonce 字段名统一（`_wpnonce` / `wpca_ajax_nonce`，见设计规范 §AJAX 网关）

### 3. 文件与路径（强制）
- [ ] 删除/恢复类文件操作：`basename` 剔除目录成分 + `realpath` 边界校验落在目标目录内 + 扩展名白名单（回归：1.9.2 备份删除路径遍历）
- [ ] 禁止对不可信数据使用 `unserialize`（回归：1.9.2 缓存对象注入）；落盘数据仅 JSON

### 4. SQL（强制）
- [ ] 所有 `$wpdb` 查询动态值经 `$wpdb->prepare`；表名/字段名仅来自白名单常量

### 5. 防御性声明
- [ ] 生产 PHP 文件含 `ABSPATH` 守卫
- [ ] 无 `eval` / 无 EXTR_SKIP 之外的 `extract` / 无 `base64_decode` 处理用户输入
- [ ] 无重复的全局/命名空间函数 stub 声明（非 WP 环境下重复声明会 Fatal，回归：测试环境踩坑）

### 6. 依赖与版本
- [ ] `Requires PHP` 与实际语法特性一致（7.4 基线）
- [ ] CI `php-lint` 7.4 矩阵通过

<!-- OPENSPEC:END -->
