<?php
/**
 * WPCleanAdmin Settings Class
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

require_once __DIR__ . '/class-wpca-settings-page.php';
require_once __DIR__ . '/class-wpca-settings-registration-tasks.php';
require_once __DIR__ . '/class-wpca-settings-render-tasks.php';
require_once __DIR__ . '/class-wpca-settings-enqueue-tasks.php';

class Settings {

    use SettingsRegistrationTasks;
    use SettingsRenderTasks;
    use SettingsEnqueueTasks;

    /** @var self|null */
    private static $instance = null;

    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    public function init() {
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
            \add_action( 'admin_init', array( $this, 'register_settings' ) );
            \add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }
    }
}
