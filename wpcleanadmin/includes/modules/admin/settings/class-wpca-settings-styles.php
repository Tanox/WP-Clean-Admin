<?php
/**
 * WPCleanAdmin Settings Styles
 *
 * 设置页面内联样式。CSS 源已抽离至 assets/css/wpca-settings.css（E-2 资产），
 * 此处内联包含该资产以保留渲染行为，主类文件不再内联超长 CSS（§4.5）。
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

/**
 * 设置页面内联样式类
 */
class Settings_Styles {

    /**
     * 输出设置页内联样式。
     *
     * CSS 已抽离至 assets/css/wpca-settings.css（E-2），此处内联包含该资产，
     * 既保持渲染行为不变，又避免本文件内联超长 CSS（§4.5）。
     *
     * @return void
     */
    public function render(): void {
        $css_file = defined( 'WPCA_PLUGIN_DIR' ) ? WPCA_PLUGIN_DIR . 'assets/css/wpca-settings.css' : '';

        ?>
        <style type="text/css">
        <?php
        if ( '' !== $css_file && \file_exists( $css_file ) ) {
            include $css_file;
        }
        ?>
        </style>
        <?php
    }
}
