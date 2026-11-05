<?php
/**
 * WPCleanAdmin Menu Customization Settings
 *
 * 说明：原内联 `<style>`（菜单定制样式）与 `<script>`（拖拽/折叠交互）已于
 * 1.9.12 抽离至 assets/css/wpca-menu-customization.css 与
 * assets/js/wpca-menu-customization.js，由 Settings_Scripts 在设置页统一 enqueue；
 * 本文件现仅保留字段 HTML 渲染，行数 <200。
 *
 * @package WPCleanAdmin
 * @version 1.9.12
 * @update_date 2026-11-05
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Menu Customization Settings Class
 */
class Menu_Customization {

    /**
     * Render menu customization field
     *
     * @return void
     */
    public static function render_menu_customization_field() {
        // Get menu manager instance
        $menu_manager = \WPCleanAdmin\Menu_Manager::getInstance();
        $menu_items = $menu_manager->get_menu_items();

        // Get current settings
        $settings = array();
        if ( function_exists( 'get_option' ) ) {
            $settings = \get_option( 'wpca_settings', array() );
        }
        $menu_settings = isset( $settings['menu'] ) ? $settings['menu'] : array();
        $menu_items_settings = isset( $menu_settings['menu_items'] ) ? $menu_settings['menu_items'] : array();
        $menu_order = isset( $menu_settings['menu_order'] ) ? $menu_settings['menu_order'] : array();

        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';

        ?>
        <div class="wpca-menu-customization">
            <p><?php echo \esc_html( \__( 'Customize admin menu items and their order.', $text_domain ) ); ?></p>

            <div class="wpca-menu-items">
                <div class="wpca-menu-items-header">
                    <h4><?php echo \esc_html( \__( 'Menu Items', $text_domain ) ); ?></h4>
                    <div class="wpca-menu-items-actions">
                        <button type="button" class="button button-small" id="wpca-select-all-menu-items">
                            <?php echo \esc_html( \__( 'Select All', $text_domain ) ); ?>
                        </button>
                        <button type="button" class="button button-small" id="wpca-deselect-all-menu-items">
                            <?php echo \esc_html( \__( 'Deselect All', $text_domain ) ); ?>
                        </button>
                    </div>
                </div>
                <div class="wpca-menu-tree">
                    <?php foreach ( $menu_items as $menu_item ) : ?>
                        <div class="wpca-menu-item wpca-menu-item-level-1">
                            <div class="wpca-menu-item-header">
                                <input type="checkbox" name="wpca_settings[menu][menu_items][<?php echo \esc_attr( $menu_item['slug'] ); ?>]" value="1" <?php echo ( isset( $menu_items_settings[$menu_item['slug']] ) && $menu_items_settings[$menu_item['slug']] ) ? 'checked="checked"' : ''; ?> />
                                <label><?php echo \esc_html( $menu_item['title'] ); ?></label>
                                <span class="wpca-menu-item-slug"><?php echo \esc_html( $menu_item['slug'] ); ?></span>
                                <span class="wpca-menu-item-toggle">▼</span>
                            </div>

                            <?php if ( ! empty( $menu_item['submenu'] ) ) : ?>
                                <div class="wpca-submenu-items">
                                    <?php foreach ( $menu_item['submenu'] as $submenu_item ) : ?>
                                        <div class="wpca-menu-item wpca-menu-item-level-2">
                                            <input type="checkbox" name="wpca_settings[menu][menu_items][<?php echo \esc_attr( $submenu_item['slug'] ); ?>]" value="1" <?php echo ( isset( $menu_items_settings[$submenu_item['slug']] ) && $menu_items_settings[$submenu_item['slug']] ) ? 'checked="checked"' : ''; ?> />
                                            <label><?php echo \esc_html( $submenu_item['title'] ); ?></label>
                                            <span class="wpca-menu-item-slug"><?php echo \esc_html( $submenu_item['slug'] ); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="wpca-menu-order">
                <div class="wpca-menu-order-header">
                    <h4><?php echo \esc_html( \__( 'Menu Order', $text_domain ) ); ?></h4>
                    <div class="wpca-menu-order-actions">
                        <button type="button" class="button button-small" id="wpca-reset-menu-order">
                            <?php echo \esc_html( \__( 'Reset to Default Order', $text_domain ) ); ?>
                        </button>
                    </div>
                </div>
                <p><?php echo \esc_html( \__( 'Drag and drop to reorder menu items:', $text_domain ) ); ?></p>
                <div class="wpca-menu-order-list" id="wpca-menu-order-list">
                    <?php foreach ( $menu_items as $menu_item ) : ?>
                        <div class="wpca-menu-order-item" data-menu-slug="<?php echo \esc_attr( $menu_item['slug'] ); ?>">
                            <span class="wpca-menu-order-handle">☰</span>
                            <span class="wpca-menu-order-title"><?php echo \esc_html( $menu_item['title'] ); ?></span>
                            <input type="hidden" name="wpca_settings[menu][menu_order][]" value="<?php echo \esc_attr( $menu_item['slug'] ); ?>" />
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
