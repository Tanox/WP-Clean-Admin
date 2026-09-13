<?php
/**
 * WPCleanAdmin Performance Preload Tasks Trait
 *
 * 资源预加载（preload / prefetch / preconnect / prerender）与状态查询，
 * 从 class-wpca-performance.php 按职责抽离，公开方法契约不变。
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
 * 资源预加载任务 trait
 */
trait PerformancePreloadTasks {

    /**
     * Resource preloading for admin pages
     *
     * Preloads critical resources to improve page load performance
     * Uses WordPress resource hints (preload, prefetch, prerender)
     *
     * @uses \add_filter() To add resource hints filter
     * @return void
     */
    public function enable_resource_preloading() {
        \add_filter( 'wp_resource_hints', array( $this, 'add_resource_hints' ), 10, 2 );
    }

    /**
     * Add resource hints for performance optimization
     *
     * @param array $hints Resource hints array
     * @param string $type Resource type (dns-prefetch, preconnect, preload, prerender)
     * @return array Modified resource hints
     */
    public function add_resource_hints( $hints, $type ) {
        $settings = \wpca_get_settings();

        if ( ! isset( $settings['performance']['resource_preloading'] ) || ! $settings['performance']['resource_preloading'] ) {
            return $hints;
        }

        // Preload critical admin assets
        $preload_resources = array();
        if ( function_exists( '\includes_url' ) ) {
            $preload_resources = array(
                'admin-css' => array(
                    'href' => \includes_url( 'css/common.css' ),
                    'as' => 'style',
                    'crossorigin' => false,
                ),
                'admin-js' => array(
                    'href' => \includes_url( 'js/common.js' ),
                    'as' => 'script',
                    'crossorigin' => false,
                ),
            );
        }

        foreach ( $preload_resources as $id => $resource ) {
            $hints[] = array(
                'href' => $resource['href'],
                'as' => $resource['as'],
                'id' => $id,
                'crossorigin' => $resource['crossorigin'] ? 'anonymous' : null,
            );
        }

        return $hints;
    }

    /**
     * Preload specific resource
     *
     * @param string $url Resource URL to preload
     * @param string $as Resource type (style, script, font, image, etc.)
     * @param string $media Optional media attribute for styles
     * @return string Link tag for preloading
     */
    public function preload_resource( $url, $as = 'script', $media = '' ) {
        if ( empty( $url ) ) {
            return '';
        }

        $link_tag = '<link rel="preload" href="' . \esc_url( $url ) . '" as="' . \esc_attr( $as ) . '"';

        if ( ! empty( $media ) && $as === 'style' ) {
            $link_tag .= ' media="' . \esc_attr( $media ) . '"';
        }

        $link_tag .= ' />';

        return $link_tag;
    }

    /**
     * Add DNS prefetch for external resources
     *
     * @param array $domains Array of domains to prefetch
     * @return void
     */
    public function add_dns_prefetch( $domains = array() ) {
        if ( empty( $domains ) || ! is_array( $domains ) ) {
            return;
        }

        \add_filter( 'wp_resource_hints', function( $hints ) use ( $domains ) {
            foreach ( $domains as $domain ) {
                $hints[] = array(
                    'href' => $domain,
                    'as' => 'script',
                    'rel' => 'dns-prefetch',
                );
            }
            return $hints;
        }, 10, 1 );
    }

    /**
     * Add preconnect for external resources
     *
     * @param array $domains Array of domains to preconnect
     * @return void
     */
    public function add_preconnect( $domains = array() ) {
        if ( empty( $domains ) || ! is_array( $domains ) ) {
            return;
        }

        \add_filter( 'wp_resource_hints', function( $hints ) use ( $domains ) {
            foreach ( $domains as $domain ) {
                $hints[] = array(
                    'href' => $domain,
                    'as' => 'script',
                    'rel' => 'preconnect',
                );
            }
            return $hints;
        }, 10, 1 );
    }

    /**
     * Prerender specified URL
     *
     * @param string $url URL to prerender
     * @return string Link tag for prerendering
     */
    public function prerender_url( $url ) {
        if ( empty( $url ) ) {
            return '';
        }

        return '<link rel="prerender" href="' . \esc_url( $url ) . '" />';
    }

    /**
     * Prefetch specified URL
     *
     * @param string $url URL to prefetch
     * @return string Link tag for prefetching
     */
    public function prefetch_url( $url ) {
        if ( empty( $url ) ) {
            return '';
        }

        return '<link rel="prefetch" href="' . \esc_url( $url ) . '" />';
    }

    /**
     * Get preloading status
     *
     * @return array Preloading status information
     */
    public function get_preloading_status() {
        $settings = \wpca_get_settings();

        return array(
            'enabled' => isset( $settings['performance']['resource_preloading'] ) ? $settings['performance']['resource_preloading'] : false,
            'preload_count' => 0,
            'dns_prefetch_count' => 0,
            'preconnect_count' => 0,
        );
    }
}
