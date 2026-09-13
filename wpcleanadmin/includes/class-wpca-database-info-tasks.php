<?php
/**
 * Database Info Tasks
 *
 * Database information retrieval and table optimization.
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
 * Information and optimization tasks for the Database class.
 */
trait DatabaseInfoTasks {

    /**
     * Get database information
     *
     * @return array Database information
     */
    public function get_database_info(): array {
        global $wpdb;

        $info = array();

        // Get database name and version
        $info['name'] = $wpdb->dbname;
        $info['version'] = $wpdb->db_version();

        // Get table count
        $info['table_count'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = %s", $wpdb->dbname ) );

        // Get database size
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = %s", $wpdb->dbname ), ARRAY_A );
        $info['size'] = ( function_exists( 'size_format' ) ? \size_format( $result['size'], 2 ) : round( $result['size'] / 1024 / 1024, 2 ) . ' MB' );

        // Get WordPress tables
        $info['wp_tables'] = array();
        $tables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . '%' ), ARRAY_N );

        foreach ( $tables as $table ) {
            $table_name = $table[0];
            $table_info = $wpdb->get_row( $wpdb->prepare( "SELECT data_length, index_length FROM information_schema.TABLES WHERE table_schema = %s AND table_name = %s", $wpdb->dbname, $table_name ), ARRAY_A );

            $total_size = $table_info['data_length'] + $table_info['index_length'];
            $data_size = $table_info['data_length'];
            $index_size = $table_info['index_length'];

            $info['wp_tables'][] = array(
                'name' => $table_name,
                'size' => ( function_exists( 'size_format' ) ? \size_format( $total_size, 2 ) : round( $total_size / 1024 / 1024, 2 ) . ' MB' ),
                'data_size' => ( function_exists( 'size_format' ) ? \size_format( $data_size, 2 ) : round( $data_size / 1024 / 1024, 2 ) . ' MB' ),
                'index_size' => ( function_exists( 'size_format' ) ? \size_format( $index_size, 2 ) : round( $index_size / 1024 / 1024, 2 ) . ' MB' )
            );
        }

        return $info;
    }

    /**
     * Optimize database tables
     *
     * @uses $wpdb->prepare() To safely prepare SQL queries
     * @uses $wpdb->get_results() To retrieve table list
     * @uses $wpdb->query() To execute optimization query
     * @uses \__() To translate strings
     * @return array Optimization results with success status, message, and table details
     */
    public function optimize_database() {
        global $wpdb;

        $results = array(
            'success' => true,
            'message' => \__( 'Database optimization completed successfully', WPCA_TEXT_DOMAIN ),
            'tables' => array()
        );

        // Get all WordPress tables
        $tables = $wpdb->get_results( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->prefix . '%' ), ARRAY_N );

        foreach ( $tables as $table ) {
            $table_name = $table[0];
            $result = $wpdb->query( $wpdb->prepare( "OPTIMIZE TABLE %s", $table_name ) );

            $results['tables'][] = array(
                'name' => $table_name,
                'optimized' => $result !== false
            );
        }

        return $results;
    }
}
