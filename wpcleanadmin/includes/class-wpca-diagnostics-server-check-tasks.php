<?php
/**
 * Diagnostics Server Check Tasks
 *
 * Server / core environment diagnostic checks (PHP, WordPress, MySQL, memory, debug).
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
 * Server / core environment check tasks for the Diagnostics class.
 */
trait DiagnosticsServerCheckTasks {

    public function check_php_version(): array {
        $current_version = phpversion();
        $min_version = '7.4';

        if ( version_compare( $current_version, $min_version, '>=' ) ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'PHP version %s is supported', WPCA_TEXT_DOMAIN ), $current_version ),
                'details' => array(
                    'current' => $current_version,
                    'minimum' => $min_version,
                    'recommended' => '8.0+'
                )
            );
        }

        return array(
            'status' => 'error',
            'message' => sprintf( __( 'PHP version %s is outdated', WPCA_TEXT_DOMAIN ), $current_version ),
            'details' => array(
                'current' => $current_version,
                'minimum' => $min_version,
                'recommended' => '8.0+'
            ),
            'action' => __( 'Please upgrade PHP to version 7.4 or higher', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_wp_version(): array {
        global $wp_version;
        $min_version = '5.8';

        if ( version_compare( $wp_version, $min_version, '>=' ) ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'WordPress version %s is supported', WPCA_TEXT_DOMAIN ), $wp_version ),
                'details' => array(
                    'current' => $wp_version,
                    'minimum' => $min_version
                )
            );
        }

        return array(
            'status' => 'error',
            'message' => sprintf( __( 'WordPress version %s is outdated', WPCA_TEXT_DOMAIN ), $wp_version ),
            'details' => array(
                'current' => $wp_version,
                'minimum' => $min_version
            ),
            'action' => __( 'Please upgrade WordPress to version 5.8 or higher', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_mysql_version(): array {
        global $wpdb;
        $current_version = $wpdb->db_version();
        $min_version = '5.6';

        if ( version_compare( $current_version, $min_version, '>=' ) ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'MySQL version %s is supported', WPCA_TEXT_DOMAIN ), $current_version ),
                'details' => array(
                    'current' => $current_version,
                    'minimum' => $min_version,
                    'recommended' => '8.0+'
                )
            );
        }

        return array(
            'status' => 'error',
            'message' => sprintf( __( 'MySQL version %s is outdated', WPCA_TEXT_DOMAIN ), $current_version ),
            'details' => array(
                'current' => $current_version,
                'minimum' => $min_version,
                'recommended' => '8.0+'
            ),
            'action' => __( 'Please upgrade MySQL to version 5.6 or higher', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_memory_limit(): array {
        $memory_limit = ini_get( 'memory_limit' );
        $min_limit = '256M';

        $current_bytes = $this->convert_to_bytes( $memory_limit );
        $min_bytes = $this->convert_to_bytes( $min_limit );

        if ( $current_bytes >= $min_bytes ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'Memory limit %s is sufficient', WPCA_TEXT_DOMAIN ), $memory_limit ),
                'details' => array(
                    'current' => $memory_limit,
                    'minimum' => $min_limit,
                    'recommended' => '512M'
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => sprintf( __( 'Memory limit %s may be insufficient', WPCA_TEXT_DOMAIN ), $memory_limit ),
            'details' => array(
                'current' => $memory_limit,
                'minimum' => $min_limit,
                'recommended' => '512M'
            ),
            'action' => __( 'Consider increasing PHP memory limit in php.ini or .htaccess', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_wp_debug(): array {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            return array(
                'status' => 'warning',
                'message' => __( 'WP_DEBUG is enabled', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'enabled' => true
                ),
                'action' => __( 'Disable WP_DEBUG on production sites for better performance and security', WPCA_TEXT_DOMAIN )
            );
        }

        return array(
            'status' => 'pass',
            'message' => __( 'WP_DEBUG is disabled', WPCA_TEXT_DOMAIN ),
            'details' => array(
                'enabled' => false
            )
        );
    }

    private function convert_to_bytes( string $value ): int {
        $value = trim( $value );
        $last = strtolower( $value[strlen( $value ) - 1] );

        switch ( $last ) {
            case 'g':
                return (int) $value * 1024 * 1024 * 1024;
            case 'm':
                return (int) $value * 1024 * 1024;
            case 'k':
                return (int) $value * 1024;
            default:
                return (int) $value;
        }
    }
}
