<?php
/**
 * WPCleanAdmin Helpers Error Tasks
 *
 * 承载标准化错误/成功响应构建与全局错误、异常处理器，从 Helpers 主类抽取。
 *
 * @package WPCleanAdmin
 * @version  1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.14
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 错误响应与全局处理器任务（trait）。
 */
trait HelpersErrorTasks {

    /**
     * Create standardized error response
     *
     * @param int $error_code Error code from WPCA_Errors class
     * @param string $message Error message
     * @param array $additional_data Additional data to include
     * @return array Error response array
     */
    public function create_error_response( $error_code, $message = '', $additional_data = array() ) {
        $response = array(
            'success' => false,
            'error_code' => $error_code,
            'message' => $message,
            'data' => $additional_data,
        );

        // Log the error
        $this->log( "Error {$error_code}: {$message}", 'error' );

        return $response;
    }

    /**
     * Create standardized success response
     *
     * @param string $message Success message
     * @param array $additional_data Additional data to include
     * @return array Success response array
     */
    public function create_success_response( $message = '', $additional_data = array() ) {
        $response = array(
            'success' => true,
            'message' => $message,
            'data' => $additional_data,
        );

        return $response;
    }

    /**
     * Handle exception and create error response
     *
     * @param Exception $exception Exception to handle
     * @param int $default_error_code Default error code to use
     * @return array Error response array
     */
    public function handle_exception( $exception, $default_error_code = WPCA_Errors::ERROR_UNKNOWN ) {
        $error_code = $default_error_code;
        $message = $exception->getMessage();

        // Determine error code from exception type
        if ( strpos( $message, 'SQLSTATE' ) !== false ) {
            $error_code = WPCA_Errors::ERROR_DATABASE;
        } elseif ( strpos( $message, 'permission' ) !== false || strpos( $message, 'capability' ) !== false ) {
            $error_code = WPCA_Errors::ERROR_PERMISSION;
        } elseif ( strpos( $message, 'file' ) !== false || strpos( $message, 'upload' ) !== false ) {
            $error_code = WPCA_Errors::ERROR_FILE_OPERATION;
        }

        // Log the exception
        $this->log( $exception, 'exception' );

        return $this->create_error_response( $error_code, $message );
    }

    /**
     * Get error message by error code
     *
     * @param int $error_code Error code
     * @return string Error message
     */
    public function get_error_message( $error_code ) {
        $messages = array(
            WPCA_Errors::ERROR_NONE => \__( 'No error occurred.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_DATABASE => \__( 'A database error occurred. Please check the logs for more details.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_PERMISSION => \__( 'You do not have permission to perform this action.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_INVALID_INPUT => \__( 'Invalid input provided. Please check your entries and try again.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_FILE_OPERATION => \__( 'A file operation failed. Please check file permissions and try again.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_AJAX => \__( 'An AJAX request failed. Please try again.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_VALIDATION => \__( 'Settings validation failed. Please check your entries.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_AUTH => \__( 'Authentication failed. Please log in again.', \WPCA_TEXT_DOMAIN ),
            WPCA_Errors::ERROR_UNKNOWN => \__( 'An unknown error occurred. Please try again.', \WPCA_TEXT_DOMAIN ),
        );

        return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : $messages[ WPCA_Errors::ERROR_UNKNOWN ];
    }

    /**
     * Register error handler for WordPress
     *
     * Sets custom error handler and exception handler for consistent error management
     *
     * @uses set_error_handler() To set custom error handler
     * @uses set_exception_handler() To set custom exception handler
     * @return void
     */
    public function register_error_handler() {
        \set_error_handler( array( $this, 'custom_error_handler' ) );
        \set_exception_handler( array( $this, 'custom_exception_handler' ) );
    }

    /**
     * Custom error handler
     *
     * @param int $errno Error level
     * @param string $errstr Error message
     * @param string $errfile Error file
     * @param int $errline Error line
     * @return bool True if error was handled
     */
    public function custom_error_handler( $errno, $errstr, $errfile, $errline ) {
        if ( ! ( error_reporting() & $errno ) ) {
            return false;
        }

        $error_types = array(
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            // E_STRICT 常量在 PHP 7.4 起已废弃，移除以避免警告
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED',
        );

        $error_type = isset( $error_types[ $errno ] ) ? $error_types[ $errno ] : 'UNKNOWN';

        $message = sprintf(
            '[%s] %s in %s on line %d',
            $error_type,
            $errstr,
            $errfile,
            $errline
        );

        $this->log( $message, 'php-error' );

        return true;
    }

    /**
     * Custom exception handler
     *
     * @param Throwable $exception Uncaught exception
     * @return void
     */
    public function custom_exception_handler( $exception ) {
        $message = sprintf(
            '[EXCEPTION] %s in %s on line %d',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $this->log( $message, 'exception' );
        $this->log( $exception->getTraceAsString(), 'exception-trace' );
    }
}
