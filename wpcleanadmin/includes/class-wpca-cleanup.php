<?php
/**
 * WPCleanAdmin Cleanup Class
 *
 * 清理门面类：保留单例与 WordPress 函数 wrapper，具体清理任务按职责
 * 拆入 Cleanup_*_Tasks trait（数据库/媒体/评论/内容），公开方法契约不变。
 *
 * @package WPCleanAdmin
 * @version 1.8.10
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cleanup class
 */
class Cleanup {

    use CleanupDatabaseTasks;
    use CleanupMediaTasks;
    use CleanupCommentsTasks;
    use CleanupContentTasks;

    /**
     * Singleton instance
     *
     * @var Cleanup
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Cleanup
     */
    public static function getInstance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize the cleanup module
     */
    public function init(): void {
        if ( function_exists( 'add_action' ) ) {
            \add_action( 'wpca_cleanup_database', array( $this, 'run_database_cleanup' ) );
            \add_action( 'wpca_cleanup_media', array( $this, 'run_media_cleanup' ) );
            \add_action( 'wpca_cleanup_comments', array( $this, 'run_comments_cleanup' ) );
            \add_action( 'wpca_cleanup_content', array( $this, 'run_content_cleanup' ) );
        }
    }

    /**
     * Wrapper for wp_parse_args function
     *
     * @param array|string $args Arguments to parse
     * @param array $defaults Default values
     * @return array Parsed arguments
     */
    private function wp_parse_args( $args, $defaults ) {
        if ( function_exists( '\wp_parse_args' ) ) {
            return \wp_parse_args( $args, $defaults );
        }
        return array_merge( $defaults, (array) $args );
    }

    /**
     * Wrapper for _get_cron_array function
     *
     * @return array Cron events array
     */
    private function _get_cron_array() {
        if ( function_exists( '_get_cron_array' ) ) {
            return \_get_cron_array();
        }
        return array();
    }

    /**
     * Wrapper for wp_unschedule_event function
     *
     * @param int $timestamp Timestamp
     * @param string $hook Hook name
     * @param array $args Hook arguments
     */
    private function wp_unschedule_event( $timestamp, $hook, $args = array() ) {
        if ( function_exists( 'wp_unschedule_event' ) ) {
            \wp_unschedule_event( $timestamp, $hook, $args );
        }
    }

    /**
     * Wrapper for wp_delete_attachment function
     *
     * @param int $post_id Post ID
     * @param bool $force_delete Force delete
     * @return mixed Deleted post or false
     */
    private function wp_delete_attachment( $post_id, $force_delete = false ) {
        if ( function_exists( 'wp_delete_attachment' ) ) {
            return \wp_delete_attachment( $post_id, $force_delete );
        }
        return false;
    }
}
