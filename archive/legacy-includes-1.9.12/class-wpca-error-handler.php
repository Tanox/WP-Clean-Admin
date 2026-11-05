<?php
/**
 * WPCleanAdmin Error Handler
 *
 * @package WPCleanAdmin
 * @version 1.9.2
 * @author Tanox
 * @since 1.8.0
 */

namespace WPCleanAdmin;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的职责 trait
require_once __DIR__ . '/class-wpca-error-handler-handlers-tasks.php';
require_once __DIR__ . '/class-wpca-error-handler-logging-tasks.php';

/**
 * Error handler class
 */
class Error_Handler {
    use ErrorHandlerHandlersTasks;
    use ErrorHandlerLoggingTasks;

    /**
     * Singleton instance
     *
     * @var Error_Handler
     */
    private static $instance = null;

    /**
     * Log levels
     */
    const LOG_LEVELS = array(
        'debug'    => 0,
        'info'     => 1,
        'notice'   => 2,
        'warning'  => 3,
        'error'    => 4,
        'critical' => 5,
    );

    /**
     * Current log level
     *
     * @var string
     */
    private $log_level = 'notice';

    /**
     * Get singleton instance
     *
     * @return Error_Handler
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize error handler
     */
    public function init() {
        // Set error handler
        \set_error_handler( array( $this, 'error_handler' ) );

        // Set exception handler
        \set_exception_handler( array( $this, 'exception_handler' ) );

        // Set shutdown function
        \register_shutdown_function( array( $this, 'shutdown_function' ) );

        // Get log level from settings
        $this->load_log_level();
    }

    /**
     * Load log level from settings
     */
    private function load_log_level() {
        $settings = ( function_exists( 'get_option' ) ? \get_option( 'wpca_settings', array() ) : array() );
        if ( isset( $settings['general'] ) && isset( $settings['general']['log_level'] ) ) {
            $log_level = $settings['general']['log_level'];
            if ( isset( self::LOG_LEVELS[ $log_level ] ) ) {
                $this->log_level = $log_level;
            }
        }
    }

    /**
     * Set log level
     *
     * @param string $log_level Log level
     */
    public function set_log_level( string $log_level ) {
        if ( isset( self::LOG_LEVELS[ $log_level ] ) ) {
            $this->log_level = $log_level;
        }
    }

    /**
     * Get log level
     *
     * @return string
     */
    public function get_log_level(): string {
        return $this->log_level;
    }
}
