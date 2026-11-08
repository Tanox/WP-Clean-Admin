<?php
/**
 * WPCleanAdmin Error Handler Logging Tasks
 *
 * 错误日志写入与日志级别读写，公开 API 不变。
 * 主类 `Error_Handler` 通过 `use ErrorLoggingTasks;` 组合。
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
 * Error handler logging tasks
 */
trait ErrorLoggingTasks {

    /**
     * Log error
     *
     * @param array $error Error data
     */
    public function log( array $error ) {
        // Log to WordPress debug log
        if ( \defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            $message = sprintf(
                '[WP Clean Admin] %s: %s in %s:%d',
                strtoupper( $error['level'] ),
                $error['message'],
                $error['file'],
                $error['line']
            );

            if ( function_exists( 'error_log' ) ) {
                \error_log( $message );
            }
        }

        // Log to custom log file
        $this->file_logger->log_to_file( $error );
    }

    /**
     * Log message
     *
     * @param string $message Message
     * @param string $level   Log level
     * @param string $file    File name
     * @param int    $line    Line number
     */
    public function log_message( string $message, string $level = 'info', string $file = '', int $line = 0 ) {
        if ( empty( $file ) || $line === 0 ) {
            $backtrace = \debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );
            if ( isset( $backtrace[0] ) ) {
                $file = $backtrace[0]['file'] ?? '';
                $line = $backtrace[0]['line'] ?? 0;
            }
        }

        $error = array(
            'type'    => 'message',
            'level'   => $level,
            'message' => $message,
            'file'    => $file,
            'line'    => $line,
            'time'    => \time()
        );

        $this->log( $error );
    }

    /**
     * Set log level
     *
     * @param string $log_level
     */
    public function set_log_level( string $log_level ) {
        $this->config->set_log_level( $log_level );
    }

    /**
     * Get log level
     *
     * @return string
     */
    public function get_log_level(): string {
        return $this->config->get_log_level();
    }
}
