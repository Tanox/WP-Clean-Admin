<?php
/**
 * WPCleanAdmin Diagnostics Class
 *
 * Singleton entry point for the diagnostics subsystem. Check registration,
 * execution and accessors live here; the individual diagnostic checks are
 * provided by the traits composed below.
 *
 * @package WPCleanAdmin
 * @version 1.8.15
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.0
 */
namespace WPCleanAdmin;

require_once __DIR__ . '/wpca-wordpress-stubs.php';

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Diagnostics {

    use DiagnosticsRegistrationTasks;
    use DiagnosticsRunnerTasks;
    use DiagnosticsServerCheckTasks;
    use DiagnosticsConflictCheckTasks;
    use DiagnosticsSecurityCheckTasks;

    private static $instance = null;

    private $checks = array();

    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    public function init(): void {
        $this->register_checks();
    }

    public function get_checks(): array {
        return $this->checks;
    }

    public function get_categories(): array {
        $categories = array(
            'core' => __( 'Core', WPCA_TEXT_DOMAIN ),
            'server' => __( 'Server', WPCA_TEXT_DOMAIN ),
            'database' => __( 'Database', WPCA_TEXT_DOMAIN ),
            'security' => __( 'Security', WPCA_TEXT_DOMAIN ),
            'performance' => __( 'Performance', WPCA_TEXT_DOMAIN ),
            'plugins' => __( 'Plugins', WPCA_TEXT_DOMAIN ),
            'themes' => __( 'Themes', WPCA_TEXT_DOMAIN )
        );

        return $categories;
    }
}
