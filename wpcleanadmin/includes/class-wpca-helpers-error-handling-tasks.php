<?php
/**
 * Helpers Error Handling Tasks
 *
 * Error/exception handler, AJAX and validation helper methods.
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
 * Error-handling, AJAX and validation helper tasks.
 */
trait HelpersErrorHandlingTasks {

    /**
     * Register error handler for WordPress
     *
     * Sets custom error handler and exception handler for consistent error management.
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
            // E_STRICT is removed since PHP 7.4; omitted to avoid deprecation warnings.
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

    /**
     * Handle AJAX errors
     *
     * Creates standardized error response for AJAX requests.
     *
     * @param string $message Error message
     * @param int $error_code Error code
     * @param array $additional_data Additional data
     * @return void Outputs JSON error response and exits
     */
    public function handle_ajax_error( $message = '', $error_code = WPCA_Errors::ERROR_AJAX, $additional_data = array() ) {
        $response = $this->create_error_response( $error_code, $message, $additional_data );

        if ( function_exists( '\wp_send_json' ) ) {
            \wp_send_json( $response );
        } else {
            // Fallback for non-WordPress environments
            header( 'Content-Type: application/json' );
            echo json_encode( $response );
            exit;
        }
    }

    /**
     * Handle validation errors
     *
     * Creates standardized error response for validation failures.
     *
     * @param array $errors Array of validation errors (field => error message)
     * @param string $message General error message
     * @return array Error response array
     */
    public function handle_validation_errors( $errors = array(), $message = '' ) {
        if ( empty( $message ) ) {
            $message = \__( 'Validation failed. Please check your entries.', \WPCA_TEXT_DOMAIN );
        }

        return $this->create_error_response(
            WPCA_Errors::ERROR_VALIDATION,
            $message,
            array( 'validation_errors' => $errors )
        );
    }

    /**
     * Validate required parameters
     *
     * @param array $params Parameters to validate
     * @param array $required List of required parameter names
     * @return array|null Validation errors or null if valid
     */
    public function validate_required_params( $params, $required = array() ) {
        $errors = array();

        foreach ( $required as $param_name ) {
            if ( ! isset( $params[ $param_name ] ) || empty( $params[ $param_name ] ) ) {
                $errors[ $param_name ] = sprintf(
                    \__( 'The %s parameter is required.', \WPCA_TEXT_DOMAIN ),
                    $param_name
                );
            }
        }

        return empty( $errors ) ? null : $errors;
    }

    /**
     * Validate nonce for AJAX requests
     *
     * @param string $nonce Nonce to verify
     * @param string $action Nonce action
     * @return bool True if nonce is valid
     */
    public function validate_ajax_nonce( $nonce, $action = 'wpca_ajax_nonce' ) {
        if ( function_exists( '\wp_verify_nonce' ) && ! \wp_verify_nonce( $nonce, $action ) ) {
            $this->handle_ajax_error(
                \__( 'Security verification failed. Please try again.', defined( 'WPCA_TEXT_DOMAIN' ) ? \WPCA_TEXT_DOMAIN : 'wp-clean-admin' ),
                WPCA_Errors::ERROR_AUTH
            );
        }

        return true;
    }

    /**
     * Check user capabilities
     *
     * @param string $capability Required capability
     * @return bool True if user has capability
     */
    public function check_capability( $capability ) {
        if ( ! \current_user_can( $capability ) ) {
            return false;
        }

        return true;
    }
}
