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
        // 内联样式由 Settings_Styles 注入（includes assets/css/wpca-settings.css，§4.5）
        $this->styles->render();

        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        ?>
        <script type="text/javascript">
            window.wpcaSaveMessage = <?php echo \json_encode( \__( 'Saving...', $text_domain ) ); ?>;
        <?php
        // E-2 重做（1.9.17）：活设置页 JS 抽离至 assets/js/wpca-settings.js
        $js_file = defined( 'WPCA_PLUGIN_DIR' ) ? WPCA_PLUGIN_DIR . 'assets/js/wpca-settings.js' : '';
        if ( '' !== $js_file && \file_exists( $js_file ) ) {
            include $js_file;
        }
        ?>
        </script>
        <?php
    }

    /**
     * Render styles (delegated to Settings_Styles)
     */
    public function render_styles() {
        $this->styles->render();
    }
}
