<?php
/**
 * WPCleanAdmin Performance Resource Combine Tasks
 *
 * 资源合并（CSS / JS combine 与合并文件生成），从 PerformanceResourceTasks 抽离。
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
 * 资源合并任务（trait）。
 */
trait PerformanceResourceCombineTasks {

    /**
     * Optimize resources
     */
    public function optimize_resources() {
        // Load settings
        $settings = \wpca_get_settings();

        if ( isset( $settings['performance'] ) ) {
            // Minify CSS and JS
            if ( isset( $settings['performance']['minify_resources'] ) && $settings['performance']['minify_resources'] ) {
                \add_filter( 'style_loader_tag', array( $this, 'minify_css' ) );
                \add_filter( 'script_loader_tag', array( $this, 'minify_js' ) );
            }

            // Combine CSS and JS
            if ( isset( $settings['performance']['combine_resources'] ) && $settings['performance']['combine_resources'] ) {
                \add_filter( 'stylesheet_uri', array( $this, 'combine_css' ) );
                \add_filter( 'script_uri', array( $this, 'combine_js' ) );
            }
        }
    }

    /**
     * Combine CSS files
     *
     * @param string $uri CSS URI
     * @return string Modified CSS URI
     */
    public function combine_css( $uri ) {
        // Check if combination is enabled
        $settings = \wpca_get_settings();
        if ( ! isset( $settings['performance']['combine_css'] ) || ! $settings['performance']['combine_css'] ) {
            return $uri;
        }

        return $uri;
    }

    /**
     * Combine multiple CSS files into one
     *
     * @param array $css_urls Array of CSS URLs to combine
     * @return string|null Combined CSS file URL or null on failure
     */
    public function combine_css_files( $css_urls = array() ) {
        if ( empty( $css_urls ) || ! is_array( $css_urls ) ) {
            return null;
        }

        $combined_content = '';
        $content_hash = '';

        foreach ( $css_urls as $url ) {
            if ( function_exists( '\wp_remote_get' ) && function_exists( '\is_wp_error' ) && function_exists( '\wp_remote_retrieve_response_code' ) && function_exists( '\wp_remote_retrieve_body' ) ) {
                $response = \wp_remote_get( $url );
                if ( ! \is_wp_error( $response ) && \wp_remote_retrieve_response_code( $response ) === 200 ) {
                    $content = \wp_remote_retrieve_body( $response );
                    // Minify the content first
                    $content = $this->minify_css_content( $content );
                    $combined_content .= $content . "\n";
                    $content_hash = md5( $content_hash . $content );
                }
            }
        }

        if ( empty( $combined_content ) ) {
            return null;
        }

        // Create combined filename
        $hash = md5( implode( ',', $css_urls ) );
        $combined_filename = 'wpca-combined-' . $hash . '.css';
        if ( function_exists( '\wp_upload_dir' ) ) {
            $upload_dir = \wp_upload_dir();
            $combined_dir = $upload_dir['basedir'] . '/wpca-cache';

            // Create directory if it doesn't exist
            if ( ! file_exists( $combined_dir ) ) {
                if ( function_exists( '\wp_mkdir_p' ) ) {
                    \wp_mkdir_p( $combined_dir );
                }
            }
        } else {
            return null;
        }

        $combined_path = $combined_dir . '/' . $combined_filename;

        // Write combined content to file
        $result = file_put_contents( $combined_path, $combined_content );

        if ( $result !== false ) {
            return $upload_dir['baseurl'] . '/wpca-cache/' . $combined_filename;
        }

        return null;
    }

    /**
     * Combine JS files
     *
     * @param string $uri JS URI
     * @return string Modified JS URI
     */
    public function combine_js( $uri ) {
        // Check if combination is enabled
        $settings = \wpca_get_settings();
        if ( ! isset( $settings['performance']['combine_js'] ) || ! $settings['performance']['combine_js'] ) {
            return $uri;
        }

        return $uri;
    }

    /**
     * Combine multiple JS files into one
     *
     * @param array $js_urls Array of JS URLs to combine
     * @return string|null Combined JS file URL or null on failure
     */
    public function combine_js_files( $js_urls = array() ) {
        if ( empty( $js_urls ) || ! is_array( $js_urls ) ) {
            return null;
        }

        $combined_content = '';

        foreach ( $js_urls as $url ) {
            if ( function_exists( '\wp_remote_get' ) && function_exists( '\is_wp_error' ) && function_exists( '\wp_remote_retrieve_response_code' ) && function_exists( '\wp_remote_retrieve_body' ) ) {
                $response = \wp_remote_get( $url );
                if ( ! \is_wp_error( $response ) && \wp_remote_retrieve_response_code( $response ) === 200 ) {
                    $content = \wp_remote_retrieve_body( $response );
                    // Ensure $content is a string
                    $content = is_string( $content ) ? $content : '';
                    // Add semicolon if needed between files
                    if ( ! empty( $combined_content ) ) {
                        $content = ';' . trim( $content );
                    }
                    $combined_content .= $content . "\n";
                }
            }
        }

        if ( empty( $combined_content ) ) {
            return null;
        }

        // Create combined filename
        $hash = md5( implode( ',', $js_urls ) );
        $combined_filename = 'wpca-combined-' . $hash . '.js';
        if ( function_exists( '\wp_upload_dir' ) ) {
            $upload_dir = \wp_upload_dir();
            $combined_dir = $upload_dir['basedir'] . '/wpca-cache';

            // Create directory if it doesn't exist
            if ( ! file_exists( $combined_dir ) ) {
                if ( function_exists( '\wp_mkdir_p' ) ) {
                    \wp_mkdir_p( $combined_dir );
                }
            }
        } else {
            return null;
        }

        $combined_path = $combined_dir . '/' . $combined_filename;

        // Write combined content to file
        $result = file_put_contents( $combined_path, $combined_content );

        if ( $result !== false ) {
            return $upload_dir['baseurl'] . '/wpca-cache/' . $combined_filename;
        }

        return null;
    }
}
