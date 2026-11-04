<?php
/**
 * WPCleanAdmin Core Path Functions
 *
 * 承载插件路径、URL 与模板加载工具，从 wpca-core-functions.php 按职责抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.10
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get plugin URL
 *
 * @param string $path Path relative to plugin directory
 * @return string Full plugin URL
 */
function wpca_get_plugin_url( $path = '' ) {
    $base_url = defined( 'WPCA_PLUGIN_URL' ) ? WPCA_PLUGIN_URL : '';
    $url = rtrim( $base_url, '/' );
    return $url . '/' . ltrim( $path, '/' );
}

/**
 * Get plugin directory path
 *
 * @param string $path Path relative to plugin directory
 * @return string Full plugin directory path
 */
function wpca_get_plugin_dir( $path = '' ) {
    $base_dir = defined( 'WPCA_PLUGIN_DIR' ) ? WPCA_PLUGIN_DIR : dirname( dirname( __FILE__ ) ) . '/';
    $dir = rtrim( $base_dir, '/' );
    return $dir . '/' . ltrim( $path, '/' );
}

/**
 * Get assets URL
 *
 * @param string $asset_path Asset path relative to assets directory
 * @return string Full asset URL
 */
function wpca_get_asset_url( $asset_path = '' ) {
    return wpca_get_plugin_url( '/assets/' . ltrim( $asset_path, '/' ) );
}

/**
 * Get template path
 *
 * @param string $template_name Template name
 * @return string Full template path
 */
function wpca_get_template_path( $template_name ) {
    return wpca_get_plugin_dir( '/includes/templates/' . $template_name );
}

/**
 * Load template
 *
 * @param string $template_name Template name
 * @param array $args Arguments to pass to template
 */
function wpca_load_template( $template_name, $args = array() ) {
    $template_path = wpca_get_template_path( $template_name );

    if ( file_exists( $template_path ) ) {
        // EXTR_SKIP 避免覆盖已存在的变量（如 $template_name、$template_path），
        // 同时 $args 必须是数组——非数组直接跳过，防止 extract() 触发警告。
        if ( is_array( $args ) ) {
            extract( $args, EXTR_SKIP );
        }
        include $template_path;
    }
}
