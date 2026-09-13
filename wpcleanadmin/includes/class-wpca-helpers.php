<?php
/**
 * WPCleanAdmin Helpers Class
 *
 * Aggregates formatting, environment, response, error-handling and logging
 * helper methods, each defined in a dedicated single-responsibility trait.
 *
 * @package WPCleanAdmin
 * @version 1.8.14
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.7.15
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load WordPress stubs for IDE compatibility
require_once __DIR__ . '/wpca-wordpress-stubs.php';

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
 *
 * Public method contract is preserved; behavior is provided by the traits
 * composed below.
 */
class Helpers {

    use HelpersFormatTasks;
    use HelpersEnvTasks;
    use HelpersResponseTasks;
    use HelpersErrorHandlingTasks;
    use HelpersLogTasks;

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
}
