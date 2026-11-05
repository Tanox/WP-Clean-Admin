<?php
/**
 * WPCleanAdmin Cleanup AJAX Comments/Content Tasks
 *
 * 承载评论与内容清理类 AJAX 处理器，从 AJAX\Cleanup 主类抽取。
 *
 * @package WPCleanAdmin
 * @version 1.9.7
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 *
 * @noinspection PhpUndefinedFunctionInspection WordPress functions are available in WP environment
 */

namespace WPCleanAdmin\AJAX;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 评论与内容清理类 AJAX 任务（trait）。
 */
trait CleanupAjaxCommentsContentTasks {

    /**
     * Cleanup comments
     *
     * @return void
     */
    public static function cleanup_comments() {
        if ( ! self::verify_cleanup_request() ) {
            return;
        }

        try {
            $result = array(
                'message' => \__( 'Comments cleanup completed successfully', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'spam_comments' => \__( 'Spam comments deleted', WPCA_TEXT_DOMAIN ),
                    'trash_comments' => \__( 'Trash comments emptied', WPCA_TEXT_DOMAIN ),
                    'unapproved_comments' => \__( 'Unapproved comments cleaned up', WPCA_TEXT_DOMAIN )
                )
            );

            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( $result );
            }
        } catch ( \Exception $e ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( array( 'message' => $e->getMessage() ) );
            }
        }
    }

    /**
     * Cleanup content
     *
     * @return void
     */
    public static function cleanup_content() {
        if ( ! self::verify_cleanup_request() ) {
            return;
        }

        try {
            $result = array(
                'message' => \__( 'Content cleanup completed successfully', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'revisions' => \__( 'Post revisions cleaned up', WPCA_TEXT_DOMAIN ),
                    'draft_posts' => \__( 'Draft posts cleaned up', WPCA_TEXT_DOMAIN ),
                    'pending_posts' => \__( 'Pending posts cleaned up', WPCA_TEXT_DOMAIN )
                )
            );

            if ( function_exists( '\wp_send_json_success' ) ) {
                \wp_send_json_success( $result );
            }
        } catch ( \Exception $e ) {
            if ( function_exists( '\wp_send_json_error' ) ) {
                \wp_send_json_error( array( 'message' => $e->getMessage() ) );
            }
        }
    }
}
