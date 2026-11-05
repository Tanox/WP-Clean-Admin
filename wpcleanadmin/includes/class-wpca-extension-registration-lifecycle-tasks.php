<?php
/**
 * WPCleanAdmin Extension Registration Lifecycle Tasks
 *
 * 扩展信息查询 / 安装 / 卸载 / 生命周期钩子，从 ExtensionRegistrationTasks 抽离。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 扩展注册生命周期任务（trait）。
 */
trait ExtensionRegistrationLifecycleTasks {

    /**
     * Get extension API version
     *
     * @return string API version
     */
    public function get_api_version() {
        return '1.0.0';
    }

    /**
     * Get extension info
     *
     * @param string $extension_id Extension ID
     * @return array Extension information
     */
    public function get_extension_info( $extension_id ) {
        $extension = $this->get_extension( $extension_id );

        if ( ! $extension ) {
            return array();
        }

        return array(
            'id' => $extension['id'],
            'name' => $extension['name'],
            'version' => $extension['version'],
            'active' => $extension['active'],
            'author' => isset( $extension['author'] ) ? $extension['author'] : '',
            'description' => isset( $extension['description'] ) ? $extension['description'] : '',
            'file' => $extension['file'],
        );
    }

    /**
     * Check if extension is active
     *
     * @param string $extension_id Extension ID
     * @return bool True if active
     */
    public function is_extension_active( $extension_id ) {
        $extension = $this->get_extension( $extension_id );

        return $extension && $extension['active'];
    }

    /**
     * Get extension count
     *
     * @param string $status Filter by status
     * @return int Extension count
     */
    public function get_extension_count( $status = 'all' ) {
        $extensions = $this->get_extensions( $status );
        return count( $extensions );
    }

    /**
     * Export extensions configuration
     *
     * @return array Exported data
     */
    public function export_extensions() {
        return array(
            'exported_at' => \current_time( 'mysql' ),
            'api_version' => $this->get_api_version(),
            'extensions' => $this->get_extensions(),
            'total_count' => $this->get_extension_count(),
            'active_count' => $this->get_extension_count( 'active' ),
        );
    }

    /**
     * Install extension
     *
     * @param string $extension_id Extension ID
     * @param array $extension_data Extension data
     * @return bool Success status
     */
    public function install_extension( $extension_id, $extension_data = array() ) {
        $extension_id = \sanitize_key( $extension_id );

        // Check if extension already exists
        if ( $this->get_extension( $extension_id ) ) {
            return false;
        }

        // Validate extension data
        $required_fields = array( 'name', 'version', 'file' );
        foreach ( $required_fields as $field ) {
            if ( ! isset( $extension_data[ $field ] ) || empty( $extension_data[ $field ] ) ) {
                return false;
            }
        }

        // Set default data
        $extension_data['id'] = $extension_id;
        $extension_data['installed_at'] = current_time( 'mysql' );
        $extension_data['active'] = false;

        // Register the extension
        if ( ! $this->register_extension( $extension_data ) ) {
            return false;
        }

        // Trigger install action
        \do_action( 'wpca_extension_installed', $extension_id, $extension_data );
        \do_action( 'wpca_extension_installed_' . $extension_id, $extension_data );

        return true;
    }

    /**
     * Uninstall extension
     *
     * @param string $extension_id Extension ID
     * @return bool Success status
     */
    public function uninstall_extension( $extension_id ) {
        $extension_id = \sanitize_key( $extension_id );

        if ( ! $this->get_extension( $extension_id ) ) {
            return false;
        }

        // Deactivate extension first
        $this->deactivate_extension( $extension_id );

        // Remove extension settings
        $option_name = 'wpca_ext_' . $extension_id . '_settings';
        \delete_option( $option_name );

        // Unregister extension
        $this->unregister_extension( $extension_id );

        // Trigger action
        \do_action( 'wpca_extension_uninstalled', $extension_id );

        return true;
    }

    /**
     * Get extension lifecycle hooks
     *
     * @param string $extension_id Extension ID
     * @return array Lifecycle hooks
     */
    public function get_lifecycle_hooks( $extension_id ) {
        return array(
            'install' => 'wpca_extension_installed_' . $extension_id,
            'activate' => 'wpca_extension_activated_' . $extension_id,
            'deactivate' => 'wpca_extension_deactivated_' . $extension_id,
            'uninstall' => 'wpca_extension_uninstalled_' . $extension_id,
        );
    }
}
