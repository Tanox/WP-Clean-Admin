<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REST 访问限制回归测试（OPT-005）
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 */

use PHPUnit\Framework\TestCase;
use WPCleanAdmin\Modules\Utilities\Classes\Resources;

class RestApiRestrictionTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['wpca_test_options']     = array();
        $GLOBALS['wpca_test_actions']      = array();
        $GLOBALS['wpca_test_filters']      = array();
        $GLOBALS['wpca_test_logged_in']   = false;

        // 设置读取带请求级记忆化（OPT-008），用例改写选项后必须失效缓存，
        // 否则会读到上一个用例遗留的快照。
        wpca_flush_settings_cache();
    }

    public function test_filter_registered_on_rest_api_init_when_enabled(): void {
        $GLOBALS['wpca_test_options']['wpca_settings'] = array(
            'security' => array( 'restrict_rest_api' => 1 ),
        );

        $resources = Resources::getInstance();
        $resources->register_rest_restriction();

        $this->assertArrayHasKey( 'rest_authentication_errors', $GLOBALS['wpca_test_filters'] );
    }

    public function test_filter_not_registered_when_disabled(): void {
        $GLOBALS['wpca_test_options']['wpca_settings'] = array(
            'security' => array( 'restrict_rest_api' => 0 ),
        );

        $resources = Resources::getInstance();
        $resources->register_rest_restriction();

        $this->assertArrayNotHasKey( 'rest_authentication_errors', $GLOBALS['wpca_test_filters'] );
    }

    public function test_anonymous_request_gets_403_error(): void {
        $GLOBALS['wpca_test_logged_in'] = false;

        $resources = Resources::getInstance();
        $result   = $resources->disable_rest_api( null );

        $this->assertInstanceOf( 'WP_Error', $result );
        $this->assertSame( 'rest_not_logged_in', $result->get_error_code() );
        $this->assertSame( 403, $result->get_error_data()['status'] );
    }

    public function test_authenticated_request_passes_through(): void {
        $GLOBALS['wpca_test_logged_in'] = true;

        $resources = Resources::getInstance();
        $result   = $resources->disable_rest_api( null );

        $this->assertNull( $result );
    }

    public function test_existing_error_is_preserved(): void {
        $GLOBALS['wpca_test_logged_in'] = false;

        $existing = new WP_Error( 'some_other_error', 'Existing', array( 'status' => 500 ) );
        $resources = Resources::getInstance();
        $result    = $resources->disable_rest_api( $existing );

        $this->assertSame( $existing, $result );
    }
}
