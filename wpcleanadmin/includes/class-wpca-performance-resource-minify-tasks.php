<?php
/**
 * WPCleanAdmin Performance Resource Minify Tasks
 *
 * 资源压缩（CSS / JS minify 与 minified URL 解析），从 PerformanceResourceTasks 抽离。
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
 * 资源压缩任务（trait）。
 */
trait PerformanceResourceMinifyTasks {

    /**
     * Minify CSS
     *
     * @param string $tag CSS tag
     * @return string Modified CSS tag
     */
    public function minify_css( $tag ) {
        // Check if minification is enabled
        $settings = \wpca_get_settings();
        if ( ! isset( $settings['performance']['minify_css'] ) || ! $settings['performance']['minify_css'] ) {
            return $tag;
        }

        // Extract CSS URL from tag
        if ( preg_match( '/href=["\']([^"\']+\.css[^"\']*)["\']/', $tag, $matches ) ) {
            $css_url = $matches[1];
            $minified_url = $this->get_minified_css_url( $css_url );

            if ( $minified_url ) {
                $tag = str_replace( $css_url, $minified_url, $tag );
            }
        }

        return $tag;
    }

    /**
     * Get minified CSS URL
     *
     * @param string $original_url Original CSS URL
     * @return string Minified CSS URL or original if not found
     */
    private function get_minified_css_url( $original_url ) {
        $parsed_url = parse_url( $original_url );

        if ( ! isset( $parsed_url['path'] ) ) {
            return $original_url;
        }

        $path = $parsed_url['path'];
        $dirname = dirname( $path );
        $basename = basename( $path );
        $minified_basename = preg_replace( '/\.css$/i', '.min.css', $basename );
        $minified_path = $dirname . '/' . $minified_basename;

        // Check if minified file exists
        $document_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : ABSPATH;
        $full_path = $document_root . ltrim( $minified_path, '/' );

        if ( file_exists( $full_path ) ) {
            // Build the full URL
            $site_url = function_exists( '\site_url' ) ? \site_url() : \get_option( 'siteurl' );
            $minified_url = $site_url . $minified_path;

            // Preserve query string
            if ( isset( $parsed_url['query'] ) ) {
                $minified_url .= '?' . $parsed_url['query'];
            }

            return $minified_url;
        }

        return $original_url;
    }

    /**
     * Minify CSS content
     *
     * @param string $css CSS content
     * @return string Minified CSS content
     */
    public function minify_css_content( $css ) {
        if ( empty( $css ) ) {
            return $css;
        }

        // Remove comments
        $css = preg_replace( '/\/\*[\s\S]*?\*\//', '', $css );

        // Remove whitespace
        $css = preg_replace( '/\s+/', ' ', $css );

        // Remove space around special characters
        $css = preg_replace( '/\s*([{}:;,>+~])\s*/', '$1', $css );

        // Remove last semicolon before }
        $css = preg_replace( '/;}/', '}', $css );

        // Trim
        $css = trim( $css );

        return $css;
    }

    /**
     * Minify JS
     *
     * @param string $tag JS tag
     * @return string Modified JS tag
     */
    public function minify_js( $tag ) {
        // Check if minification is enabled
        $settings = \wpca_get_settings();
        if ( ! isset( $settings['performance']['minify_js'] ) || ! $settings['performance']['minify_js'] ) {
            return $tag;
        }

        // Extract JS URL from tag
        if ( preg_match( '/src=["\']([^"\']+\.js[^"\']*)["\']/', $tag, $matches ) ) {
            $js_url = $matches[1];
            $minified_url = $this->get_minified_js_url( $js_url );

            if ( $minified_url ) {
                $tag = str_replace( $js_url, $minified_url, $tag );
            }
        }

        return $tag;
    }

    /**
     * Get minified JS URL
     *
     * @param string $original_url Original JS URL
     * @return string Minified JS URL or original if not found
     */
    private function get_minified_js_url( $original_url ) {
        $parsed_url = parse_url( $original_url );

        if ( ! isset( $parsed_url['path'] ) ) {
            return $original_url;
        }

        $path = $parsed_url['path'];
        $dirname = dirname( $path );
        $basename = basename( $path );
        $minified_basename = preg_replace( '/\.js$/i', '.min.js', $basename );
        $minified_path = $dirname . '/' . $minified_basename;

        // Check if minified file exists
        $document_root = isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : ABSPATH;
        $full_path = $document_root . ltrim( $minified_path, '/' );

        if ( file_exists( $full_path ) ) {
            // Build the full URL
            $site_url = function_exists( '\site_url' ) ? \site_url() : \get_option( 'siteurl' );
            $minified_url = $site_url . $minified_path;

            // Preserve query string
            if ( isset( $parsed_url['query'] ) ) {
                $minified_url .= '?' . $parsed_url['query'];
            }

            return $minified_url;
        }

        return $original_url;
    }

    /**
     * Minify JS content
     *
     * @param string $js JS content
     * @return string Minified JS content
     */
    public function minify_js_content( $js ) {
        if ( empty( $js ) ) {
            return $js;
        }

        // Remove single-line comments (but not http:// URLs)
        $js = preg_replace( '/\/\/(?![a-zA-Z]+:\/\/)(.*?)[\r\n]/', '$1', $js );

        // Remove multi-line comments
        $js = preg_replace( '/\/\*[\s\S]*?\*\//', '', $js );

        // Remove extra whitespace
        $js = preg_replace( '/\s+/', ' ', $js );

        // Remove space around operators
        $js = preg_replace( '/\s*([{}();,.=!<>+\-*\/&|?%:~])\s*/', '$1', $js );

        // Trim
        $js = trim( $js );

        return $js;
    }
}
