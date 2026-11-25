<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 错误处理器唯一化回归测试（OPT-004）
 *
 * @package WPCleanAdmin
 * @version 1.9.17
 */

use PHPUnit\Framework\TestCase;
use WPCleanAdmin\Modules\Utilities\Classes\Helpers;

class ErrorHandlerTest extends TestCase {

    /**
     * @var Helpers
     */
    private $helpers;

    protected function setUp(): void {
        $this->helpers = Helpers::getInstance();
    }

    public function test_custom_error_handler_does_not_swallow_errors(): void {
        $result = $this->helpers->custom_error_handler( E_USER_WARNING, 'test warning', __FILE__, __LINE__ );

        $this->assertFalse( $result );
    }

    public function test_register_error_handler_is_deprecated_noop(): void {
        $before = set_error_handler( null );

        $this->helpers->register_error_handler();

        $after = set_error_handler( null );
        if ( $before !== null ) {
            restore_error_handler();
        }
        if ( $after !== null ) {
            restore_error_handler();
        }

        $this->assertSame( $before, $after );
    }
}
