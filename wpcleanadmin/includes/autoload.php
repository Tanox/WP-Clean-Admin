<?php
/**
 * WPCleanAdmin PSR-4 Autoloader
 *
 * @package WPCleanAdmin
 * @version 1.9.0
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
// Stubs 仅在非 WordPress 环境（如 CLI 运行 PHPUnit）下加载，
// 用于让 autoloader 在缺少 WP 函数时仍能完成类文件加载。
// 真实 WP 环境下 ABSPATH 已定义，整段逻辑被跳过，不会污染全局函数表。
if ( ! defined( 'ABSPATH' ) ) {
    require_once __DIR__ . '/wpca-wordpress-stubs.php';

    if ( file_exists( __DIR__ . '/composer-stub.php' ) ) {
        require_once __DIR__ . '/composer-stub.php';
    }

    if ( file_exists( __DIR__ . '/elementor-stub.php' ) ) {
        require_once __DIR__ . '/elementor-stub.php';
    }

    // 为 IDE/CLI 环境定义 ABSPATH，使后续类文件的守卫放行
    define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

/**
 * Register autoloader for WPCleanAdmin classes
 */
spl_autoload_register( function( $class ) {
    // Check if the class belongs to our namespace
    if ( strpos( $class, 'WPCleanAdmin\\' ) !== 0 ) {
        return;
    }
    
    // Remove namespace prefix
    $class_name = str_replace( 'WPCleanAdmin\\', '', $class );
    
    // Split namespace parts
    $namespace_parts = explode( '\\', $class_name );
    $class_basename = array_pop( $namespace_parts );
    
    // Convert camelCase to kebab-case and replace underscores with hyphens for class name
    $class_file = strtolower( preg_replace( '/(?<!^)[A-Z]/', '-$0', $class_basename ) );
    $class_file = str_replace( '_', '-', $class_file );
    
    // Build directory path from namespace parts
    $dir_path = '';
    if ( ! empty( $namespace_parts ) ) {
        foreach ( $namespace_parts as $part ) {
            $dir_path .= strtolower( $part ) . '/';
        }
    }
    
    // Build full file path
    $plugin_dir = defined( 'WPCA_PLUGIN_DIR' ) ? WPCA_PLUGIN_DIR : dirname( dirname( __FILE__ ) ) . '/';
    
    // Check for files in different locations
    $file_locations = array();
    
    // Check for modular structure first
    if ( ! empty( $namespace_parts ) && $namespace_parts[0] === 'Modules' ) {
        // Rebuild dir path without 'modules' prefix
        $modular_dir_path = '';
        if ( count( $namespace_parts ) > 1 ) {
            $modular_parts = array_slice( $namespace_parts, 1 ); // Remove 'Modules' part
            foreach ( $modular_parts as $part ) {
                $modular_dir_path .= strtolower( $part ) . '/';
            }
        }
        
        // For modular structure
        $file_locations[] = $plugin_dir . 'includes/modules/' . $modular_dir_path . 'class-wpca-' . $class_file . '.php';
        $file_locations[] = $plugin_dir . 'includes/modules/' . $modular_dir_path . $class_file . '.php';
        
        // For modular classes without prefix
        $file_locations[] = $plugin_dir . 'includes/modules/' . $modular_dir_path . $class_basename . '.php';
    } 
    // For AJAX handlers
    elseif ( ! empty( $namespace_parts ) && $namespace_parts[0] === 'AJAX' ) {
        $file_locations[] = $plugin_dir . 'includes/' . $dir_path . $class_file . '-ajax.php';
        $file_locations[] = $plugin_dir . 'includes/ajax/' . $class_file . '-ajax.php';
    } 
    // For Settings handlers
    elseif ( ! empty( $namespace_parts ) && $namespace_parts[0] === 'Settings' ) {
        $file_locations[] = $plugin_dir . 'includes/' . $dir_path . $class_file . '.php';
        $file_locations[] = $plugin_dir . 'includes/settings/' . $class_file . '.php';
    } 
    // For main classes
    else {
        $file_locations[] = $plugin_dir . 'includes/class-wpca-' . $class_file . '.php';
    }
    
    // Check if any of the file locations exist
    foreach ( $file_locations as $file ) {
        if ( file_exists( $file ) ) {
            require_once $file;
            return;
        }
    }
} );


