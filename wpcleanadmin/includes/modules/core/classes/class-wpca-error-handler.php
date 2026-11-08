<?php
/**
 * WPCleanAdmin Error Handler
 *
 * @package WPCleanAdmin\Modules\Core\Classes
 * @version 1.9.16
 * @author Tanox
 * @since 1.8.0
 */

namespace WPCleanAdmin\Modules\Core\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-error-levels.php';
require_once __DIR__ . '/class-wpca-file-logger.php';
require_once __DIR__ . '/class-wpca-error-config.php';
require_once __DIR__ . '/class-wpca-error-handler-handling-tasks.php';
require_once __DIR__ . '/class-wpca-error-handler-logging-tasks.php';

use WPCleanAdmin\Modules\Core\Classes\Error_Levels;
use WPCleanAdmin\Modules\Core\Classes\File_Logger;
use WPCleanAdmin\Modules\Core\Classes\Error_Config;

/**
 * Error handler class
 */
class Error_Handler {
    use Error_Levels;
    use ErrorHandlingTasks;
    use ErrorLoggingTasks;

    /**
     * Singleton instance
     *
     * @var Error_Handler
     */
    private static $instance = null;

    /**
     * 文件日志器
     *
     * @var File_Logger
     */
    private $file_logger;

    /**
     * 错误日志配置器
     *
     * @var Error_Config
     */
    private $config;

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
        $this->file_logger = new File_Logger();
        $this->config      = new Error_Config();
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
        $this->config->load_log_level();
    }
}
