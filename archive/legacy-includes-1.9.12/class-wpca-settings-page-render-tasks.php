<?php
/**
 * WPCleanAdmin Settings Page Render Tasks Trait
 *
 * 设置页完整渲染（HTML + 选项卡）。原内联约 240 行 CSS 与约 40 行 JS 已于
 * 1.9.12 抽离至 assets/css/wpca-settings-page.css 与
 * assets/js/wpca-settings-page.js（'Saving...' 字符串经 wp_localize_script 注入
 * wpcaSettings.saveMessage），由 Settings_Scripts 在设置页统一 enqueue；
 * 本 trait 现仅保留 HTML 渲染，行数 <200。公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.9.12
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置页渲染任务 trait
 */
trait SettingsPageRenderTasks {

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        $text_domain = defined( 'WPCA_TEXT_DOMAIN' ) ? WPCA_TEXT_DOMAIN : 'wp-clean-admin';
        ?>
        <div class="wrap wpca-settings-wrap">
            <h1><?php echo \esc_html( ( function_exists( 'get_admin_page_title' ) ? \get_admin_page_title() : 'WP Clean Admin' ) ); ?></h1>

            <form method="post" action="options.php" id="wpca-settings-form">
                <?php
                if ( function_exists( 'settings_fields' ) ) {
                    \settings_fields( 'wp-clean-admin' );
                }
                ?>

                <!-- Settings Tabs -->
                <div class="wpca-settings-tabs" id="wpca-settings-tabs">
                    <div class="wpca-tabs-nav" id="wpca-tabs-nav">
                        <button type="button" class="wpca-tab-button active" data-tab="general">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php echo \esc_html( \__( 'General', $text_domain ) ); ?>
                        </button>
                        <button type="button" class="wpca-tab-button" data-tab="cleanup">
                            <span class="dashicons dashicons-clipboard"></span>
                            <?php echo \esc_html( \__( 'Cleanup', $text_domain ) ); ?>
                        </button>
                        <button type="button" class="wpca-tab-button" data-tab="performance">
                            <span class="dashicons dashicons-chart-line"></span>
                            <?php echo \esc_html( \__( 'Performance', $text_domain ) ); ?>
                        </button>
                        <button type="button" class="wpca-tab-button" data-tab="security">
                            <span class="dashicons dashicons-shield"></span>
                            <?php echo \esc_html( \__( 'Security', $text_domain ) ); ?>
                        </button>
                        <button type="button" class="wpca-tab-button" data-tab="diagnostics">
                            <span class="dashicons dashicons-heart"></span>
                            <?php echo \esc_html( \__( 'Diagnostics', $text_domain ) ); ?>
                        </button>
                    </div>

                    <div class="wpca-tabs-content" id="wpca-tabs-content">
                        <!-- General Tab -->
                        <div class="wpca-tab-content active" id="wpca-tab-general">
                            <?php
                            if ( function_exists( 'do_settings_sections' ) ) {
                                // Render only general settings
                                \do_settings_sections( 'wp-clean-admin' );
                            }
                            ?>
                        </div>

                        <!-- Cleanup Tab -->
                        <div class="wpca-tab-content" id="wpca-tab-cleanup">
                            <?php
                            if ( function_exists( 'do_settings_sections' ) ) {
                                // Render only cleanup settings
                                \do_settings_sections( 'wp-clean-admin' );
                            }
                            ?>
                        </div>

                        <!-- Performance Tab -->
                        <div class="wpca-tab-content" id="wpca-tab-performance">
                            <?php
                            if ( function_exists( 'do_settings_sections' ) ) {
                                // Render only performance settings
                                \do_settings_sections( 'wp-clean-admin' );
                            }
                            ?>
                        </div>

                        <!-- Security Tab -->
                        <div class="wpca-tab-content" id="wpca-tab-security">
                            <?php
                            if ( function_exists( 'do_settings_sections' ) ) {
                                // Render only security settings
                                \do_settings_sections( 'wp-clean-admin' );
                            }
                            ?>
                        </div>

                        <!-- Diagnostics Tab -->
                        <div class="wpca-tab-content" id="wpca-tab-diagnostics">
                            <?php
                            if ( function_exists( 'do_settings_sections' ) ) {
                                \do_settings_sections( 'wp-clean-admin' );
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="wpca-settings-submit">
                    <?php
                    if ( function_exists( 'submit_button' ) ) {
                        \submit_button( \__( 'Save Changes', $text_domain ), 'primary', 'submit', false, array( 'id' => 'wpca-save-button' ) );
                    }
                    ?>
                    <div class="wpca-save-message" id="wpca-save-message"></div>
                </div>
            </form>
        </div>
        <?php
    }
}
