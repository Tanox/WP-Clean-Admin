<?php
/**
 * Helpers Response Tasks
 *
 * Standardized response / sanitization / error-message helper methods.
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
 * Response, sanitization and error-message helper tasks.
 */
trait HelpersResponseTasks {

    /**
     * Sanitize array of data
     *
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    public function sanitize_array( $data ): array {
        if ( ! is_array( $data ) ) {
            return array();
        }

        foreach ( $data as &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->sanitize_array( $value );
            } else {
                $value = \sanitize_text_field( $value );
            }
        }

        return $data;
    }

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
}
