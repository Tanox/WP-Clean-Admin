<?php
/**
 * WPCleanAdmin Extension API Menu Tasks Trait
 *
 * 扩展菜单项与设置区段/字段注册，从 class-wpca-extension-api.php 按职责抽离，公开方法契约不变。
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
 * 扩展菜单/设置注册任务 trait
 */
trait ExtensionMenuTasks {

    /**
     * Create custom menu item
     *
     * @param array $menu_item Menu item configuration
     * @return bool Success status
     */
    public function register_menu_item( $menu_item ) {
        $required = array( 'title', 'slug', 'callback' );

        foreach ( $required as $field ) {
            if ( ! isset( $menu_item[ $field ] ) || empty( $menu_item[ $field ] ) ) {
                return false;
            }
        }

        $defaults = array(
            'parent' => 'wp-clean-admin',
            'capability' => 'manage_options',
            'icon' => 'dashicons-admin-plugins',
            'position' => null,
        );

        $menu_item = wp_parse_args( $menu_item, $defaults );

        \add_action( 'wpca_admin_menu', function() use ( $menu_item ) {
            \add_submenu_page(
                $menu_item['parent'],
                $menu_item['title'],
                $menu_item['title'],
                $menu_item['capability'],
                $menu_item['slug'],
                $menu_item['callback'],
                $menu_item['position']
            );
        });

        return true;
    }

    /**
     * Register settings section
     *
     * @param array $section Section configuration
     * @return bool Success status
     */
    public function register_settings_section( $section ) {
        $required = array( 'id', 'title', 'callback' );

        foreach ( $required as $field ) {
            if ( ! isset( $section[ $field ] ) || empty( $section[ $field ] ) ) {
                return false;
            }
        }

        $defaults = array(
            'page' => 'wpca_settings',
        );

        $section = wp_parse_args( $section, $defaults );

        \add_action( 'wpca_settings_sections', function() use ( $section ) {
            \add_settings_section(
                $section['id'],
                $section['title'],
                $section['callback'],
                $section['page']
            );
        });

        return true;
    }

    /**
     * Register settings field
     *
     * @param array $field Field configuration
     * @return bool Success status
     */
    public function register_settings_field( $field ) {
        $required = array( 'id', 'title', 'callback', 'section' );

        foreach ( $required as $key ) {
            if ( ! isset( $field[ $key ] ) || empty( $field[ $key ] ) ) {
                return false;
            }
        }

        $defaults = array(
            'page' => 'wpca_settings',
            'args' => array(),
        );

        $field = wp_parse_args( $field, $defaults );

        \add_action( 'wpca_settings_fields', function() use ( $field ) {
            \add_settings_field(
                $field['id'],
                $field['title'],
                $field['callback'],
                $field['page'],
                $field['section'],
                $field['args']
            );
        });

        return true;
    }
}
