<?php
/**
 * WPCleanAdmin Settings Scripts Class
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.17
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */


namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-settings-styles.php';

/**
 * Settings Scripts class (renders JS + delegates CSS to Settings_Styles)
 */
class Settings_Scripts {

    /**
     * 内联样式渲染器
     *
     * @var Settings_Styles
     */
    private $styles;

    private static $instance = null;

    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->styles = new Settings_Styles();
    }

    /**
     * Render settings page scripts and styles
     */
    public function render_scripts() {
        // 设置页 CSS 已抽离至 assets/css/wpca-settings.css，由 Settings_Styles::render()
        // 经 include 注入（§4.5）；设置页 JS 由 SettingsEnqueueTasks 经 wp_enqueue_script
        // 标准加载（含 wp_localize_script 本地化），此处不再内联/重复输出 JS
        $this->styles->render();
    }

    /**
     * Render styles (delegated to Settings_Styles)
     */
    public function render_styles() {
        $this->styles->render();
    }
}
