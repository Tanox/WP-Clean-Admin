<?php
/**
 * WPCleanAdmin Performance Resource Tasks Trait (composite)
 *
 * 组合 trait：聚合 PerformanceResourceMinifyTasks 与 PerformanceResourceCombineTasks。
 * 消费类（class-wpca-performance.php）仍 `use PerformanceResourceTasks;`，零改动。
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

require_once __DIR__ . '/class-wpca-performance-resource-minify-tasks.php';
require_once __DIR__ . '/class-wpca-performance-resource-combine-tasks.php';

/**
 * 资源优化任务（组合 trait）。
 */
trait PerformanceResourceTasks {
    use PerformanceResourceMinifyTasks;
    use PerformanceResourceCombineTasks;
}
