<?php
/**
 * WPCleanAdmin Settings Page Render Tasks Trait
 *
 * 设置页完整渲染（HTML + 选项卡交互 JS + 内联样式），
 * 从 class-wpca-settings.php 按职责抽离，公开方法契约不变。
 *
 * 注：render_settings_page() 内联约 240 行 CSS（与约 40 行 JS），
 * 纯 trait 拆分后本 trait 仍 >200 行；后续可将该 CSS 抽离至
 * assets/css/wpca-settings.css、JS 抽离至 assets/js/wpca-settings-page.js
 * 并改 enqueue 加载，方可彻底达标。
 *
 * @package WPCleanAdmin
 * @version 1.8.11
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
                <div class="wpca-settings-tabs">
                    <div class="wpca-tabs-nav">
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

                    <div class="wpca-tabs-content">
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

        <script type="text/javascript">
        (function($) {
            'use strict';

            $(document).ready(function() {
                // Settings tabs functionality
                const tabButtons = $('.wpca-tab-button');
                const tabContents = $('.wpca-tab-content');

                tabButtons.on('click', function() {
                    const tabId = $(this).data('tab');

                    // Remove active class from all tabs
                    tabButtons.removeClass('active');
                    tabContents.removeClass('active');

                    // Add active class to selected tab
                    $(this).addClass('active');
                    $(`#wpca-tab-${tabId}`).addClass('active');

                    // Scroll to top of settings
                    $('html, body').animate({
                        scrollTop: $('.wpca-settings-form').offset().top - 20
                    }, 300);
                });

                // Form submission handling
                $('#wpca-settings-form').on('submit', function(e) {
                    // Show saving message
                    $('#wpca-save-message').html('<span class="wpca-saving">' + <?php echo \json_encode( \__( 'Saving...', $text_domain ) ); ?> + '</span>');
                });

                // Add toggle functionality to setting sections
                $('.wpca-settings-section h3').on('click', function() {
                    const section = $(this).closest('.wpca-settings-section');
                    const content = section.nextUntil('.wpca-settings-section');

                    section.toggleClass('collapsed');
                    content.slideToggle();
                });
            });
        })(jQuery);
        </script>

        <style type="text/css">
            .wpca-settings-wrap {
                max-width: 1200px;
                margin: 0 auto;
            }

            .wpca-settings-tabs {
                margin: 20px 0;
                background: #fff;
                border: 1px solid #e1e1e1;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .wpca-tabs-nav {
                display: flex;
                flex-wrap: wrap;
                background: #f8f9fa;
                border-bottom: 1px solid #e1e1e1;
                padding: 0;
                margin: 0;
            }

            .wpca-tab-button {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: transparent;
                border: none;
                border-bottom: 3px solid transparent;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 14px;
                font-weight: 500;
                color: #555;
            }

            .wpca-tab-button:hover {
                background: rgba(0, 124, 186, 0.05);
                color: #007cba;
            }

            .wpca-tab-button.active {
                background: #fff;
                color: #007cba;
                border-bottom-color: #007cba;
            }

            .wpca-tab-button .dashicons {
                font-size: 16px;
                width: 16px;
                height: 16px;
            }

            .wpca-tabs-content {
                padding: 20px;
            }

            .wpca-tab-content {
                display: none;
            }

            .wpca-tab-content.active {
                display: block;
                animation: wpca-fade-in 0.3s ease;
            }

            .wpca-settings-section {
                margin-bottom: 20px;
                padding: 20px;
                background: #f8f9fa;
                border: 1px solid #e1e1e1;
                border-radius: 6px;
                transition: all 0.3s ease;
            }

            .wpca-settings-section:hover {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .wpca-settings-section h3 {
                margin: 0 0 15px;
                padding: 0;
                font-size: 16px;
                font-weight: 600;
                color: #333;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .wpca-settings-section h3:after {
                content: '▼';
                font-size: 12px;
                color: #666;
                transition: transform 0.3s ease;
            }

            .wpca-settings-section.collapsed h3:after {
                transform: rotate(-90deg);
            }

            .wpca-settings-section .form-table {
                margin: 0;
                background: #fff;
                border: 1px solid #e1e1e1;
                border-radius: 4px;
                overflow: hidden;
            }

            .wpca-settings-section .form-table tr {
                border-bottom: 1px solid #f0f0f0;
                transition: background-color 0.2s ease;
            }

            .wpca-settings-section .form-table tr:last-child {
                border-bottom: none;
            }

            .wpca-settings-section .form-table tr:hover {
                background-color: #f8f9fa;
            }

            .wpca-settings-section .form-table th {
                padding: 12px 15px;
                width: 300px;
                font-weight: 500;
                color: #333;
                background: #fafafa;
                border-right: 1px solid #f0f0f0;
            }

            .wpca-settings-section .form-table td {
                padding: 12px 15px;
                color: #555;
            }

            .wpca-settings-submit {
                margin: 30px 0;
                padding: 20px;
                background: #f8f9fa;
                border: 1px solid #e1e1e1;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            #wpca-save-button {
                font-size: 14px;
                padding: 8px 20px;
                font-weight: 500;
            }

            .wpca-save-message {
                font-size: 14px;
            }

            .wpca-saving {
                color: #007cba;
                font-weight: 500;
            }

            /* Responsive Design */
            @media screen and (max-width: 782px) {
                .wpca-settings-wrap {
                    padding: 0 10px;
                }

                .wpca-tabs-nav {
                    flex-direction: column;
                }

                .wpca-tab-button {
                    justify-content: flex-start;
                    border-bottom: 1px solid #e1e1e1;
                }

                .wpca-tab-button.active {
                    border-bottom: 1px solid #e1e1e1;
                    border-left: 3px solid #007cba;
                }

                .wpca-settings-section .form-table {
                    display: block;
                }

                .wpca-settings-section .form-table tr {
                    display: block;
                    border-bottom: 1px solid #f0f0f0;
                }

                .wpca-settings-section .form-table th,
                .wpca-settings-section .form-table td {
                    display: block;
                    width: 100%;
                    border-right: none;
                    border-bottom: 1px solid #f0f0f0;
                }

                .wpca-settings-section .form-table th {
                    background: #fafafa;
                    padding-bottom: 8px;
                }

                .wpca-settings-section .form-table td {
                    padding-top: 8px;
                    padding-bottom: 12px;
                }

                .wpca-settings-submit {
                    flex-direction: column;
                    gap: 15px;
                    align-items: stretch;
                }

                #wpca-save-button {
                    width: 100%;
                    text-align: center;
                }
            }

            /* Animation */
            @keyframes wpca-fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        <?php
    }
}
