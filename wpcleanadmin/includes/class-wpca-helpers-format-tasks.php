<?php
/**
 * Helpers Format Tasks
 *
 * Formatting helper methods for the Helpers class.
 *
 * @package WPCleanAdmin
 * @version 1.8.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Format-related helper tasks. Injected into the Helpers class via trait use.
 */
trait HelpersFormatTasks {

    /**
     * Format bytes to human readable size
     *
     * @param int $bytes Bytes to format
     * @param int $precision Precision of the result
     * @return string Human readable size
     */
    public function format_bytes( $bytes, $precision = 2 ) {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

        $bytes = max( $bytes, 0 );
        $pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
        $pow = min( $pow, count( $units ) - 1 );

        $bytes /= pow( 1024, $pow );

        return round( $bytes, $precision ) . ' ' . $units[ $pow ];
    }

    /**
     * Format seconds to human readable time
     *
     * @param int $seconds Seconds to format
     * @return string Human readable time
     */
    public function format_seconds( int $seconds ): string {
        $days = floor( $seconds / 86400 );
        $hours = floor( ( $seconds % 86400 ) / 3600 );
        $minutes = floor( ( $seconds % 3600 ) / 60 );
        $seconds = $seconds % 60;

        $result = array();

        if ( $days > 0 ) {
            $result[] = sprintf( \_n( '%d day', '%d days', $days, \WPCA_TEXT_DOMAIN ), $days );
        }
        if ( $hours > 0 ) {
            $result[] = sprintf( \_n( '%d hour', '%d hours', $hours, \WPCA_TEXT_DOMAIN ), $hours );
        }
        if ( $minutes > 0 ) {
            $result[] = sprintf( \_n( '%d minute', '%d minutes', $minutes, \WPCA_TEXT_DOMAIN ), $minutes );
        }
        if ( $seconds > 0 ) {
            $result[] = sprintf( \_n( '%d second', '%d seconds', $seconds, \WPCA_TEXT_DOMAIN ), $seconds );
        }

        return implode( ', ', $result );
    }
}
