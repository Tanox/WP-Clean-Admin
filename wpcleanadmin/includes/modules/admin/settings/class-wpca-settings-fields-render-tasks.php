<?php
/**
 * WPCleanAdmin Settings Fields Render Tasks
 *
 * 承载 Settings_Fields 对各字段类型渲染器的代理方法，从主类抽取。
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.18
 */

namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置字段渲染代理（trait）。
 */
trait SettingsFieldsRenderTasks {

    /**
     * Render text field (delegated)
     */
    public function render_text_field( $args ) { $this->renderers->render_text_field( $args ); }

    /**
     * Render textarea field (delegated)
     */
    public function render_textarea_field( $args ) { $this->renderers->render_textarea_field( $args ); }

    /**
     * Render checkbox field (delegated)
     */
    public function render_checkbox_field( $args ) { $this->renderers->render_checkbox_field( $args ); }

    /**
     * Render radio field (delegated)
     */
    public function render_radio_field( $args ) { $this->renderers->render_radio_field( $args ); }

    /**
     * Render select field (delegated)
     */
    public function render_select_field( $args ) { $this->renderers->render_select_field( $args ); }

    /**
     * Render number field (delegated)
     */
    public function render_number_field( $args ) { $this->renderers->render_number_field( $args ); }

    /**
     * Render color field (delegated)
     */
    public function render_color_field( $args ) { $this->renderers->render_color_field( $args ); }

    /**
     * Render date field (delegated)
     */
    public function render_date_field( $args ) { $this->renderers->render_date_field( $args ); }

    /**
     * Render email field (delegated)
     */
    public function render_email_field( $args ) { $this->renderers->render_email_field( $args ); }

    /**
     * Render URL field (delegated)
     */
    public function render_url_field( $args ) { $this->renderers->render_url_field( $args ); }

    /**
     * Render password field (delegated)
     */
    public function render_password_field( $args ) { $this->renderers->render_password_field( $args ); }
}
