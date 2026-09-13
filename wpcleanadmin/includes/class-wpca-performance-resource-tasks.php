<?php
/**
 * WPCleanAdmin Performance Resource Tasks Trait
 *
 * 资源压缩、合并与 URL 改写（CSS / JS minify & combine），
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
 * 资源压缩合并任务 trait
 */
trait PerformanceResourceTasks {

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
     * Basic JS minification - removes comments and extra whitespace
     * For production use, consider using a library like JSMin or Terser
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
