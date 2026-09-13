<?php
/**
 * Database Backup List Tasks
 *
 * Lists and deletes database backup files.
 *
 * @package WPCleanAdmin
 * @version 1.8.18
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Backup-listing tasks for the Database class.
 */
trait DatabaseBackupListTasks {

    /**
     * Get database backups list
     *
     * @return array Database backups
     */
    public function get_database_backups(): array {
        $backups = array();

        // Get backup directory
        $backup_dir = WPCA_PLUGIN_DIR . 'backups/';

        // Check if backup directory exists
        if ( ! file_exists( $backup_dir ) ) {
            return $backups;
        }

        // Get backup files
        $files = glob( $backup_dir . '*.sql' );

        foreach ( $files as $file ) {
            $backups[] = array(
                'name' => basename( $file ),
                'path' => $file,
                'size' => filesize( $file ),
                'modified' => filemtime( $file )
            );
        }

        // Sort backups by modified date (newest first)
        usort( $backups, function( $a, $b ) {
            return $b['modified'] - $a['modified'];
        } );

        return $backups;
    }

    /**
     * Delete database backup
     *
     * @param string $backup_file Backup file name
     * @return array Delete results
     */
    public function delete_database_backup( $backup_file ) {
        $results = array(
            'success' => false,
            'message' => \__( 'Failed to delete backup file', WPCA_TEXT_DOMAIN )
        );

        // Get full backup file path
        $backup_path = WPCA_PLUGIN_DIR . 'backups/' . $backup_file;

        // Check if backup file exists
        if ( file_exists( $backup_path ) ) {
            // Delete backup file
            if ( unlink( $backup_path ) ) {
                $results['success'] = true;
                $results['message'] = \__( 'Backup file deleted successfully', WPCA_TEXT_DOMAIN );
            }
        }

        return $results;
    }
}
