<?php
/**
 * WPCleanAdmin Error Handler Handlers Tasks
 *
 * 承载错误/异常/关闭钩子与日志级别判定逻辑，从 Error_Handler 主类抽取。
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

/**
 * 错误处理器钩子相关任务（trait）。
 */
trait ErrorHandlerHandlersTasks {

    /**
     * Custom error handler
     *
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile Error file
     * @param int $errline Error line
     * @return bool
     */
    public function error_handler( int $errno, string $errstr, string $errfile, int $errline ): bool {
        // Convert error number to log level
        $log_level = $this->get_log_level_from_errorno( $errno );

        // Check if error should be logged
        if ( $this->should_log( $log_level ) ) {
            $error = array(
                'type'    => 'error',
                'level'   => $log_level,
                'message' => $errstr,
                'file'    => $errfile,
                'line'    => $errline,
                'time'    => \time(),
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
            'time'    => \time(),
        );

        $this->log( $error );

        // Display error for debugging
        if ( \defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            echo '<pre>';
            echo 'Uncaught Exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine() . '\n';
            echo $exception->getTraceAsString();
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

    /**
     * Get log level from error number
     *
     * @param int $errno Error number
     * @return string
     */
    private function get_log_level_from_errorno( int $errno ): string {
        switch ( $errno ) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return 'error';
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return 'warning';
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'notice';
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'info';
            default:
                // Handle E_STRICT if it exists (deprecated in PHP 5.4+, removed in PHP 7.0+)
                if ( defined( 'E_STRICT' ) && $errno === E_STRICT ) {
                    return 'info';
                }
                return 'debug';
        }
    }

    /**
     * Check if message should be logged
     *
     * @param string $log_level Log level
     * @return bool
     */
    public function should_log( string $log_level ): bool {
        $current_level = self::LOG_LEVELS[ $this->log_level ] ?? 2;
        $message_level = self::LOG_LEVELS[ $log_level ] ?? 0;

        return $message_level >= $current_level;
    }
}
