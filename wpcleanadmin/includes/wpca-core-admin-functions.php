<?php
/**
 * WPCleanAdmin Core Admin Functions
 *
 * 承载后台页面上下文判定、提示输出与页面标题等后台相关工具，
 * 从 wpca-core-functions.php 按职责抽取。
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
 * Check if user has access to WPCA features
 *
 * @return bool True if user has access, false otherwise
 */
function wpca_current_user_can() {
    return function_exists( 'current_user_can' ) ? \current_user_can( 'manage_options' ) : false;
}

/**
 * Show admin notice
 *
 * @param string $message Notice message
 * @param string $type Notice type (success, error, warning, info)
 */
function wpca_admin_notice( $message, $type = 'info' ) {
    $allowed_types = array( 'success', 'error', 'warning', 'info' );
    $type = in_array( $type, $allowed_types ) ? $type : 'info';
    
    echo '<div class="notice notice-' . ( function_exists( 'esc_attr' ) ? \esc_attr( $type ) : htmlspecialchars( $type, ENT_QUOTES, 'UTF-8' ) ) . ' is-dismissible">';
    echo '<p>' . ( function_exists( 'esc_html' ) ? \esc_html( $message ) : htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' ) ) . '</p>';
    echo '</div>';
}

/**
 * Get current tab from URL
 *
 * @param string $default Default tab if no tab is specified
 * @return string Current tab
 */
function wpca_get_current_tab( $default = 'dashboard' ) {
    return isset( $_GET['tab'] ) ? ( function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $_GET['tab'] ) : $_GET['tab'] ) : $default;
}

/**
 * Get settings page URL
 *
 * @param string $tab Tab to open
 * @return string Settings page URL
 */
function wpca_get_settings_url( $tab = '' ) {
    $url = function_exists( 'admin_url' ) ? \admin_url( 'admin.php?page=wp-clean-admin' ) : '';

    if ( ! empty( $tab ) ) {
        // 使用 add_query_arg 构建查询参数，避免手工拼接导致的 XSS / 重复转义问题
        if ( function_exists( 'add_query_arg' ) ) {
            $url = \add_query_arg( 'tab', $tab, $url );
        } else {
            $url .= '&tab=' . rawurlencode( $tab );
        }
    }

    return $url;
}

/**
 * Check if current page is WPCA settings page
 *
 * @return bool True if current page is WPCA settings page, false otherwise
 */
function wpca_is_settings_page() {
    return isset( $_GET['page'] ) && $_GET['page'] === 'wp-clean-admin';
}

/**
 * Get plugin menu slug
 *
 * @return string Plugin menu slug
 */
function wpca_get_menu_slug() {
    return 'wp-clean-admin';
}

/**
 * Get plugin text domain
 *
 * @return string Plugin text domain
 */
function wpca_get_text_domain() {
    return defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
}

/**
 * Get admin page title
 *
 * @param string $tab Current tab
 * @return string Admin page title
 */
function wpca_get_admin_page_title( $tab = '' ) {
    $translate = function_exists( '__' ) ? function( $text, $domain ) { return __( $text, $domain ); } : function( $text, $domain ) { return $text; };
    
    $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
    $titles = array(
        'dashboard' => $translate( 'WP Clean Admin Dashboard', $text_domain ),
        'settings' => $translate( 'WP Clean Admin Settings', $text_domain ),
        'database' => $translate( 'Database Optimization', $text_domain ),
        'performance' => $translate( 'Performance Settings', $text_domain ),
        'menu' => $translate( 'Menu Management', $text_domain ),
        'permissions' => $translate( 'Permissions', $text_domain ),
        'login' => $translate( 'Login Customization', $text_domain ),
        'cleanup' => $translate( 'Cleanup', $text_domain ),
        'resources' => $translate( 'Resource Optimization', $text_domain ),
        'reset' => $translate( 'Reset Settings', $text_domain )
    );
    
    return isset( $titles[ $tab ] ) ? $titles[ $tab ] : $translate( 'WP Clean Admin', $text_domain );
}
