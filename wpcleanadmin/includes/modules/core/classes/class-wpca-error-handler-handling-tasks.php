<?php
/**
 * WPCleanAdmin Error Handler Handling Tasks
 *
 * PHP 错误/异常/停机回调处理，公开 API 不变。
 * 主类 `Error_Handler` 通过 `use ErrorHandlingTasks;` 组合。
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

/**
 * Error handler handling tasks
 */
trait ErrorHandlingTasks {

    /**
     * Custom error handler
     *
     * @param int    $errno  Error number
     * @param string $errstr Error message
     * @param string $errfile Error file
     * @param int    $errline Error line
     * @return bool
     */
    public function error_handler( int $errno, string $errstr, string $errfile, int $errline ): bool {
        // Convert error number to log level
        $log_level = $this->get_log_level_from_errorno( $errno );

        // Check if error should be logged
        if ( $this->config->should_log( $log_level ) ) {
            $error = array(
                'type'    => 'error',
                'level'   => $log_level,
                'message' => $errstr,
                'file'    => $errfile,
                'line'    => $errline,
                'time'    => \time()
            );

            $this->log( $error );
        }

        // Return false to let PHP handle the error normally
        return false;
    }

    /**
     * Custom exception handler
     *
     * @param \Throwable $exception Exception object
     */
    public function exception_handler( \Throwable $exception ) {
        $error = array(
            'type'    => 'exception',
            'level'   => 'error',
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTrace(),
            'time'    => \time()
        );

        $this->log( $error );

        // Display error for debugging
        if ( \defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            echo '<pre>';
            echo \esc_html( 'Uncaught Exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine() . '\n' );
            echo \esc_html( $exception->getTraceAsString() );
            echo '</pre>';
        }
    }

    /**
     * Shutdown function
     */
    public function shutdown_function() {
        $error = \error_get_last();
        if ( $error !== null ) {
            $this->error_handler( $error['type'], $error['message'], $error['file'], $error['line'] );
        }
    }
}
