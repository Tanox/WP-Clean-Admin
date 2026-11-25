<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 统一 AJAX 响应契约测试（OPT-002）
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 */

use PHPUnit\Framework\TestCase;
use WPCleanAdmin\AJAX\Response;
use WPCleanAdmin\Modules\Utilities\Classes\WPCA_Errors;

class ResponseTest extends TestCase {

    protected function setUp(): void {
        $GLOBALS['wpca_test_json'] = null;
    }

    public function test_success_payload(): void {
        Response::success( array( 'settings' => array( 'enabled' => true ) ), 'Saved' );

        $this->assertSame( true, $GLOBALS['wpca_test_json']['success'] );
        $this->assertSame( 'Saved', $GLOBALS['wpca_test_json']['message'] );
        $this->assertSame( array( 'enabled' => true ), $GLOBALS['wpca_test_json']['data']['settings'] );
        $this->assertArrayNotHasKey( 'error_code', $GLOBALS['wpca_test_json'] );
    }

    public function test_error_payload_with_error_code(): void {
        Response::error( WPCA_Errors::ERROR_PERMISSION, 'Denied' );

        $this->assertSame( false, $GLOBALS['wpca_test_json']['success'] );
        $this->assertSame( 1002, $GLOBALS['wpca_test_json']['error_code'] );
        $this->assertSame( 'Denied', $GLOBALS['wpca_test_json']['message'] );
        $this->assertSame( array(), $GLOBALS['wpca_test_json']['data'] );
    }
}
