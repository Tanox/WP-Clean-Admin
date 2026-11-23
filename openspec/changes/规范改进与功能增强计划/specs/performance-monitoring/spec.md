<!-- OPENSPEC:START -->
# 性能监控规范（T002）

> **实现基座**：`modules/utilities/classes/class-wpca-cache.php`（组合 `CacheApiTasks` / `CacheMemoryTasks` / `CacheDatabaseTasks` / `CacheFileTasks`）；设置项 `wpca_settings.performance.*`。

## ADDED Requirements

### Requirement: 三级缓存统一入口
缓存读写 SHALL 仅经 `Cache::get/set/delete/clear` 分发（type ∈ `memory` / `database` / `file`），禁止业务代码直连后端私有方法或自建缓存键。

#### Scenario: 文件缓存写入
- **WHEN** 调用 `Cache::set( $key, $value, $ttl, 'file' )`
- **THEN** 内容以 `wp_json_encode` 写入 `WPCA_PLUGIN_DIR/cache/md5(key).cache`
- **AND** 目录不存在时经 `wp_mkdir_p` 创建

#### Scenario: 过期与旧格式清理
- **WHEN** 文件缓存反序列化失败或已过期
- **THEN** 直接删除该文件并返回默认值（兼容旧 PHP 序列化格式安全丢弃）

### Requirement: 缓存安全边界
缓存文件内容 SHALL 仅使用 JSON 编解码（`wp_json_encode` / `json_decode`，数组形态），SHALL NOT 使用 PHP `serialize`/`unserialize` 处理任何落盘数据（防对象注入）。

#### Scenario: 篡改的缓存文件
- **WHEN** 缓存文件被替换为 PHP 序列化 payload
- **THEN** `json_decode` 返回非数组 → 文件被删除并返回默认值，不触发对象实例化

### Requirement: 缓存可配置性
`cache_enabled` 与 `cache_expiration` SHALL 从 `wpca_settings.performance` 读取，`expiration <= 0` 回退 3600 秒；运行期可通过 `set_cache_enabled()` / `set_cache_expiration()` 调整。

#### Scenario: 设置禁用缓存
- **WHEN** `performance.cache_enabled` 为 falsy
- **THEN** `get/set` 直接返回默认值 / false，不触碰任何后端

### Requirement: 数据库缓存键空间
数据库缓存键 SHALL 统一使用 `wpca_cache_` 前缀写入 options 表（autoload=false）；清空时按前缀 LIKE 批量删除。

#### Scenario: 清空数据库缓存
- **WHEN** 调用 `Cache::clear( 'database' )`
- **THEN** 仅删除 `wpca_cache_%` 前缀的选项，不影响业务设置

### Requirement: 性能监控指标（规划）
模块 SHALL 暴露缓存命中/写入/清空计数供诊断页展示（诊断数据经 `Diagnostics` 模块聚合）；监控数据本身 SHALL 经 `Cache::set(..., 'memory')` 短期暂存，SHALL NOT 写入文件后端形成放大开销。

#### Scenario: 诊断页读取缓存指标
- **WHEN** 管理员打开诊断页
- **THEN** 展示自加载以来的命中/写入/清空计数与当前 `cache_enabled`/`cache_expiration` 配置

<!-- OPENSPEC:END -->
