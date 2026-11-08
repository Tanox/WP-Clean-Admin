<?php
/**
 * WPCleanAdmin Settings Field Renderers
 *
 * 承载各类型设置字段的渲染方法，从 Settings_Fields 主类抽取。
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-settings-field-renderers-input-tasks.php';
require_once __DIR__ . '/class-wpca-settings-field-renderers-extra-tasks.php';

/**
 * 字段渲染器类
 */
class Settings_Field_Renderers {

    use SettingsFieldRenderersInputTasks;
    use SettingsFieldRenderersExtraTasks;
}
