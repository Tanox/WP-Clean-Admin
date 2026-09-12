<?php
/**
 * WPCleanAdmin Cleanup Statistics
 *
 * 抽取原 class-wpca-cleanup.php 的清理统计查询，独立为子模块。
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

class Cleanup_Stats {

    /**
     * Get cleanup statistics
     *
     * @return array Cleanup statistics
     * @global \wpdb $wpdb WordPress database object
     */
    public function get_cleanup_stats(): array {
        global $wpdb;

        $stats = array();

        // Get transients count
        $stats['transients'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%_transient%'" );

        // Get orphaned postmeta count
        $stats['orphaned_postmeta'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.ID IS NULL" );

        // Get orphaned termmeta count
        $stats['orphaned_termmeta'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->termmeta} LEFT JOIN {$wpdb->terms} ON {$wpdb->termmeta}.term_id = {$wpdb->terms}.term_id WHERE {$wpdb->terms}.term_id IS NULL" );

        // Get orphaned relationships count
        $stats['orphaned_relationships'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->term_relationships} LEFT JOIN {$wpdb->posts} ON {$wpdb->term_relationships}.object_id = {$wpdb->posts}.ID WHERE {$wpdb->posts}.ID IS NULL" );

        // Get spam comments count
        $stats['spam_comments'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'" );

        // Get trash comments count
        $stats['trash_comments'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'trash'" );

        // Get unapproved comments count
        $stats['unapproved_comments'] = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '0'" );

        // Get orphaned media count
        $stats['orphaned_media'] = $this->get_orphaned_media_count();

        return $stats;
    }

    /**
     * Get orphaned media count
     *
     * @return int Orphaned media count
     * @global \wpdb $wpdb WordPress database object
     */
    private function get_orphaned_media_count(): int {
        global $wpdb;

        $orphaned_count = $wpdb->get_var( "
            SELECT COUNT(*) 
            FROM {$wpdb->posts} p 
            LEFT JOIN {$wpdb->postmeta} pm ON pm.meta_value LIKE CONCAT('%', p.ID, '%') 
            WHERE p.post_type = 'attachment' 
            AND pm.meta_id IS NULL
        " );

        return intval( $orphaned_count );
    }
}
