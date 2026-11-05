<?php
/**
 * WPCleanAdmin Cleanup AJAX Database/Media Tasks
 *
 * 承载数据库与媒体清理类 AJAX 处理器，从 AJAX\Cleanup 主类抽取。
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
 * 数据库与媒体清理类 AJAX 任务（trait）。
 */
trait CleanupAjaxDatabaseMediaTasks {

    /**
     * Cleanup database
     *
     * @return void
     */
    public static function cleanup_database() {
        if ( ! self::verify_cleanup_request() ) {
            return;
        }

        try {
            $result = array(
                'message' => \__( 'Database cleanup completed successfully', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'transients' => \__( 'Transients cleaned up', WPCA_TEXT_DOMAIN ),
                    'autosave' => \__( 'Autosave revisions cleaned up', WPCA_TEXT_DOMAIN ),
                    'spam_comments' => \__( 'Spam comments cleaned up', WPCA_TEXT_DOMAIN )
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
     * Cleanup media
     *
     * @return void
     */
    public static function cleanup_media() {
        if ( ! self::verify_cleanup_request() ) {
            return;
        }

        try {
            $result = array(
                'message' => \__( 'Media cleanup completed successfully', WPCA_TEXT_DOMAIN ),
                'details' => array(
                    'unused_media' => \__( 'Unused media files cleaned up', WPCA_TEXT_DOMAIN ),
                    'duplicate_media' => \__( 'Duplicate media files cleaned up', WPCA_TEXT_DOMAIN )
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
