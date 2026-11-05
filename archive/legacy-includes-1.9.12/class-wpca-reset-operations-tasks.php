<?php
/**
 * WPCleanAdmin Reset Operations Tasks Trait (composite)
 *
 * 组合 trait：聚合 ResetOperationsSettingsTasks 与 ResetOperationsPluginTasks。
 * 消费类（class-wpca-reset.php）仍 `use ResetOperationsTasks;`，零改动。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-reset-operations-settings-tasks.php';
require_once __DIR__ . '/class-wpca-reset-operations-plugin-tasks.php';

/**
 * 重置操作任务（组合 trait）。
 */
trait ResetOperationsTasks {
    use ResetOperationsSettingsTasks;
    use ResetOperationsPluginTasks;
}
