<?php
/**
 * Database Restore Tasks
 *
 * Restores the database from a SQL backup file with query-safety validation.
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
 * Restore tasks for the Database class.
 */
trait DatabaseRestoreTasks {

    /**
     * Check if a SQL query is safe to execute
     *
     * Only allows SELECT, INSERT, UPDATE with WHERE, DELETE with WHERE,
     * OPTIMIZE TABLE, and REPAIR TABLE queries.
     *
     * @param string $query The SQL query to check
     * @return bool True if the query is safe, false otherwise
     */
    private function is_safe_sql_query( string $query ): bool {
        $query = trim( strtoupper( $query ) );

        // Define allowed query patterns (lowercase for matching)
        $allowed_patterns = array(
            '/^SELECT/',
            '/^INSERT\s+INTO/',
            '/^UPDATE.*WHERE/',
            '/^DELETE\s+FROM.*WHERE/',
            '/^OPTIMIZE\s+TABLE/',
            '/^REPAIR\s+TABLE/',
        );

        foreach ( $allowed_patterns as $pattern ) {
            if ( preg_match( $pattern, $query ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Restore database from backup
     *
     * @param string $backup_file Backup file name
     * @return array Restore results
     */
    public function restore_database( string $backup_file ): array {
        global $wpdb;

        $results = array(
            'success' => false,
            'message' => \__( 'Database restore failed', WPCA_TEXT_DOMAIN )
        );

        // Validate backup file name to prevent path traversal
        $safe_filename = preg_replace( '/[^a-zA-Z0-9_\-\.]/', '', $backup_file );
        if ( $safe_filename !== $backup_file ) {
            $results['message'] = \__( 'Invalid backup file name', WPCA_TEXT_DOMAIN );
            return $results;
        }

        // Get full backup file path
        $backup_path = WPCA_PLUGIN_DIR . 'backups/' . $backup_file;

        // Check if backup file exists
        if ( ! file_exists( $backup_path ) ) {
            $results['message'] = \__( 'Backup file not found', WPCA_TEXT_DOMAIN );
            return $results;
        }

        // Verify backup file is within expected directory
        $real_backup_path = realpath( $backup_path );
        $real_backups_dir = realpath( WPCA_PLUGIN_DIR . 'backups/' );
        if ( strpos( $real_backup_path, $real_backups_dir ) !== 0 ) {
            $results['message'] = \__( 'Invalid backup file path', WPCA_TEXT_DOMAIN );
            return $results;
        }

        // Read backup file content
        $backup_content = file_get_contents( $backup_path );

        if ( $backup_content === false ) {
            $results['message'] = \__( 'Failed to read backup file', WPCA_TEXT_DOMAIN );
            return $results;
        }

        // Execute SQL queries with safety checks
        $queries = explode( ';', $backup_content );
        $success = true;
        $error_message = '';

        foreach ( $queries as $query ) {
            $query = trim( $query );
            if ( ! empty( $query ) ) {
                // Validate query safety before execution
                if ( ! $this->is_safe_sql_query( $query ) ) {
                    $success = false;
                    $error_message = \__( 'Unsafe SQL query detected', WPCA_TEXT_DOMAIN );
                    break;
                }

                // Execute the query
                $result = $wpdb->query( $query );
                if ( $result === false ) {
                    $success = false;
                    $error_message = \__( 'Query execution failed', WPCA_TEXT_DOMAIN );
                    break;
                }
            }
        }

        if ( $success ) {
            $results['success'] = true;
            $results['message'] = \__( 'Database restore completed successfully', WPCA_TEXT_DOMAIN );
        } else {
            $results['message'] = $error_message;
        }

        return $results;
    }
}
