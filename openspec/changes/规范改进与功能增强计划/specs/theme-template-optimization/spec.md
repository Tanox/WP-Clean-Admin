<!-- OPENSPEC:START -->
# 主题模板优化模块规范（T006）

> **设计参考**：[plugin-architecture/design.md](../../plugin-architecture/design.md)；依赖 T001（错误处理）、T002（性能监控）。

## ADDED Requirements

### Requirement: 主题模板路径缓存
对当前主题模板文件的探测（`locate_template` 类调用）SHALL 经 `Cache`（`memory` 后端）按主题目录 + 版本号键控缓存，主题切换/升级时失效。

#### Scenario: 重复探测不重复 IO
- **WHEN** 同一请求内多次探测同一模板
- **THEN** 仅首次产生文件系统调用，后续命中内存缓存

### Requirement: 模板清理不破坏主题
模板优化（移除不必要的 `body_class` 过滤、前端 emoji 资源等）SHALL 仅在 `wpca_settings.performance` 对应开关开启时生效，且提供按主题排除清单。

#### Scenario: 优化开关关闭
- **WHEN** `performance.disable_emojis` 为 falsy
- **THEN** 前端 emoji 资源保持 WordPress 默认行为

### Requirement: 失败安全
模板探测/优化中的任何异常 SHALL 经 `Helpers::handle_exception()` 兜底记录，SHALL NOT 因优化逻辑导致前台致命错误。

#### Scenario: 探测异常降级
- **WHEN** 模板探测抛出异常
- **THEN** 记录 `ERROR_FILE_OPERATION` 并回退 WordPress 默认行为

<!-- OPENSPEC:END -->
