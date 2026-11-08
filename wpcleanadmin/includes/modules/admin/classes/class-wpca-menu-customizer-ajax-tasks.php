<?php
/**
 * WPCleanAdmin Menu Customizer AJAX Tasks
 *
 * 菜单定制器的 AJAX 处理与 nonce/权限校验，公开 API 不变。
 * 主类 `Menu_Customizer` 通过 `use MenuCustomizerAjaxTasks;` 组合。
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Menu customizer AJAX tasks
 */
trait MenuCustomizerAjaxTasks {

    /**
     * Verify AJAX nonce and user capability.
     *
     * @return bool
     */
    private function verify_ajax_request(): bool {
        $nonce = isset( $_POST['_wpnonce'] ) ? \sanitize_text_field( \wp_unslash( $_POST['_wpnonce'] ) ) : '';

        if ( ! function_exists( '\wp_verify_nonce' ) || '' === $nonce || ! \wp_verify_nonce( $nonce, 'wpca_ajax_nonce' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Invalid nonce', \WPCA_TEXT_DOMAIN ) );
            }
            return false;
        }

        if ( ! function_exists( '\current_user_can' ) || ! \current_user_can( 'manage_options' ) ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( \__( 'Insufficient permissions', \WPCA_TEXT_DOMAIN ) );
            }
            return false;
        }

        return true;
    }

    /**
     * AJAX handler: save menu customizer settings.
     */
    public function ajax_save_settings(): void {
        if ( ! $this->verify_ajax_request() ) {
            return;
        }

        $settings = isset( $_POST['settings'] ) ? \wp_unslash( $_POST['settings'] ) : array();
        if ( ! is_array( $settings ) ) {
            $settings = array();
        }
        $settings = \WPCleanAdmin\AJAX\Settings::sanitize_settings( $settings );

        if ( $this->options->save_settings( $settings ) ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'message' => \__( 'Menu customizer settings saved successfully', \WPCA_TEXT_DOMAIN ) ) );
            }
            return;
        }

        if ( function_exists( '\wp_send_json_error' ) ) {
            \wp_send_json_error( \__( 'Failed to save menu customizer settings', \WPCA_TEXT_DOMAIN ) );
        }
    }

    /**
     * AJAX handler: reset menu customizer settings.
     */
    public function ajax_reset_settings(): void {
        if ( ! $this->verify_ajax_request() ) {
            return;
        }

        if ( $this->options->reset_settings() ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'message' => \__( 'Menu customizer settings reset to default', \WPCA_TEXT_DOMAIN ) ) );
            }
            return;
        }

        if ( function_exists( '\wp_send_json_error' ) ) {
            \wp_send_json_error( \__( 'Failed to reset menu customizer settings', \WPCA_TEXT_DOMAIN ) );
        }
    }

    /**
     * AJAX handler: export menu customizer settings.
     */
    public function ajax_export_settings(): void {
        if ( ! $this->verify_ajax_request() ) {
            return;
        }

        if ( function_exists( '\wp_send_json_success' ) ) {
            \wp_send_json_success( $this->render->export_settings() );
        }
    }

    /**
     * AJAX handler: import menu customizer settings.
     */
    public function ajax_import_settings(): void {
        if ( ! $this->verify_ajax_request() ) {
            return;
        }

        $import_data = isset( $_POST['import_data'] ) ? \wp_unslash( $_POST['import_data'] ) : array();
        if ( is_string( $import_data ) ) {
            $decoded = function_exists( '\json_decode' ) ? \json_decode( $import_data, true ) : null;
            $import_data = is_array( $decoded ) ? $decoded : array();
        }
        if ( ! is_array( $import_data ) ) {
            $import_data = array();
        }

        if ( $this->render->import_settings( $import_data ) ) {
            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( array( 'message' => \__( 'Menu customizer settings imported successfully', \WPCA_TEXT_DOMAIN ) ) );
            }
            return;
        }

        if ( function_exists( '\wp_send_json_error' ) ) {
            \wp_send_json_error( \__( 'Failed to import menu customizer settings', \WPCA_TEXT_DOMAIN ) );
        }
    }
}
