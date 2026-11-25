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
        // 哨兵处理器：登记前后生效的处理器应保持为哨兵，
        // 证明 register_error_handler() 没有再注册新的全局错误处理器。
        $sentinel = static function () {
            return false;
        };

        set_error_handler( $sentinel );

        $this->helpers->register_error_handler();

        // 取出生效中的处理器并复位（set_error_handler(null) 会把当前处理器压栈）
        $current = set_error_handler( null );
        restore_error_handler(); // 还原哨兵
        restore_error_handler(); // 还原 PHPUnit 的错误处理器

        $this->assertSame(
            $sentinel,
            $current,
            'register_error_handler() must not install a global error handler'
        );
    }
}
