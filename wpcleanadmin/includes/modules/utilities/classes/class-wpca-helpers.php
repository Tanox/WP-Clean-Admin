<?php
/**
 * WPCleanAdmin Helpers Class
 *
 * @package WPCleanAdmin
 * @version  1.9.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin\Modules\Utilities\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 引入拆分出的 Helpers trait
require_once __DIR__ . '/class-wpca-helpers-format-tasks.php';
require_once __DIR__ . '/class-wpca-helpers-error-tasks.php';
require_once __DIR__ . '/class-wpca-helpers-logging-tasks.php';
require_once __DIR__ . '/class-wpca-helpers-validation-tasks.php';

/**
 * Error codes for WPCleanAdmin plugin
 */
abstract class WPCA_Errors {
    /** @var int No error */
    const ERROR_NONE = 0;
    /** @var int Database error */
    const ERROR_DATABASE = 1001;
    /** @var int Permission denied */
    const ERROR_PERMISSION = 1002;
    /** @var int Invalid input */
    const ERROR_INVALID_INPUT = 1003;
    /** @var int File operation error */
    const ERROR_FILE_OPERATION = 1004;
    /** @var int AJAX error */
    const ERROR_AJAX = 1005;
    /** @var int Settings validation error */
    const ERROR_VALIDATION = 1006;
    /** @var int Authentication error */
    const ERROR_AUTH = 1007;
    /** @var int Unknown error */
    const ERROR_UNKNOWN = 9999;
}

/**
 * Helpers class
 */
class Helpers {

    use HelpersFormatTasks;
    use HelpersErrorTasks;
    use HelpersLoggingTasks;
    use HelpersValidationTasks;

    /**
     * Singleton instance
     *
     * @var Helpers
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return Helpers
     */
    public static function getInstance(): Helpers {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Empty constructor
    }

    /**
     * Sanitize array of data
     *
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    public function sanitize_array( $data ): array {
        if ( ! is_array( $data ) ) {
            return array();
        }

        foreach ( $data as &$value ) {
            if ( is_array( $value ) ) {
                $value = $this->sanitize_array( $value );
            } else {
                $value = \sanitize_text_field( $value );
            }
        }

        return $data;
    }
}
