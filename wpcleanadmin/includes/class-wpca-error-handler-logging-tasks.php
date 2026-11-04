<?php
/**
 * WPCleanAdmin Error Handler Logging Tasks
 *
 * 承载日志记录与文件落盘逻辑，从 Error_Handler 主类抽取。
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
 * 错误日志记录相关任务（trait）。
 */
trait ErrorHandlerLoggingTasks {

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
        $this->log_to_file( $error );
    }

    /**
     * Log to custom file
     *
     * @param array $error Error data
     */
    private function log_to_file( array $error ) {
        $plugin_dir = defined( 'WPCA_PLUGIN_DIR' ) ? WPCA_PLUGIN_DIR : dirname( dirname( __FILE__ ) ) . '/';
        $log_dir    = $plugin_dir . 'logs';

        // Create log directory if it doesn't exist
        if ( ! \is_dir( $log_dir ) ) {
            if ( function_exists( 'wp_mkdir_p' ) ) {
                \wp_mkdir_p( $log_dir );
            } else {
                // Fallback to mkdir if wp_mkdir_p is not available
                \mkdir( $log_dir, 0755, true );
            }
        }

        $log_file = $log_dir . '/wpca-' . \date( 'Y-m-d' ) . '.log';
        $message  = sprintf(
            '[%s] [%s] %s: %s in %s:%d\n',
            \date( 'Y-m-d H:i:s' ),
            $error['type'],
            strtoupper( $error['level'] ),
            $error['message'],
            $error['file'],
            $error['line']
        );

        // Add trace if available
        if ( isset( $error['trace'] ) ) {
            $message .= 'Trace: ' . \print_r( $error['trace'], true ) . '\n';
        }

        // Write to log file
        \file_put_contents( $log_file, $message, FILE_APPEND );
    }

    /**
     * Log message
     *
     * @param string $message Message
     * @param string $level Log level
     * @param string $file File name
     * @param int $line Line number
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
            'time'    => \time(),
        );

        $this->log( $error );
    }
}
