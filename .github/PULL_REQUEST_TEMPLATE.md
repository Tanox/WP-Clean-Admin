<!-- 请填写以下 PR 描述，标题遵循 `<type>: <描述>` 格式 -->
<!-- 完整流程与自检清单见 CONTRIBUTING.md §7，分支约定见 §6 -->

## 变更总结

<!-- 简述本次 PR 做了什么 -->

## 动机

<!-- 为什么要做这次变更？关联了哪个 Issue？（如 Closes #123） -->

## 变更类型

- [ ] `feat` 新功能
- [ ] `fix` 修复 bug
- [ ] `docs` 文档更新
- [ ] `refactor` 重构
- [ ] `perf` 性能优化
- [ ] `style` 代码风格
- [ ] `test` 测试
- [ ] `chore` 构建/依赖/配置
- [ ] `ci` CI/CD

## 测试说明

<!-- 如何验证本次变更？单元测试 / 手动步骤 -->

- [ ] `cd wpcleanadmin && composer test` 通过
- [ ] `cd wpcleanadmin && composer phpcs` 通过
- [ ] 全量 `php -l` 通过
- [ ] 已手动验证功能（如适用）
- [ ] 本机无 PHP 运行时：已在提交信息标注「待 CI/PHP 环境验证」

## 检查清单（对应 `CONTRIBUTING.md` §7.1）

- [ ] 代码遵循 WPCS 3.x 与项目约定；单文件 ≤ 200 行（排除项已登记）
- [ ] 类名无下划线；新 HTML 元素使用语义化 `wpca-` 前缀 id
- [ ] 新增/修改文件头 `@version`、PHPDoc 已同步；版本源与 `CHANGELOG.md` 一致（如需）
- [ ] `README.md` / `README.en.md` / `CHANGELOG.md` / `CONTRIBUTING.md` 等文档已按 §9.2 同步清单检查
- [ ] 未引用 `archive/`；未向 `wpcleanadmin/` 放入非运行时文件
- [ ] 无 `console.log` / `debugger` 残留
- [ ] 安全红线已遵守（nonce + capability、输入 sanitize、输出 esc、SQL prepare、统一 `WPCA_TEXT_DOMAIN`）

## 关联

Closes #
