<?php
/**
 * WPCleanAdmin Database Cleanup Task
 *
 * 数据库清理任务（transients / orphaned meta / expired crons），
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

class Cleanup_Database_Task {

    use Cleanup_Helpers;

    /**
     * Run database cleanup
     *
     * @param array $options Cleanup options
     * @return array Cleanup results with success status, message, and cleaned item counts
     * @global \wpdb $wpdb WordPress database object
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
