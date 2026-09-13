<?php
/**
 * WPCleanAdmin Extension API Registration Tasks Trait
 *
 * 扩展注册、激活/停用、查询、安装/卸载与生命周期，
 * 从 class-wpca-extension-api.php 按职责抽离，公开方法契约不变。
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
 * 扩展注册任务 trait
 */
trait ExtensionRegistrationTasks {

    /**
     * Load all registered extensions
     *
     * @uses \apply_filters() To get loaded extensions
     * @return void
     */
    public function load_extensions() {
        $extensions = \apply_filters( 'wpca_register_extensions', array() );

        foreach ( $extensions as $extension ) {
            $this->register_extension( $extension );
        }

        \do_action( 'wpca_extensions_loaded' );
    }

    /**
     * Register a new extension
     *
     * @param array $extension Extension configuration
     * @return bool Success status
     */
    public function register_extension( $extension ) {
        if ( ! is_array( $extension ) ) {
            return false;
        }

        $required_keys = array( 'id', 'name', 'version', 'file' );

        foreach ( $required_keys as $key ) {
            if ( ! isset( $extension[ $key ] ) || empty( $extension[ $key ] ) ) {
                return false;
            }
        }

        $extension_id = \sanitize_key( $extension['id'] );

        if ( isset( $this->extensions[ $extension_id ] ) ) {
            return false;
        }

        $extension['registered_at'] = current_time( 'mysql' );
        $extension['active'] = false;

        $this->extensions[ $extension_id ] = $extension;

        \do_action( "wpca_extension_registered_{$extension_id}", $extension );
        \do_action( 'wpca_extension_registered', $extension_id, $extension );

        return true;
    }

    /**
     * Unregister an extension
     *
     * @param string $extension_id Extension ID
     * @return bool Success status
     */
    public function unregister_extension( $extension_id ) {
        $extension_id = sanitize_key( $extension_id );

        if ( ! isset( $this->extensions[ $extension_id ] ) ) {
            return false;
        }

        $extension = $this->extensions[ $extension_id ];

        // Deactivate if active
        if ( $extension['active'] ) {
            $this->deactivate_extension( $extension_id );
        }

        unset( $this->extensions[ $extension_id ] );

        \do_action( "wpca_extension_unregistered_{$extension_id}", $extension );
        \do_action( 'wpca_extension_unregistered', $extension_id, $extension );

        return true;
    }

    /**
     * Activate an extension
     *
     * @param string $extension_id Extension ID
     * @return bool Success status
     */
    public function activate_extension( $extension_id ) {
        $extension_id = sanitize_key( $extension_id );

        if ( ! isset( $this->extensions[ $extension_id ] ) ) {
            return false;
        }

        $extension = $this->extensions[ $extension_id ];

        if ( $extension['active'] ) {
            return true;
        }

        // Include extension file
        if ( file_exists( $extension['file'] ) ) {
            include_once $extension['file'];
        }

        $extension['active'] = true;
        $extension['activated_at'] = current_time( 'mysql' );
        $this->extensions[ $extension_id ] = $extension;

        \do_action( "wpca_extension_activated_{$extension_id}", $extension );
        \do_action( 'wpca_extension_activated', $extension_id, $extension );

        return true;
    }

    /**
     * Deactivate an extension
     *
     * @param string $extension_id Extension ID
     * @return bool Success status
     */
    public function deactivate_extension( $extension_id ) {
        $extension_id = sanitize_key( $extension_id );

        if ( ! isset( $this->extensions[ $extension_id ] ) ) {
            return false;
        }

        $extension = $this->extensions[ $extension_id ];

        if ( ! $extension['active'] ) {
            return true;
        }

        $extension['active'] = false;
        unset( $extension['activated_at'] );
        $this->extensions[ $extension_id ] = $extension;

        \do_action( "wpca_extension_deactivated_{$extension_id}", $extension );
        \do_action( 'wpca_extension_deactivated', $extension_id, $extension );

        return true;
    }

    /**
     * Get all registered extensions
     *
     * @param string $status Filter by status (all, active, inactive)
     * @return array Registered extensions
     */
    public function get_extensions( $status = 'all' ) {
        if ( $status === 'all' ) {
            return $this->extensions;
        }

        $filtered = array();

        foreach ( $this->extensions as $id => $extension ) {
            if ( $status === 'active' && $extension['active'] ) {
                $filtered[ $id ] = $extension;
            } elseif ( $status === 'inactive' && ! $extension['active'] ) {
                $filtered[ $id ] = $extension;
            }
        }

        return $filtered;
    }

    /**
     * Get single extension by ID
     *
     * @param string $extension_id Extension ID
     * @return array|null Extension data or null if not found
     */
    public function get_extension( $extension_id ) {
        $extension_id = \sanitize_key( $extension_id );

        return isset( $this->extensions[ $extension_id ] ) ? $this->extensions[ $extension_id ] : null;
    }

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
