<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置字段回显回归测试（OPT-003）
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 */

use PHPUnit\Framework\TestCase;
use WPCleanAdmin\Modules\Admin\Settings\Settings_Fields;

class SettingsFieldsEchoTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['wpca_test_options'] = array();
        wpca_flush_settings_cache();
    }

    public function test_echo_prefers_nested_wpca_settings_value(): void {
        $GLOBALS['wpca_test_options']['wpca_settings'] = array(
            'general' => array( 'clean_admin_bar' => 0 ),
        );
        $GLOBALS['wpca_test_options']['clean_admin_bar'] = 1;

        $html = $this->render(
            array(
                'id'          => 'clean_admin_bar',
                'type'        => 'checkbox',
                'description' => '',
                'group'       => 'general',
            )
        );

        $this->assertStringContainsString( 'name="wpca_settings[general][clean_admin_bar]"', $html );
        $this->assertStringNotContainsString( 'checked="checked"', $html );
    }

    public function test_echo_falls_back_to_standalone_option(): void {
        $GLOBALS['wpca_test_options']['clean_dashboard'] = 1;

        $html = $this->render(
            array(
                'id'          => 'clean_dashboard',
                'type'        => 'checkbox',
                'description' => '',
                'group'       => 'general',
            )
        );

        $this->assertStringContainsString( 'checked="checked"', $html );
    }

    private function render( array $args ): string {
        ob_start();
        ( new Settings_Fields() )->render_field( $args );
        return (string) ob_get_clean();
    }
}
