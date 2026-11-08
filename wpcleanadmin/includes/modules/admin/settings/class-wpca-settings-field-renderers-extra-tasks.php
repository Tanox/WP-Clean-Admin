<?php
/**
 * WPCleanAdmin Settings Field Renderers Extra Tasks
 *
 * 扩展输入类字段渲染（color/date/email/url/password），公开 API 不变。
 * 主类 `Settings_Field_Renderers` 通过 `use SettingsFieldRenderersExtraTasks;` 组合。
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Field renderers: extra input tasks
 */
trait SettingsFieldRenderersExtraTasks {

    /**
     * Render color field
     */
    public function render_color_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="color" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render date field
     */
    public function render_date_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="date" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="regular-text" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render email field
     */
    public function render_email_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="email" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="regular-text" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render URL field
     */
    public function render_url_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="url" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="regular-text" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render password field
     */
    public function render_password_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="password" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="regular-text" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }
}
