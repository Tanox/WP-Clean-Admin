<?php
/**
 * WPCleanAdmin Settings Field Renderers Input Tasks
 *
 * 基础输入类字段渲染（text/textarea/checkbox/radio/select/number），公开 API 不变。
 * 主类 `Settings_Field_Renderers` 通过 `use SettingsFieldRenderersInputTasks;` 组合。
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
 * Field renderers: basic input tasks
 */
trait SettingsFieldRenderersInputTasks {

    /**
     * Render text field
     */
    public function render_text_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="text" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="regular-text" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render textarea field
     */
    public function render_textarea_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <textarea id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" class="large-text code" rows="5"><?php echo \esc_textarea( $value ); ?></textarea>
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render checkbox field
     */
    public function render_checkbox_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <label for="<?php echo \esc_attr( $field_id ); ?>">
            <input type="checkbox" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="1" <?php \checked( $value ); ?> />
            <?php echo \wp_kses_post( $desc ); ?>
        </label>
        <?php
    }

    /**
     * Render radio field
     */
    public function render_radio_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $options  = isset( $args['options'] ) ? $args['options'] : array();
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <fieldset>
            <legend class="screen-reader-text"><?php echo \esc_html( $field_id ); ?></legend>
            <?php foreach ( $options as $option_value => $option_label ) : ?>
                <label>
                    <input type="radio" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $option_value ); ?>" <?php \checked( $value, $option_value ); ?> />
                    <?php echo \esc_html( $option_label ); ?>
                </label><br />
            <?php endforeach; ?>
            <?php if ( $desc ) : ?>
                <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
            <?php endif; ?>
        </fieldset>
        <?php
    }

    /**
     * Render select field
     */
    public function render_select_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $options  = isset( $args['options'] ) ? $args['options'] : array();
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <select id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>">
            <?php foreach ( $options as $option_value => $option_label ) : ?>
                <option value="<?php echo \esc_attr( $option_value ); ?>" <?php \selected( $value, $option_value ); ?>><?php echo \esc_html( $option_label ); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Render number field
     */
    public function render_number_field( $args ) {
        $field_id = isset( $args['id'] ) ? $args['id'] : '';
        $value    = isset( $args['value'] ) ? $args['value'] : '';
        $min      = isset( $args['min'] ) ? $args['min'] : '';
        $max      = isset( $args['max'] ) ? $args['max'] : '';
        $step     = isset( $args['step'] ) ? $args['step'] : '';
        $desc     = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="number" id="<?php echo \esc_attr( $field_id ); ?>" name="<?php echo \esc_attr( $field_id ); ?>" value="<?php echo \esc_attr( $value ); ?>" class="small-text" min="<?php echo \esc_attr( $min ); ?>" max="<?php echo \esc_attr( $max ); ?>" step="<?php echo \esc_attr( $step ); ?>" />
        <?php if ( $desc ) : ?>
            <p class="description"><?php echo \wp_kses_post( $desc ); ?></p>
        <?php endif; ?>
        <?php
    }
}
