<?php
/**
 * WPCleanAdmin Cleanup Helpers Trait
 *
 * 抽取原 class-wpca-cleanup.php 中的 WordPress 函数包装器与通用辅助方法，
 * 供各 Cleanup Task 子模块复用，避免重复代码。
 *
 * @package WPCleanAdmin
 * @version 1.8.5
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.5
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Cleanup_Helpers {

    /**
     * Wrapper for wp_parse_args function
     *
     * @param array|string $args Arguments to parse
     * @param array $defaults Default values
     * @return array Parsed arguments
     */
    protected function wp_parse_args( $args, $defaults ) {
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
    protected function _get_cron_array() {
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
    protected function wp_unschedule_event( $timestamp, $hook, $args = array() ) {
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
    protected function wp_delete_attachment( $post_id, $force_delete = false ) {
        if ( function_exists( 'wp_delete_attachment' ) ) {
            return \wp_delete_attachment( $post_id, $force_delete );
        }
        return false;
    }

    /**
     * Delete comments with specific status
     *
     * @param string $status Comment status (spam, unapproved, trash)
     * @return int Number of comments deleted
     * @global \wpdb $wpdb WordPress database object
     */
    protected function wp_delete_comments_with_status( string $status ): int {
        global $wpdb;

        $comment_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT comment_ID FROM {$wpdb->comments} WHERE comment_approved = %s",
            $status
        ) );

        $deleted = 0;
        foreach ( $comment_ids as $comment_id ) {
            \wp_delete_comment( $comment_id, true );
            $deleted++;
        }

        return $deleted;
    }

    /**
     * Delete old comments
     *
     * @param int $days_old Number of days to consider comment as old
     * @return int Number of comments deleted
     * @global \wpdb $wpdb WordPress database object
     */
    protected function wp_delete_old_comments( int $days_old ): int {
        global $wpdb;

        $cutoff_date = date( 'Y-m-d H:i:s', strtotime( "-{$days_old} days" ) );

        $comment_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT comment_ID FROM {$wpdb->comments} WHERE comment_date < %s AND comment_approved = '1'",
            $cutoff_date
        ) );

        $deleted = 0;
        foreach ( $comment_ids as $comment_id ) {
            \wp_delete_comment( $comment_id, true );
            $deleted++;
        }

        return $deleted;
    }
}
