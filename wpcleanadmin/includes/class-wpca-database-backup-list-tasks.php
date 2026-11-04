<?php
/**
 * Database Backup List Tasks
 *
 * Lists and deletes database backup files.
 *
 * @package WPCleanAdmin
 * @version 1.9.2
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

        // 仅取文件名，剔除目录成分，防御 ../ 路径遍历
        $backup_file = \basename( \sanitize_text_field( (string) $backup_file ) );
        if ( '' === $backup_file ) {
            return $results;
        }

        // 拼接并校验文件真实存在
        $backup_path = WPCA_PLUGIN_DIR . 'backups/' . $backup_file;
        if ( ! \file_exists( $backup_path ) ) {
            return $results;
        }

        // 校验真实路径必须落在备份目录内，且扩展名为 .sql
        $backup_dir = \realpath( WPCA_PLUGIN_DIR . 'backups/' );
        $real_path  = \realpath( $backup_path );
        if (
            false === $real_path
            || false === $backup_dir
            || 0 !== \strpos( $real_path, $backup_dir . \DIRECTORY_SEPARATOR )
            || '.sql' !== \strtolower( \substr( $real_path, -4 ) )
        ) {
            $results['message'] = \__( 'Invalid backup file path', WPCA_TEXT_DOMAIN );
            return $results;
        }

        // Delete backup file
        if ( \unlink( $real_path ) ) {
            $results['success'] = true;
            $results['message'] = \__( 'Backup file deleted successfully', WPCA_TEXT_DOMAIN );
        }

        return $results;
    }
}
