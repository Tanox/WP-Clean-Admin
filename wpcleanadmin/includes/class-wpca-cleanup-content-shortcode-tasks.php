<?php
/**
 * WPCleanAdmin Cleanup Content Shortcode Tasks
 *
 * 短代码清理工具（未使用 / 孤立短代码），从 CleanupContentTasks 抽离。
 *
 * @package WPCleanAdmin
 * @version 1.9.11
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.9.11
 */

namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 内容清理（短代码）任务（trait）。
 */
trait CleanupContentShortcodeTasks {

    /**
     * Clean unused shortcodes from posts
     *
     * This method finds posts containing shortcodes that are no longer registered
     * and removes the shortcode tags from the content.
     *
     * @global \wpdb $wpdb WordPress database object
     * @return int Number of posts cleaned
     */
    public function cleanup_unused_shortcodes() {
        global $wpdb;

        $cleaned_count = 0;

        // Get all registered shortcodes
        $registered_shortcodes = array();
        if ( function_exists( '\shortcode_atts' ) ) {
            global $shortcode_tags;
            if ( isset( $shortcode_tags ) && is_array( $shortcode_tags ) ) {
                $registered_shortcodes = array_keys( $shortcode_tags );
            }
        }

        if ( empty( $registered_shortcodes ) ) {
            return 0;
        }

        // Build pattern to match all registered shortcodes
        $shortcode_patterns = array();
        foreach ( $registered_shortcodes as $shortcode ) {
            $shortcode_patterns[] = '\[' . preg_quote( $shortcode, '/' ) . '(?:\s+[^=\]]+)?(?:\s*=\s*["\'][^"\']*["\'])?(?:\s*|\/)*\]';
            $shortcode_patterns[] = '\[\/' . preg_quote( $shortcode, '/' ) . '\]';
        }

        // Build pattern to match any shortcode
        $all_shortcodes_pattern = '/' . implode( '|', $shortcode_patterns ) . '/s';

        // Find posts with content that might contain shortcodes
        $posts = $wpdb->get_results(
            "SELECT ID, post_content FROM {$wpdb->posts}
             WHERE post_type IN ('post', 'page')
             AND post_status IN ('publish', 'draft', 'pending', 'private')
             AND post_content LIKE '%[%'"
        );

        foreach ( $posts as $post ) {
            $original_content = $post->post_content;
            $cleaned_content = preg_replace( $all_shortcodes_pattern, '', $original_content );

            // If content changed, update the post
            if ( $original_content !== $cleaned_content ) {
                $wpdb->update(
                    $wpdb->posts,
                    array( 'post_content' => $cleaned_content ),
                    array( 'ID' => $post->ID ),
                    array( '%s' ),
                    array( '%d' )
                );
                $cleaned_count++;
            }
        }

        return $cleaned_count;
    }

    /**
     * Remove specific shortcode from content
     *
     * @param string $content Post content
     * @param string $shortcode Shortcode name
     * @return string Content with shortcode removed
     */
    public function remove_shortcode( $content, $shortcode ) {
        $pattern = '/\[' . preg_quote( $shortcode, '/' ) . '(?:\s+[^=\]]+)?(?:\s*=\s*["\'][^"\']*["\'])?(?:\s*|\/)*\](\[\/' . preg_quote( $shortcode, '/' ) . '\])?/s';
        return preg_replace( $pattern, '', $content );
    }

    /**
     * Get registered shortcodes
     *
     * @return array Registered shortcodes
     */
    public function get_registered_shortcodes() {
        global $shortcode_tags;
        if ( isset( $shortcode_tags ) && is_array( $shortcode_tags ) ) {
            return array_keys( $shortcode_tags );
        }
        return array();
    }

    /**
     * Get orphaned shortcodes
     *
     * Finds shortcodes that are registered but have no corresponding posts using them.
     *
     * @return array Array of orphaned shortcode names
     * @global $wpdb WordPress database object
     */
    public function get_orphaned_shortcodes() {
        global $wpdb;

        // Get all registered shortcodes
        $registered_shortcodes = $this->get_registered_shortcodes();

        if ( empty( $registered_shortcodes ) ) {
            return array();
        }

        $orphaned = array();

        foreach ( $registered_shortcodes as $shortcode ) {
            // Check if shortcode is used in any published content
            $usage_count = $wpdb->get_var( $wpdb->prepare( "
                SELECT COUNT(*)
                FROM {$wpdb->posts}
                WHERE post_status IN ('publish', 'draft', 'pending')
                AND post_content LIKE %s
            ", '%[' . $shortcode . '%' ) );

            if ( $usage_count === 0 ) {
                $orphaned[] = $shortcode;
            }
        }

        return $orphaned;
    }
}
