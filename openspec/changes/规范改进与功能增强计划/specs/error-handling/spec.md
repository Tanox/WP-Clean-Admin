<!-- OPENSPEC:START -->
# 错误处理规范（T001）

> **权威错误码来源**：`modules/utilities/classes/class-wpca-helpers.php` 中的 `WPCA_Errors` 抽象类（单一来源，禁止在其他文件重复定义错误码常量）。

## 设计参考

### 架构设计
- **详细设计**: [plugin-architecture/design.md](../../specs/../plugin-architecture/design.md)
- **实现基座**: `Helpers`（组合 `HelpersErrorTasks` / `HelpersLoggingTasks` / `HelpersValidationTasks`）

## 错误码总表（唯一权威）

| 常量 | 值 | 语义 | 典型场景 |
|------|-----|------|----------|
| `ERROR_NONE` | 0 | 无错误 | 成功响应占位 |
| `ERROR_DATABASE` | 1001 | 数据库错误 | `$wpdb` 查询失败、SQLSTATE 异常 |
| `ERROR_PERMISSION` | 1002 | 权限不足 | `current_user_can` 未通过 |
| `ERROR_INVALID_INPUT` | 1003 | 输入非法 | 参数缺失/格式错误 |
| `ERROR_FILE_OPERATION` | 1004 | 文件操作失败 | 读写备份/缓存文件失败 |
| `ERROR_AJAX` | 1005 | AJAX 请求错误 | AJAX handler 兜底失败 |
| `ERROR_VALIDATION` | 1006 | 设置校验失败 | 批量字段校验未通过 |
| `ERROR_AUTH` | 1007 | 认证失败 | nonce 校验失败、2FA 未通过 |
| `ERROR_UNKNOWN` | 9999 | 未知错误 | 兜底 |

## ADDED Requirements

### Requirement: 统一错误响应结构
所有失败路径 SHALL 通过 `Helpers::create_error_response()` 构建响应，结构固定为 `{ success: false, error_code, message, data }`；禁止各 handler 自造结构。

#### Scenario: AJAX handler 返回错误
- **WHEN** AJAX 处理器校验失败或执行异常
- **THEN** 通过 `Helpers::create_error_response( $code, $message, $data )` 构建响应
- **AND** `error_code` 必须取自 `WPCA_Errors` 常量，不得使用裸数字

#### Scenario: AJAX 校验类失败直接终止
- **WHEN** nonce 或权限校验失败
- **THEN** 通过 `Helpers::handle_ajax_error()` 输出 JSON 并终止请求（内部复用 `create_error_response`）
- **AND** HTTP 层不返回 200 之外的裸文本

### Requirement: 异常到错误码的映射
未捕获异常 SHALL 经 `Helpers::handle_exception()` 归类：含 `SQLSTATE` → `ERROR_DATABASE`；含 `permission`/`capability` → `ERROR_PERMISSION`；含 `file`/`upload` → `ERROR_FILE_OPERATION`；其余沿用默认码。

#### Scenario: 数据库异常归类
- **WHEN** 异常消息包含 `SQLSTATE`
- **THEN** 映射为 `ERROR_DATABASE` 并记录日志后返回标准错误响应

### Requirement: 全局错误与异常处理器
`Helpers::register_error_handler()` SHALL 注册 `custom_error_handler`（按 `error_reporting()` 过滤、记录后返回 true）与 `custom_exception_handler`（记录消息与调用栈）。

#### Scenario: PHP 警告被吞并记录
- **WHEN** 运行期产生被抑制级别的警告
- **THEN** `custom_error_handler` 依 `error_reporting() & $errno` 判定后记录日志并返回 true，不中断请求

### Requirement: 日志上下文规范
日志 SHALL 经 `Helpers::log()`（仅 `WP_DEBUG` 开启时落盘）；结构化上下文用 `log_error()` / `log_success()`，上下文数组以 JSON 追加；禁止散落裸 `error_log()` 调用。

#### Scenario: 带上下文的错误日志
- **WHEN** 业务代码调用 `log_error( $msg, $context, $code )`
- **THEN** 输出形如 `Error [<code>]: <msg> | Context: {...}` 的日志行
- **AND** `WP_DEBUG` 关闭时不产生任何输出

### Requirement: 错误消息国际化
面向用户的错误消息 SHALL 使用 `WPCA_Errors` 对应的 `get_error_message()` 文案或 `\__( ... , WPCA_TEXT_DOMAIN )` 包裹，禁止硬编码未翻译字符串。

#### Scenario: 未知错误码回退
- **WHEN** 传入未登记的错误码
- **THEN** `get_error_message()` 回退返回 `ERROR_UNKNOWN` 对应文案

<!-- OPENSPEC:END -->
