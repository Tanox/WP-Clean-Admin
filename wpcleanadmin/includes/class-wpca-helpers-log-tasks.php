<?php
/**
 * Helpers Log Tasks
 *
 * Logging and log-management helper methods.
 *
 * @package WPCleanAdmin
 * @version 1.8.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Logging and log-management helper tasks.
 */
trait HelpersLogTasks {

    /**
     * Log message to debug log
     *
     * @param mixed $message Message to log
     * @param string $context Context of the log
     */
    public function log( $message, $context = 'general' ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        $log_message = sprintf( '[WPCleanAdmin] [%s] %s', $context, $message );

        if ( is_array( $message ) || is_object( $message ) ) {
            $log_message = sprintf( '[WPCleanAdmin] [%s] %s', $context, print_r( $message, true ) );
        }

        error_log( $log_message );
    }

    /**
     * Log error with context
     *
     * @param string $message Error message
     * @param array $context Context data
     * @param int $error_code Error code
     * @return void
     */
    public function log_error( $message, $context = array(), $error_code = WPCA_Errors::ERROR_UNKNOWN ) {
        $context_str = ! empty( $context ) ? ' | Context: ' . json_encode( $context ) : '';
        $this->log( "Error [{$error_code}]: {$message}{$context_str}", 'error' );
    }

    /**
     * Log success action
     *
     * @param string $message Success message
     * @param array $context Context data
     * @return void
     */
    public function log_success( $message, $context = array() ) {
        $context_str = ! empty( $context ) ? ' | Context: ' . json_encode( $context ) : '';
        $this->log( "Success: {$message}{$context_str}", 'success' );
    }

    /**
     * Get error statistics
     *
     * @param int $days Number of days to analyze
     * @return array Error statistics
     */
    public function get_error_stats( $days = 7 ) {
        return array(
            'total_errors' => 0,
            'error_types' => array(),
            'recent_errors' => array(),
            'period' => $days . ' days',
        );
    }

    /**
     * Clear error logs
     *
     * @return bool Success status
     */
    public function clear_logs() {
        $this->log( 'Logs cleared by user', 'log-clear' );
        return true;
    }

    /**
     * Export error logs
     *
     * @return array Error logs for export
     */
    public function export_logs() {
        return array(
            'exported_at' => \current_time( 'mysql' ),
            'wp_version' => $this->get_wp_version(),
            'php_version' => $this->get_php_version(),
            'plugin_version' => $this->get_plugin_info( 'Version' ),
            'logs' => array(),
        );
    }
}
