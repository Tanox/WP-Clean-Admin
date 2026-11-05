<?php
/**
 * WPCleanAdmin Cleanup Content Posts Tasks
 *
 * 内容清理（空文章 / 重复文章 DB 清理），从 CleanupContentTasks 抽离。
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
 * 内容清理（文章）任务（trait）。
 */
trait CleanupContentPostsTasks {

    /**
     * Run content cleanup
     *
     * @param array $options Cleanup options
     * @return array Cleanup results
     */
    public function run_content_cleanup( array $options = array() ): array {
        global $wpdb;

        $results = array(
            'success' => true,
            'message' => \__( 'Content cleanup completed successfully', WPCA_TEXT_DOMAIN ),
            'cleaned' => array()
        );

        // Set default options
        $default_options = array(
            'unused_shortcodes' => true,
            'empty_posts' => true,
            'duplicate_posts' => false
        );

        $options = $this->wp_parse_args( $options, $default_options );

        // Clean unused shortcodes
        if ( $options['unused_shortcodes'] ) {
            $results['cleaned']['unused_shortcodes'] = $this->cleanup_unused_shortcodes();
        }

        // Clean empty posts
        if ( $options['empty_posts'] ) {
            $deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE post_content = %s AND post_type = %s AND post_status = %s", '', 'post', 'publish' ) );
            $results['cleaned']['empty_posts'] = $deleted;
        }

        return $results;
    }

    /**
     * Cleanup empty posts
     *
     * Provides functionality to clean empty posts according to the OpenSpec
     * admin-cleanup specification.
     *
     * @param array $options Cleanup options including:
     *                       - post_types: Array of post types to clean (default: all)
     *                       - post_statuses: Array of post statuses to clean (default: draft, publish)
     *                       - age_days: Only clean posts older than X days (default: 0 = all)
     * @return array Cleanup result with cleaned_count, posts_type, and message
     * @global $wpdb WordPress database object
     */
    public function cleanup_empty_posts( $options = array() ) {
        global $wpdb;

        $default_options = array(
            'post_types' => array( 'post', 'page' ),
            'post_statuses' => array( 'draft', 'publish' ),
            'age_days' => 0
        );

        $options = $this->wp_parse_args( $options, $default_options );

        // Build post type filter
        $post_types_placeholders = implode( ', ', array_fill( 0, count( $options['post_types'] ), '%s' ) );
        $post_statuses_placeholders = implode( ', ', array_fill( 0, count( $options['post_statuses'] ), '%s' ) );

        $query = $wpdb->prepare( "
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type IN ({$post_types_placeholders})
            AND post_status IN ({$post_statuses_placeholders})
            AND (
                post_content = ''
                OR post_content LIKE '% %'
            )
        ", array_merge( $options['post_types'], $options['post_statuses'] ) );

        // Add age filter
        if ( $options['age_days'] > 0 ) {
            $cutoff_date = date( 'Y-m-d H:i:s', strtotime( "-{$options['age_days']} days" ) );
            $query .= $wpdb->prepare( " AND post_date < %s", $cutoff_date );
        }

        $count = $wpdb->get_var( $query );

        return array(
            'cleaned_count' => (int) $count,
            'posts_type' => 'empty_posts',
            'message' => sprintf(
                \__( 'Found %d empty posts to clean', WPCA_TEXT_DOMAIN ),
                $count
            ),
        );
    }

    /**
     * Cleanup duplicate posts
     *
     * Provides functionality to clean duplicate posts according to the OpenSpec
     * admin-cleanup specification.
     *
     * @param array $options Cleanup options including:
     *                       - post_types: Array of post types to check (default: post, page)
     *                       - delete_method: 'keep_oldest' or 'keep_newest' (default: keep_oldest)
     *                       - compare_fields: Fields to compare for duplicates (default: title, content)
     * @return array Cleanup result with cleaned_count, duplicates_found, and message
     * @global $wpdb WordPress database object
     */
    public function cleanup_duplicate_posts( $options = array() ) {
        global $wpdb;

        $default_options = array(
            'post_types' => array( 'post', 'page' ),
            'delete_method' => 'keep_oldest',
            'compare_fields' => array( 'title', 'content' )
        );

        $options = $this->wp_parse_args( $options, $default_options );

        // Build post type filter
        $post_types_placeholders = implode( ', ', array_fill( 0, count( $options['post_types'] ), '%s' ) );

        // Find duplicates based on post_title and post_content
        $duplicates = $wpdb->get_results( $wpdb->prepare( "
            SELECT p1.ID, p1.post_title, p1.post_date
            FROM {$wpdb->posts} p1
            INNER JOIN {$wpdb->posts} p2
                ON p1.post_title = p2.post_title
                AND p1.post_content = p2.post_content
                AND p1.ID != p2.ID
            WHERE p1.post_type IN ({$post_types_placeholders})
            AND p1.post_status IN ('publish', 'draft', 'pending')
            ORDER BY p1.post_date ASC
        ", $options['post_types'] ) );

        if ( empty( $duplicates ) ) {
            return array(
                'cleaned_count' => 0,
                'duplicates_found' => 0,
                'type' => 'duplicate_posts',
                'message' => \__( 'No duplicate posts found', WPCA_TEXT_DOMAIN ),
            );
        }

        // Group duplicates by content
        $duplicate_groups = array();
        foreach ( $duplicates as $post ) {
            $key = md5( $post->post_title . $post->post_date );
            if ( ! isset( $duplicate_groups[ $key ] ) ) {
                $duplicate_groups[ $key ] = array();
            }
            $duplicate_groups[ $key ][] = $post;
        }

        // Delete duplicates keeping the first (oldest or newest based on option)
        $deleted_count = 0;
        foreach ( $duplicate_groups as $group ) {
            if ( count( $group ) > 1 ) {
                // Skip first, delete rest
                $start_index = ( $options['delete_method'] === 'keep_newest' ) ? 0 : 1;
                $end_index = count( $group );

                for ( $i = $start_index; $i < $end_index; $i++ ) {
                    \wp_delete_post( $group[ $i ]->ID, true );
                    $deleted_count++;
                }
            }
        }

        return array(
            'cleaned_count' => $deleted_count,
            'duplicates_found' => count( $duplicates ),
            'type' => 'duplicate_posts',
            'message' => sprintf(
                \__( 'Cleaned %d duplicate posts from %d found', WPCA_TEXT_DOMAIN ),
                $deleted_count,
                count( $duplicates )
            ),
        );
    }
}
