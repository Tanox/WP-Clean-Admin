<?php
/**
 * WPCleanAdmin Extension Registration Core Tasks
 *
 * 扩展注册 / 激活 / 停用 / 查询，从 ExtensionRegistrationTasks 抽离。
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
 * 扩展注册核心任务（trait）。
 */
trait ExtensionRegistrationCoreTasks {

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
}
