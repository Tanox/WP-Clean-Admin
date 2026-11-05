<?php
/**
 * WPCleanAdmin Extension Registration Tasks Trait (composite)
 *
 * 组合 trait：聚合 ExtensionRegistrationCoreTasks 与 ExtensionRegistrationLifecycleTasks。
 * 消费类（class-wpca-extension-api.php）仍 `use ExtensionRegistrationTasks;`，零改动。
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

require_once __DIR__ . '/class-wpca-extension-registration-core-tasks.php';
require_once __DIR__ . '/class-wpca-extension-registration-lifecycle-tasks.php';

/**
 * 扩展注册任务（组合 trait）。
 */
trait ExtensionRegistrationTasks {
    use ExtensionRegistrationCoreTasks;
    use ExtensionRegistrationLifecycleTasks;
}
