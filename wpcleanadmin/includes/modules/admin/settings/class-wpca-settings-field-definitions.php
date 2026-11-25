<?php
/**
 * WPCleanAdmin Settings Field Definitions
 *
 * 承载设置字段的声明数据（id / 标题 / 类型 / 分组），从 Settings_Fields 主类抽取。
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.18
 */

namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置字段声明集合
 */
class Settings_Field_Definitions {

    /**
     * 获取通用（general）分组字段声明
     *
     * @return array
     */
    public static function general_fields(): array {
        return array(
            array(
                'id'          => 'clean_admin_bar',
                'title'       => \__( 'Clean Admin Bar', 'wp-clean-admin' ),
                'type'        => 'checkbox',
                'description' => \__( 'Remove unnecessary items from the admin bar.', 'wp-clean-admin' ),
                'section'     => 'wpca_general_section',
                'group'       => 'general',
            ),
            array(
                'id'          => 'clean_dashboard',
                'title'       => \__( 'Clean Dashboard', 'wp-clean-admin' ),
                'type'        => 'checkbox',
                'description' => \__( 'Remove unnecessary dashboard widgets.', 'wp-clean-admin' ),
                'section'     => 'wpca_general_section',
                'group'       => 'general',
            ),
            array(
                'id'          => 'remove_wp_logo',
                'title'       => \__( 'Remove WP Logo', 'wp-clean-admin' ),
                'type'        => 'checkbox',
                'description' => \__( 'Remove the WordPress logo from the admin bar.', 'wp-clean-admin' ),
                'section'     => 'wpca_general_section',
                'group'       => 'general',
            ),
            array(
                'id'          => 'log_level',
                'title'       => \__( 'Log Level', 'wp-clean-admin' ),
                'type'        => 'select',
                'options'     => array(
                    'debug'    => \__( 'Debug', 'wp-clean-admin' ),
                    'info'     => \__( 'Info', 'wp-clean-admin' ),
                    'notice'   => \__( 'Notice', 'wp-clean-admin' ),
                    'warning'  => \__( 'Warning', 'wp-clean-admin' ),
                    'error'    => \__( 'Error', 'wp-clean-admin' ),
                    'critical' => \__( 'Critical', 'wp-clean-admin' ),
                ),
                'description' => \__( 'Select the minimum log level to record.', 'wp-clean-admin' ),
                'section'     => 'wpca_general_section',
                'group'       => 'general',
            ),
        );
    }
}
