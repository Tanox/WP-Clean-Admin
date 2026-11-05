<?php
/**
 * WPCleanAdmin Settings Registration Tasks Trait (composite)
 *
 * 组合 trait：聚合 SettingsRegistrationSectionsTasks 与 SettingsRegistrationFieldsTasks。
 * 消费类（class-wpca-settings.php）仍 `use SettingsRegistrationTasks;`，零改动。
 * 公开方法 register_settings() 保持签名不变（内部编排 sections / fields 注册）。
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

require_once __DIR__ . '/class-wpca-settings-registration-sections-tasks.php';
require_once __DIR__ . '/class-wpca-settings-registration-fields-tasks.php';

/**
 * 设置注册任务（组合 trait）。
 */
trait SettingsRegistrationTasks {
    use SettingsRegistrationSectionsTasks;
    use SettingsRegistrationFieldsTasks;
}
