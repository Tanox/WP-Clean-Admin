<?php
/**
 * WPCleanAdmin Extension API Hook Tasks Trait
 *
 * 扩展 hooks / filters 注册与执行，从 class-wpca-extension-api.php 按职责抽离，公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.10
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 扩展钩子任务 trait
 */
trait ExtensionHookTasks {

    /**
     * Add extension hook
     *
     * @param string $hook Hook name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @param int $accepted_args Number of accepted arguments
     * @return bool Success status
     */
    public function add_hook( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        if ( ! is_callable( $callback ) ) {
            return false;
        }

        if ( ! isset( $this->hooks[ $hook ] ) ) {
            $this->hooks[ $hook ] = array();
        }

        $this->hooks[ $hook ][] = array(
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        );

        return true;
    }

    /**
     * Add extension filter
     *
     * @param string $filter Filter name
     * @param callable $callback Callback function
     * @param int $priority Hook priority
     * @param int $accepted_args Number of accepted arguments
     * @return bool Success status
     */
    public function add_filter( $filter, $callback, $priority = 10, $accepted_args = 1 ) {
        if ( ! is_callable( $callback ) ) {
            return false;
        }

        if ( ! isset( $this->filters[ $filter ] ) ) {
            $this->filters[ $filter ] = array();
        }

        $this->filters[ $filter ][] = array(
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        );

        return true;
    }

    /**
     * Execute hooks
     *
     * @param string $hook Hook name
     * @param mixed $arg Optional argument
     * @return mixed Result
     */
    public function execute_hook( $hook, $arg = '' ) {
        if ( ! isset( $this->hooks[ $hook ] ) ) {
            return $arg;
        }

        $args = func_get_args();
        array_shift( $args );

        foreach ( $this->hooks[ $hook ] as $hook_config ) {
            $result = call_user_func_array(
                $hook_config['callback'],
                array_slice( $args, 0, $hook_config['accepted_args'] )
            );
            if ( $result !== null ) {
                $arg = $result;
            }
        }

        return $arg;
    }

    /**
     * Apply filters
     *
     * @param string $filter Filter name
     * @param mixed $value Value to filter
     * @return mixed Filtered value
     */
    public function apply_filter( $filter, $value ) {
        if ( ! isset( $this->filters[ $filter ] ) ) {
            return $value;
        }

        foreach ( $this->filters[ $filter ] as $filter_config ) {
            $value = call_user_func_array(
                $filter_config['callback'],
                array( $value )
            );
        }

        return $value;
    }
}
