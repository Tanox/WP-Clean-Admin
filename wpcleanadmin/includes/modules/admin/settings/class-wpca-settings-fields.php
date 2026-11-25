<?php
/**
 * WPCleanAdmin Settings Fields
 *
 * @package WPCleanAdmin\Modules\Admin\Settings
 * @version 1.9.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */


namespace WPCleanAdmin\Modules\Admin\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-wpca-settings-field-renderers.php';
require_once __DIR__ . '/class-wpca-settings-field-definitions.php';
require_once __DIR__ . '/class-wpca-settings-fields-render-tasks.php';

/**
 * Settings Fields class
 */
class Settings_Fields {

    use SettingsFieldsRenderTasks;

    /**
     * 字段渲染器
     *
     * @var Settings_Field_Renderers
     */
    private $renderers;

    /**
     * Constructor
     */
    public function __construct() {
        $this->renderers = new Settings_Field_Renderers();
    }

    /**
     * Register all settings fields
     */
    public function register_fields() {
        // General settings
        if ( function_exists( '\add_settings_section' ) ) {
            \add_settings_section(
                'wpca_general_section',
                \__( 'General Settings', 'wp-clean-admin' ),
                array( $this, 'render_general_section' ),
                'wp-clean-admin-general'
            );
        }

        foreach ( Settings_Field_Definitions::general_fields() as $field ) {
            if ( function_exists( '\add_settings_field' ) ) {
                \add_settings_field(
                    $field['id'],
                    $field['title'],
                    array( $this, 'render_field' ),
                    'wp-clean-admin-' . ( isset( $field['group'] ) ? $field['group'] : 'general' ),
                    $field['section'],
                    $field
                );
            }
        }
    }

    /**
     * Render a single field
     *
     * @param array $args
     */
    public function render_field( $args ) {
        $callback = 'render_' . ( isset( $args['type'] ) ? $args['type'] : '' ) . '_field';
        if ( ! method_exists( $this->renderers, $callback ) ) {
            return;
        }

        $id      = isset( $args['id'] ) ? $args['id'] : '';
        $group   = isset( $args['group'] ) ? $args['group'] : 'general';
        $default = isset( $args['value'] ) ? $args['value'] : '';

        $settings = function_exists( '\wpca_get_settings' )
            ? \wpca_get_settings()
            : \get_option( 'wpca_settings', array() );

        $nested = ( is_array( $settings ) && isset( $settings[ $group ] )
            && is_array( $settings[ $group ] ) && array_key_exists( $id, $settings[ $group ] ) )
            ? $settings[ $group ][ $id ]
            : null;

        // 嵌套值优先；未保存时回退到独立 option 键（array_key_exists 保证 0/false 正确回显）
        $args['value'] = null !== $nested ? $nested : \get_option( $id, $default );
        $args['name']  = 'wpca_settings[' . $group . '][' . $id . ']';

        $this->renderers->{$callback}( $args );
    }

    /**
     * Render general section description
     */
    public function render_general_section() {
        echo '<p>' . \esc_html( \__( 'Configure general plugin behavior.', 'wp-clean-admin' ) ) . '</p>';
    }
}
