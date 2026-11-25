<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 菜单定制器单 option 源、默认 AJAX 注册与 legacy 迁移回归测试
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 */

use PHPUnit\Framework\TestCase;
use WPCleanAdmin\Modules\Admin\Classes\Menu_Customizer;
use WPCleanAdmin\Modules\Admin\Classes\Menu_Customizer_Options;
use WPCleanAdmin\Modules\Admin\Classes\Menu_Customizer_Render;

class MenuCustomizerOptionsTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['wpca_test_options'] = array();
        $GLOBALS['wpca_test_actions'] = array();
        $GLOBALS['wpca_test_filters'] = array();

        // 重置请求内迁移标记，保证各用例独立
        $property = new ReflectionProperty( Menu_Customizer_Options::class, 'legacy_migrated' );
        $property->setAccessible( true );
        $property->setValue( false );
    }

    public function test_single_option_key(): void {
        $this->assertSame( 'wpca_menu_customizer_settings', Menu_Customizer_Options::OPTION_KEY );
    }

    public function test_save_and_get_use_only_own_key(): void {
        $options = new Menu_Customizer_Options();
        $options->save_settings(
            array(
                'enabled'    => true,
                'menu_items' => array( 'x' => array( 'hidden' => true ) ),
            )
        );

        $this->assertArrayHasKey( 'wpca_menu_customizer_settings', $GLOBALS['wpca_test_options'] );
        $this->assertArrayNotHasKey( 'wpca_settings', $GLOBALS['wpca_test_options'] );
        $settings = $options->get_settings();
        $this->assertTrue( $settings['menu_items']['x']['hidden'] );
    }

    public function test_ajax_actions_registered_without_legacy_flag(): void {
        $customizer = Menu_Customizer::getInstance();
        $customizer->init();

        foreach ( array( 'save', 'reset', 'export', 'import' ) as $action ) {
            $this->assertArrayHasKey(
                'wp_ajax_wpca_' . $action . '_menu_customizer',
                $GLOBALS['wpca_test_actions']
            );
        }
    }

    public function test_legacy_menu_customizer_migrated(): void {
        $GLOBALS['wpca_test_options']['wpca_settings'] = array(
            'menu_customizer' => 1,
            'menu_items'      => array( 'x' => array( 'hidden' => true ) ),
        );

        $settings = ( new Menu_Customizer_Options() )->get_settings();

        $this->assertTrue( $settings['menu_items']['x']['hidden'] );
        $this->assertArrayHasKey( 'wpca_menu_customizer_settings', $GLOBALS['wpca_test_options'] );
        $this->assertArrayHasKey( 'wpca_settings', $GLOBALS['wpca_test_options'] );
    }

    public function test_export_import_go_through_options(): void {
        $render = new Menu_Customizer_Render( new Menu_Customizer_Options() );
        $result = $render->import_settings(
            array( 'admin_bar_items' => array( 'wp-logo' => array( 'hidden' => true ) ) )
        );

        $this->assertTrue( $result );
        $saved = $GLOBALS['wpca_test_options']['wpca_menu_customizer_settings'];
        $this->assertTrue( $saved['admin_bar_items']['wp-logo']['hidden'] );
        $exported = $render->export_settings();
        $this->assertTrue( $exported['admin_bar_items']['wp-logo']['hidden'] );
    }
}
