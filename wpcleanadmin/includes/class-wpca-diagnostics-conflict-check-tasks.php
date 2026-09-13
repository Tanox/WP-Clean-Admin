<?php
/**
 * Diagnostics Conflict Check Tasks
 *
 * Compatibility / conflict diagnostic checks (plugins, theme, cache, REST API).
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
 * Compatibility / conflict check tasks for the Diagnostics class.
 */
trait DiagnosticsConflictCheckTasks {

    public function check_plugin_conflicts(): array {
        $conflicting_plugins = array();

        if ( function_exists( 'get_plugins' ) ) {
            $plugins = get_plugins();
            $active_plugins = get_option( 'active_plugins', array() );

            $known_conflicts = array(
                'wp-super-cache/wp-super-cache.php',
                'w3-total-cache/w3-total-cache.php',
                'wp-rocket/wp-rocket.php'
            );

            foreach ( $active_plugins as $plugin ) {
                if ( in_array( $plugin, $known_conflicts ) ) {
                    $conflicting_plugins[] = $plugins[$plugin]['Name'] ?? $plugin;
                }
            }
        }

        if ( empty( $conflicting_plugins ) ) {
            return array(
                'status' => 'pass',
                'message' => __( 'No known plugin conflicts detected', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'checked_plugins' => count( $active_plugins ?? array() ),
                    'conflicts_found' => 0
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => sprintf( __( '%d potential plugin conflicts detected', WPCA_TEXT_DOMAIN ), count( $conflicting_plugins ) ),
            'details' => array(
                'conflicting_plugins' => $conflicting_plugins
            ),
            'action' => __( 'Some plugins may conflict with performance optimizations. Test compatibility before production use.', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_theme_conflicts(): array {
        $current_theme = wp_get_theme();
        $parent_theme = $current_theme->parent();

        $potential_conflicts = array();

        if ( $parent_theme ) {
            if ( version_compare( $parent_theme->get( 'Version' ), '1.0', '<' ) ) {
                $potential_conflicts[] = sprintf( __( 'Parent theme %s may be outdated', WPCA_TEXT_DOMAIN ), $parent_theme->get( 'Name' ) );
            }
        }

        if ( version_compare( $current_theme->get( 'Version' ), '1.0', '<' ) ) {
            $potential_conflicts[] = sprintf( __( 'Theme %s may be outdated', WPCA_TEXT_DOMAIN ), $current_theme->get( 'Name' ) );
        }

        if ( empty( $potential_conflicts ) ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'Theme %s is compatible', WPCA_TEXT_DOMAIN ), $current_theme->get( 'Name' ) ),
                'details' => array(
                    'theme' => $current_theme->get( 'Name' ),
                    'version' => $current_theme->get( 'Version' ),
                    'parent' => $parent_theme ? $parent_theme->get( 'Name' ) : 'None'
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => __( 'Potential theme conflicts detected', WPCA_TEXT_DOMAIN ),
            'details' => array(
                'theme' => $current_theme->get( 'Name' ),
                'version' => $current_theme->get( 'Version' ),
                'issues' => $potential_conflicts
            ),
            'action' => __( 'Consider updating your theme to the latest version', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_cache_status(): array {
        $cache_plugins = array(
            'wp-super-cache/wp-super-cache.php',
            'w3-total-cache/w3-total-cache.php',
            'wp-rocket/wp-rocket.php',
            'wp-fastest-cache/wpFastestCache.php'
        );

        $active_plugins = get_option( 'active_plugins', array() );
        $cache_active = false;
        $cache_plugin = '';

        foreach ( $cache_plugins as $plugin ) {
            if ( in_array( $plugin, $active_plugins ) ) {
                $cache_active = true;
                $cache_plugin = $plugin;
                break;
            }
        }

        if ( $cache_active ) {
            return array(
                'status' => 'pass',
                'message' => sprintf( __( 'Caching plugin %s is active', WPCA_TEXT_DOMAIN ), $cache_plugin ),
                'details' => array(
                    'cache_active' => true,
                    'plugin' => $cache_plugin
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => __( 'No caching plugin detected', WPCA_TEXT_DOMAIN ),
            'details' => array(
                'cache_active' => false
            ),
            'action' => __( 'Consider installing a caching plugin for better performance', WPCA_TEXT_DOMAIN )
        );
    }

    public function check_rest_api(): array {
        $rest_api_enabled = true;

        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
            $rest_api_enabled = false;
        }

        if ( function_exists( 'get_option' ) ) {
            $disabled = get_option( 'wpca_disable_rest_api', false );
            if ( $disabled ) {
                $rest_api_enabled = false;
            }
        }

        if ( $rest_api_enabled ) {
            return array(
                'status' => 'pass',
                'message' => __( 'REST API is enabled', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'enabled' => true
                )
            );
        }

        return array(
            'status' => 'warning',
            'message' => __( 'REST API is disabled', WPCA_TEXT_DOMAIN ),
            'details' => array(
                'enabled' => false
            ),
            'action' => __( 'Some plugins may require REST API to be enabled', WPCA_TEXT_DOMAIN )
        );
    }
}
