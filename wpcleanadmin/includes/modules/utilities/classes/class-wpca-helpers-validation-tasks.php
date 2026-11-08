<?php
/**
 * WPCleanAdmin Helpers Validation Tasks
 *
 * 承载能力校验、参数校验、AJAX nonce 校验与 AJAX/验证错误响应，从 Helpers 主类抽取。
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
 * 校验与 AJAX 错误处理任务（trait）。
 */
trait HelpersValidationTasks {

    /**
     * Check if user has required capability
     *
     * @param string $capability Capability to check
     * @return bool True if user has capability, false otherwise
     */
    public function user_has_capability( $capability ) {
        if ( ! function_exists( 'current_user_can' ) ) {
            return false;
        }

        return \current_user_can( $capability );
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
        if ( ! function_exists( '\wp_verify_nonce' ) || ! \wp_verify_nonce( $nonce, $action ) ) {
            $this->handle_ajax_error(
                \__( 'Security verification failed. Please try again.', \WPCA_TEXT_DOMAIN ),
                WPCA_Errors::ERROR_AUTH
            );
            return false;
        }

        return true;
    }

    /**
     * Handle AJAX errors
     *
     * Creates standardized error response for AJAX requests
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
     * Creates standardized error response for validation failures
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
}
