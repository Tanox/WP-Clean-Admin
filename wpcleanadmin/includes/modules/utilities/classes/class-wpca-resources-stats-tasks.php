<?php
/**
 * WPCleanAdmin Resources Stats Tasks
 *
 * 资源用量/优化状态/明细统计任务。主类 `Resources` 通过 `use ResourcesStatsTasks;`
 * 组合，公开 API 不变。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Resources stats tasks
 */
trait ResourcesStatsTasks {

    /**
     * Get resource usage statistics
     *
     * @return array Resource usage statistics
     */
    public function get_resource_usage(): array {
        $usage = array();

        // Get memory usage
        $usage['memory'] = array(
            'current' => \size_format( \memory_get_usage() ),
            'peak' => \size_format( \memory_get_peak_usage() ),
            'limit' => \ini_get( 'memory_limit' )
        );

        // Get PHP version
        $usage['php_version'] = PHP_VERSION;

        // Get WordPress version
        if ( defined( 'WP_VERSION' ) ) {
            $usage['wp_version'] = constant( 'WP_VERSION' );
        }

        // Get active plugins count
        if ( function_exists( 'get_option' ) ) {
            $active_plugins = \get_option( 'active_plugins', array() );
            $usage['active_plugins_count'] = count( $active_plugins );
        }

        return $usage;
    }

    /**
     * Get resource optimization status
     *
     * @return array Resource optimization status
     */
    public function get_optimization_status(): array {
        $settings = \wpca_get_settings();

        return array(
            'emojis_disabled' => isset( $settings['resources']['disable_emojis'] ) ? $settings['resources']['disable_emojis'] : false,
            'embed_disabled' => isset( $settings['resources']['disable_embed'] ) ? $settings['resources']['disable_embed'] : false,
            'rest_api_disabled' => isset( $settings['resources']['disable_rest_api'] ) ? $settings['resources']['disable_rest_api'] : false,
            'heartbeat_disabled' => isset( $settings['resources']['disable_heartbeat'] ) ? $settings['resources']['disable_heartbeat'] : false,
            'admin_toolbar_removed' => isset( $settings['resources']['remove_admin_toolbar'] ) ? $settings['resources']['remove_admin_toolbar'] : false,
            'dashboard_widgets_removed' => isset( $settings['resources']['remove_dashboard_widgets'] ) ? $settings['resources']['remove_dashboard_widgets'] : false,
        );
    }

    /**
     * Get resources statistics
     *
     * @return array Resources statistics
     */
    public function get_resources_stats(): array {
        return array(
            'usage' => $this->get_resource_usage(),
            'optimization' => $this->get_optimization_status()
        );
    }

    /**
     * Get resource details
     *
     * @param string $type Resource type
     * @return array Resource details
     */
    public function get_resource_details( string $type ): array {
        $details = array();

        switch ( $type ) {
            case 'scripts':
                $details = $this->get_scripts_details();
                break;
            case 'styles':
                $details = $this->get_styles_details();
                break;
            default:
                $details = array('message' => 'Invalid resource type');
        }

        return $details;
    }

    /**
     * Get scripts details
     *
     * @return array Scripts details
     */
    private function get_scripts_details(): array {
        global $wp_scripts;

        $scripts = array();

        if ( isset( $wp_scripts ) && is_object( $wp_scripts ) ) {
            foreach ( $wp_scripts->queue as $handle ) {
                if ( isset( $wp_scripts->registered[ $handle ] ) ) {
                    $script = $wp_scripts->registered[ $handle ];
                    $scripts[] = array(
                        'handle' => $handle,
                        'src' => $script->src ?? '',
                        'deps' => $script->deps ?? array(),
                        'ver' => $script->ver ?? false
                    );
                }
            }
        }

        return $scripts;
    }

    /**
     * Get styles details
     *
     * @return array Styles details
     */
    private function get_styles_details(): array {
        global $wp_styles;

        $styles = array();

        if ( isset( $wp_styles ) && is_object( $wp_styles ) ) {
            foreach ( $wp_styles->queue as $handle ) {
                if ( isset( $wp_styles->registered[ $handle ] ) ) {
                    $style = $wp_styles->registered[ $handle ];
                    $styles[] = array(
                        'handle' => $handle,
                        'src' => $style->src ?? '',
                        'deps' => $style->deps ?? array(),
                        'ver' => $style->ver ?? false
                    );
                }
            }
        }

        return $styles;
    }
}
