# MEMORY.md — WPCleanAdmin 长期记忆

> 跨会话稳定事实，按需更新并注明日期。

## 版本与文档同步约定（2026-09-13 确立）
- 版本号权威来源：`wpcleanadmin/wp-clean-admin.php` 的 `WPCA_VERSION` 常量（当前 **1.8.5**），插件头 `Version:`、README 徽章、根 `CHANGELOG.md` 须与之保持一致。
- openspec 规范文件的「描述性当前版本」引用（项目概述、`WPCA_VERSION`、`@version`、当前 API 版本、示例插件头 `Version:`）须在发版时同步到当前版本；**历史变更表、提案基线、`见 X.Y.Z 修复记录`、架构设计中「X.Y.Z 确立」等历史引用禁止改动**。
- 原型 `prototype/` 无插件版本串，无需版本同步。
- `update_versions.php` / `update_versions.ps1` 为硬编码旧版（1.8.0→1.8.1）的过时批量工具，不宜直接运行；版本同步建议手工按文件精确替换，避免无关 diff 膨胀。

## 目录边界（2026-09-13 确认）
- 插件运行时代码仅驻 `wpcleanadmin/`；原型 `prototype/`、文档 `docs/`、规范 `openspec/`、根级 README/CHANGELOG/AGENTS 等不放入 `wpcleanadmin/`。
- Community Health Files 驻 `.github/`，**禁止创建 `.github/readme.md`**（全局规则）。
- 仓库 README 默认中文 `README.md`；英文 `README.en.md`。
