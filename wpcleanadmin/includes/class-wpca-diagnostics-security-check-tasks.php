<?php
/**
 * Diagnostics Security Check Tasks
 *
 * Security-related diagnostic checks (file permissions, database tables, SSL).
 *
 * @package WPCleanAdmin
 * @version 1.8.15
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.0
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Security-related check tasks for the Diagnostics class.
 */
trait DiagnosticsSecurityCheckTasks {

    public function check_file_permissions(): array {
        $wp_content_dir = WP_CONTENT_DIR;
        $uploads_dir = WP_CONTENT_DIR . '/uploads';

        $issues = array();

        if ( ! is_writable( $wp_content_dir ) ) {
            $issues[] = sprintf( __( 'wp-content directory is not writable: %s', WPCA_TEXT_DOMAIN ), $wp_content_dir );
        }

        if ( ! is_writable( $uploads_dir ) ) {
            $issues[] = sprintf( __( 'uploads directory is not writable: %s', WPCA_TEXT_DOMAIN ), $uploads_dir );
        }

        if ( empty( $issues ) ) {
            return array(
                'status' => 'pass',
                'message' => __( 'File permissions are correct', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'wp_content_writable' => true,
                    'uploads_writable' => true
                )
            );
        }

        return array(
            'status' => 'error',
            'message' => sprintf( __( '%d file permission issues detected', WPCA_TEXT_DOMAIN ), count( $issues ) ),
            'details' => array(
                'issues' => $issues
            ),
            'action' => __( 'Fix file permissions to allow WordPress to write to necessary directories', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_database_tables(): array {
        global $wpdb;

        $required_tables = array(
            $wpdb->posts,
            $wpdb->postmeta,
            $wpdb->comments,
            $wpdb->commentmeta,
            $wpdb->terms,
            $wpdb->term_taxonomy,
            $wpdb->term_relationships,
            $wpdb->users,
            $wpdb->usermeta,
            $wpdb->options
        );

        $missing_tables = array();

        foreach ( $required_tables as $table ) {
            if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) !== $table ) {
                $missing_tables[] = $table;
            }
        }

        if ( empty( $missing_tables ) ) {
            return array(
                'status' => 'pass',
                'message' => __( 'All required database tables exist', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'checked_tables' => count( $required_tables ),
                    'missing_tables' => 0
                )
            );
        }

        return array(
            'status' => 'error',
            'message' => sprintf( __( '%d database tables are missing', WPCA_TEXT_DOMAIN ), count( $missing_tables ) ),
            'details' => array(
                'missing_tables' => $missing_tables
            ),
            'action' => __( 'Restore missing database tables from backup or run WordPress repair', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_ssl_status(): array {
        if ( function_exists( 'is_ssl' ) && is_ssl() ) {
            return array(
                'status' => 'pass',
                'message' => __( 'SSL is enabled', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'ssl_enabled' => true,
                    'site_url' => get_site_url()
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => __( 'SSL is not enabled', WPCA_TEXT_DOMAIN ),
            'details' => array(
                'ssl_enabled' => false,
                'site_url' => get_site_url()
            ),
            'action' => __( 'Enable SSL/TLS for better security', WPCA_TEXT_DOMAIN )
        );
    }
}
