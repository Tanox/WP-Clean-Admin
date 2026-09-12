<?php
/**
 * WPCleanAdmin Media Cleanup Task
 *
 * 媒体清理任务（orphaned / unused / duplicate media），
 * 从原 class-wpca-cleanup.php 抽离，复用 Cleanup_Helpers trait。
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

class Cleanup_Media_Task {

    use Cleanup_Helpers;

    /**
     * Run media cleanup
     *
     * @param array $options Cleanup options
     * @return array Cleanup results with success status, message, and cleaned item counts
     */
    public function run_media_cleanup( array $options = array() ): array {
        global $wpdb;

        $results = array(
            'success' => true,
            'message' => \__( 'Media cleanup completed successfully', WPCA_TEXT_DOMAIN ),
            'cleaned' => array()
        );

        $default_options = array(
            'orphaned_media' => true,
            'unused_media' => true,
            'duplicate_media' => false,
            'media_age_days' => 0
        );

        $options = $this->wp_parse_args( $options, $default_options );

        if ( $options['orphaned_media'] ) {
            $orphaned_media = $this->get_orphaned_media();

            $deleted = 0;
            foreach ( $orphaned_media as $media ) {
                $this->wp_delete_attachment( $media->ID, true );
                $deleted++;
            }

            $results['cleaned']['orphaned_media'] = $deleted;
        }

        if ( $options['unused_media'] ) {
            $unused_media = $this->get_unused_media( $options['media_age_days'] );

            $deleted = 0;
            foreach ( $unused_media as $media ) {
                $this->wp_delete_attachment( $media->ID, true );
                $deleted++;
            }

            $results['cleaned']['unused_media'] = $deleted;
        }

        if ( $options['duplicate_media'] ) {
            $duplicates = $this->get_duplicate_media();

            $deleted = 0;
            foreach ( $duplicates as $media ) {
                $this->wp_delete_attachment( $media->ID, true );
                $deleted++;
            }

            $results['cleaned']['duplicate_media'] = $deleted;
        }

        return $results;
    }

    /**
     * Get orphaned media files
     *
     * @return array Array of orphaned media post objects
     * @global \wpdb $wpdb WordPress database object
     */
    private function get_orphaned_media(): array {
        global $wpdb;

        $orphaned_media = $wpdb->get_results( "
            SELECT p.ID, p.guid, p.post_title, p.post_date
            FROM {$wpdb->posts} p 
            LEFT JOIN {$wpdb->postmeta} pm ON pm.meta_value LIKE CONCAT('%', p.ID, '%') 
            WHERE p.post_type = 'attachment' 
            AND pm.meta_id IS NULL
            GROUP BY p.ID
        " );

        return $orphaned_media ? $orphaned_media : array();
    }

    /**
     * Get unused media files
     *
     * @param int $age_days Only include media older than X days (0 = all)
     * @return array Array of unused media post objects
     * @global \wpdb $wpdb WordPress database object
     */
    private function get_unused_media( int $age_days = 0 ): array {
        global $wpdb;

        $age_filter = '';
        if ( $age_days > 0 ) {
            $cutoff_date = date( 'Y-m-d H:i:s', strtotime( "-{$age_days} days" ) );
            $age_filter = $wpdb->prepare( " AND p.post_date < %s", $cutoff_date );
        }

        $unused_media = $wpdb->get_results( "
            SELECT DISTINCT p.ID, p.guid, p.post_title, p.post_date
            FROM {$wpdb->posts} p 
            WHERE p.post_type = 'attachment'
            AND p.ID NOT IN (
                SELECT DISTINCT pm.post_id 
                FROM {$wpdb->postmeta} pm 
                WHERE pm.meta_key = '_thumbnail_id'
            )
            AND p.ID NOT IN (
                SELECT DISTINCT CAST(pm.meta_value AS UNSIGNED) 
                FROM {$wpdb->postmeta} pm 
                WHERE pm.meta_value LIKE CONCAT('%', p.ID, '%')
                AND pm.meta_key IN ( '_wp_attachment_metadata', '_elementor_data' )
            )
            AND p.post_status = 'inherit'
            {$age_filter}
        " );

        return $unused_media ? $unused_media : array();
    }

    /**
     * Get duplicate media files
     *
     * @return array Array of duplicate media post objects
     * @global \wpdb $wpdb WordPress database object
     */
    private function get_duplicate_media(): array {
        global $wpdb;

        $duplicates = $wpdb->get_results( "
            SELECT p1.ID, p1.guid, p1.post_title, p1.post_name
            FROM {$wpdb->posts} p1
            INNER JOIN (
                SELECT post_name, COUNT(*) as cnt
                FROM {$wpdb->posts}
                WHERE post_type = 'attachment'
                AND post_status = 'inherit'
                GROUP BY post_name
                HAVING COUNT(*) > 1
            ) p2 ON p1.post_name = p2.post_name
            WHERE p1.post_type = 'attachment'
            AND p1.post_status = 'inherit'
            ORDER BY p1.post_date ASC
        " );

        return $duplicates ? $duplicates : array();
    }
}
