<?php
/**
 * WPCleanAdmin Extension API Settings Tasks Trait
 *
 * 扩展设置存取与重置，从 class-wpca-extension-api.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 扩展设置任务 trait
 */
trait ExtensionSettingsTasks {

    /**
     * Save extension settings
     *
     * @param string $extension_id Extension ID
     * @param array $settings Extension settings
     * @return bool Success status
     */
    public function save_extension_settings( $extension_id, $settings ) {
        $extension_id = \sanitize_key( $extension_id );

        if ( ! $this->get_extension( $extension_id ) ) {
            return false;
        }

        $option_name = 'wpca_ext_' . $extension_id . '_settings';

        // Validate settings
        $validated_settings = \apply_filters( 'wpca_extension_settings_validate_' . $extension_id, $settings, $extension_id );

        // Save settings
        $result = \update_option( $option_name, $validated_settings );

        // Trigger action
        if ( $result ) {
            \do_action( 'wpca_ext_settings_saved', $extension_id, $validated_settings );
            \do_action( 'wpca_ext_settings_saved_' . $extension_id, $validated_settings );
        }

        return $result;
    }

    /**
     * Get extension settings
     *
     * @param string $extension_id Extension ID
     * @return array Extension settings
     */
    public function get_extension_settings( $extension_id ) {
        $extension_id = \sanitize_key( $extension_id );
        $option_name = 'wpca_ext_' . $extension_id . '_settings';

        $settings = \get_option( $option_name, array() );

        // Filter the settings before returning
        return \apply_filters( 'wpca_extension_settings_get_' . $extension_id, $settings, $extension_id );
    }

    /**
     * Reset extension settings
     *
     * @param string $extension_id Extension ID
     * @return bool Success status
     */
    public function reset_extension_settings( $extension_id ) {
        $extension_id = \sanitize_key( $extension_id );
        $option_name = 'wpca_ext_' . $extension_id . '_settings';

        // Get default settings
        $default_settings = \apply_filters( 'wpca_extension_default_settings_' . $extension_id, array(), $extension_id );

        // Save default settings
        $result = \update_option( $option_name, $default_settings );

        // Trigger action
        if ( $result ) {
            \do_action( 'wpca_ext_settings_reset', $extension_id, $default_settings );
            \do_action( 'wpca_ext_settings_reset_' . $extension_id, $default_settings );
        }

        return $result;
    }
}
