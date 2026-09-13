<?php
/**
 * WPCleanAdmin Cleanup Database Tasks Trait
 *
 * 数据库清理任务（transients / orphaned meta / expired crons 等），
 * 从 class-wpca-cleanup.php 按职责抽离，公开方法契约不变。
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
 * 数据库清理任务 trait
 */
trait CleanupDatabaseTasks {

    /**
     * Get cleanup statistics
     *
     * @return array Cleanup statistics
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

    /**
     * Run database cleanup
     *
     * @param array $options Cleanup options including transients, orphaned_postmeta, orphaned_termmeta, orphaned_relationships, and expired_crons
     * @return array Cleanup results with success status, message, and cleaned item counts
     * @global $wpdb WordPress database object
     */
    public function run_database_cleanup( array $options = array() ): array {
        global $wpdb;

        $results = array(
            'success' => true,
            'message' => \__( 'Database cleanup completed successfully', WPCA_TEXT_DOMAIN ),
            'cleaned' => array()
        );

        // Set default options
        $default_options = array(
            'transients' => true,
            'orphaned_postmeta' => true,
            'orphaned_termmeta' => true,
            'orphaned_relationships' => true,
            'expired_crons' => true
        );

        $options = $this->wp_parse_args( $options, $default_options );

        // Clean transients
        if ( $options['transients'] ) {
            $deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d", '%_transient_timeout_%', time() ) );
            $deleted += $wpdb->query( $wpdb->prepare( "DELETE t1 FROM {$wpdb->options} t1 INNER JOIN {$wpdb->options} t2 ON t1.option_name = CONCAT( '_transient_', SUBSTRING( t2.option_name, 19 ) ) WHERE t2.option_name LIKE %s", '%_transient_timeout_%' ) );
            $results['cleaned']['transients'] = $deleted;
        }

        // Clean orphaned postmeta
        if ( $options['orphaned_postmeta'] ) {
            $deleted = $wpdb->query( $wpdb->prepare(
                "DELETE pm FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.ID IS NULL"
            ) );
            $results['cleaned']['orphaned_postmeta'] = $deleted;
        }

        // Clean orphaned termmeta
        if ( $options['orphaned_termmeta'] ) {
            $deleted = $wpdb->query( $wpdb->prepare(
                "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->terms} t ON tm.term_id = t.term_id WHERE t.term_id IS NULL"
            ) );
            $results['cleaned']['orphaned_termmeta'] = $deleted;
        }

        // Clean orphaned relationships
        if ( $options['orphaned_relationships'] ) {
            $deleted = $wpdb->query( $wpdb->prepare(
                "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID WHERE p.ID IS NULL"
            ) );
            $results['cleaned']['orphaned_relationships'] = $deleted;
        }

        // Clean expired crons
        if ( $options['expired_crons'] ) {
            $crons = $this->_get_cron_array();
            $now = time();
            $deleted = 0;

            foreach ( $crons as $timestamp => $cronhooks ) {
                if ( $timestamp < $now ) {
                    foreach ( $cronhooks as $hook => $events ) {
                        foreach ( $events as $sig => $data ) {
                            $this->wp_unschedule_event( $timestamp, $hook, $data['args'] );
                            $deleted++;
                        }
                    }
                }
            }

            $results['cleaned']['expired_crons'] = $deleted;
        }

        return $results;
    }
}
