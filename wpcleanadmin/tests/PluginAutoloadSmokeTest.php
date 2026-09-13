<?php
/**
 * Autoload smoke tests
 *
 * 验证根命名空间（legacy）与 modules 子命名空间（modular）的关键类均可被
 * autoloader 正确加载。历史上 modules/** 曾误写根命名空间，与根版同名类
 * 产生 FQCN 冲突（Cannot declare class fatal），本测试用于回归防护。
 *
 * @package WPCleanAdmin
 * @version 1.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PHPUnit\Framework\TestCase;

class PluginAutoloadSmokeTest extends TestCase {

	/**
	 * 提供应由 autoloader 加载成功的类清单。
	 *
	 * @return array
	 */
	public function classProvider(): array {
		return array(
			// 根命名空间（legacy 类体系）。
			array( 'WPCleanAdmin\\Core' ),
			array( 'WPCleanAdmin\\Helpers' ),
			array( 'WPCleanAdmin\\Cache' ),
			array( 'WPCleanAdmin\\Dashboard' ),
			array( 'WPCleanAdmin\\Permissions' ),
			array( 'WPCleanAdmin\\Resources' ),
			array( 'WPCleanAdmin\\Reset' ),
			array( 'WPCleanAdmin\\Menu_Manager' ),
			array( 'WPCleanAdmin\\Menu_Customizer' ),
			array( 'WPCleanAdmin\\User_Roles' ),
			array( 'WPCleanAdmin\\Login' ),
			array( 'WPCleanAdmin\\Error_Handler' ),
			array( 'WPCleanAdmin\\AJAX' ),
			// modules 子命名空间（modular 类体系）。
			array( 'WPCleanAdmin\\Modules\\Core\\Classes\\Core' ),
			array( 'WPCleanAdmin\\Modules\\Core\\Classes\\Module_Loader' ),
			array( 'WPCleanAdmin\\Modules\\Core\\Classes\\Error_Handler' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\Dashboard' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\Login' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\Menu_Customizer' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\Menu_Manager' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\Permissions' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Classes\\User_Roles' ),
			array( 'WPCleanAdmin\\Modules\\Admin\\Settings\\Settings' ),
			array( 'WPCleanAdmin\\Modules\\Utilities\\Classes\\Helpers' ),
			array( 'WPCleanAdmin\\Modules\\Utilities\\Classes\\Cache' ),
			array( 'WPCleanAdmin\\Modules\\Utilities\\Classes\\Resources' ),
			array( 'WPCleanAdmin\\Modules\\Utilities\\Classes\\I18n' ),
		);
	}

	/**
	 * 断言类可被 autoloader 加载且声明成功。
	 *
	 * @dataProvider classProvider
	 * @param string $fqcn 完整类名。
	 */
	public function test_class_is_autoloadable( string $fqcn ): void {
		$this->assertTrue(
			class_exists( $fqcn, true ),
			sprintf( 'Autoloader failed to load class: %s', $fqcn )
		);
	}
}
