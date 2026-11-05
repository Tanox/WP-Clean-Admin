<?php
/**
 * WPCleanAdmin Cleanup Content Tasks Trait (composite)
 *
 * 组合 trait：聚合 CleanupContentPostsTasks 与 CleanupContentShortcodeTasks。
 * 消费类（class-wpca-cleanup.php）仍 `use CleanupContentTasks;`，零改动。
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

require_once __DIR__ . '/class-wpca-cleanup-content-posts-tasks.php';
require_once __DIR__ . '/class-wpca-cleanup-content-shortcode-tasks.php';

/**
 * 内容清理任务（组合 trait）。
 */
trait CleanupContentTasks {
    use CleanupContentPostsTasks;
    use CleanupContentShortcodeTasks;
}
