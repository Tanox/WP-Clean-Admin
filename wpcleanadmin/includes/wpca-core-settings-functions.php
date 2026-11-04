<?php
/**
 * WPCleanAdmin Core Settings Functions
 *
 * 承载插件选项的读取、写入与递归消毒，从 wpca-core-functions.php 按职责抽取。
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
 * Get plugin settings
 *
 * @param string $key Specific setting key to get
 * @param mixed $default Default value if setting not found
 * @return mixed Plugin settings or specific setting value
 */
function wpca_get_settings( $key = '', $default = false ) {
    $settings = function_exists( 'get_option' ) ? \get_option( 'wpca_settings', array() ) : array();
    
    if ( empty( $key ) ) {
        return $settings;
    }
    
    return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Update plugin settings
 *
 * @param array $settings Settings to update
 * @return bool Update result
 */
function wpca_update_settings( $settings ) {
    // Validate input is an array
    if ( ! is_array( $settings ) ) {
        return false;
    }
    
    // Filter settings to ensure they are valid
    $validated_settings = array();
    
    foreach ( $settings as $key => $value ) {
        // Sanitize key
        $sanitized_key = function_exists( 'sanitize_key' ) ? \sanitize_key( $key ) : $key;
        
        // Validate and sanitize value based on type
        if ( is_array( $value ) ) {
            // Recursively validate nested arrays
            $sanitized_value = wpca_sanitize_nested_array( $value );
        } elseif ( is_string( $value ) ) {
            $sanitized_value = function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $value ) : $value;
        } elseif ( is_numeric( $value ) ) {
            $sanitized_value = floatval( $value );
        } elseif ( is_bool( $value ) ) {
            $sanitized_value = $value;
        } else {
            // Skip invalid types
            continue;
        }
        
        $validated_settings[ $sanitized_key ] = $sanitized_value;
    }
    
    // Allow other plugins to modify settings before saving
    if ( function_exists( 'apply_filters' ) ) {
        $validated_settings = \apply_filters( 'wpca_update_settings', $validated_settings );
    }
    
    return function_exists( 'update_option' ) ? \update_option( 'wpca_settings', $validated_settings ) : false;
}

/**
 * Sanitize nested array recursively
 *
 * @param array $array Array to sanitize
 * @return array Sanitized array
 */
function wpca_sanitize_nested_array( $array ) {
    $sanitized = array();
    
    foreach ( $array as $key => $value ) {
        $sanitized_key = function_exists( 'sanitize_key' ) ? \sanitize_key( $key ) : $key;
        
        if ( is_array( $value ) ) {
            $sanitized[ $sanitized_key ] = wpca_sanitize_nested_array( $value );
        } elseif ( is_string( $value ) ) {
            $sanitized[ $sanitized_key ] = function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $value ) : $value;
        } elseif ( is_numeric( $value ) ) {
            $sanitized[ $sanitized_key ] = floatval( $value );
        } elseif ( is_bool( $value ) ) {
            $sanitized[ $sanitized_key ] = $value;
        }
    }
    
    return $sanitized;
}

/**
 * Get database settings
 *
 * @param string $key Specific setting key to get
 * @param mixed $default Default value if setting not found
 * @return mixed Database settings or specific setting value
 */
function wpca_get_database_settings( $key = '', $default = false ) {
    $settings = function_exists( 'get_option' ) ? \get_option( 'wpca_database_settings', array() ) : array();
    
    if ( empty( $key ) ) {
        return $settings;
    }
    
    return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Get performance settings
 *
 * @param string $key Specific setting key to get
 * @param mixed $default Default value if setting not found
 * @return mixed Performance settings or specific setting value
 */
function wpca_get_performance_settings( $key = '', $default = false ) {
    $settings = function_exists( 'get_option' ) ? \get_option( 'wpca_performance_settings', array() ) : array();
    
    if ( empty( $key ) ) {
        return $settings;
    }
    
    return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Sanitize array of data
 *
 * @param array $data Data to sanitize
 * @return array Sanitized data
 */
function wpca_sanitize_array( $data ) {
    if ( ! is_array( $data ) ) {
        return function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $data ) : $data;
    }
    
    foreach ( $data as &$value ) {
        if ( is_array( $value ) ) {
            $value = wpca_sanitize_array( $value );
        } else {
            $value = function_exists( 'sanitize_text_field' ) ? \sanitize_text_field( $value ) : $value;
        }
    }
    
    return $data;
}
