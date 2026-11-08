<?php
/**
 * WPCleanAdmin Login Style Tasks
 *
 * 登录页样式相关委托方法（转发至 `Login_Style`），公开 API 不变。
 * 主类 `Login` 通过 `use LoginStyleTasks;` 组合。
 *
 * @package WPCleanAdmin
 * @version 1.9.16
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */

namespace WPCleanAdmin\Modules\Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Login style tasks
 */
trait LoginStyleTasks {

    /**
     * Enqueue login scripts and styles (delegated)
     */
    public function enqueue_login_scripts() {
        $this->style->enqueue_login_scripts();
    }

    /**
     * Filter login header URL (delegated)
     *
     * @param string $url
     * @return string
     */
    public function filter_login_header_url( $url ) {
        return $this->style->filter_login_header_url( $url );
    }

    /**
     * Filter login header title (delegated)
     *
     * @param string $title
     * @return string
     */
    public function filter_login_header_title( string $title ): string {
        return $this->style->filter_login_header_title( $title );
    }

    /**
     * Add login footer content (delegated)
     */
    public function add_login_footer_content() {
        $this->style->add_login_footer_content();
    }

    /**
     * Filter login body class (delegated)
     *
     * @param array $classes
     * @return array
     */
    public function filter_login_body_class( $classes ) {
        return $this->style->filter_login_body_class( $classes );
    }

    /**
     * Customize login page (delegated)
     */
    public function customize_login_page(): void {
        $this->style->customize_login_page();
    }

    /**
     * Add custom login logo (delegated)
     */
    public function add_custom_login_logo(): void {
        $this->style->add_custom_login_logo();
    }

    /**
     * Add custom login background (delegated)
     */
    public function add_custom_login_background() {
        $this->style->add_custom_login_background();
    }
}
