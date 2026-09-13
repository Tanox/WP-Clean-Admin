<?php
/**
 * Diagnostics Runner Tasks
 *
 * Runs all or a single diagnostic check and aggregates results.
 *
 * @package WPCleanAdmin
 * @version 1.8.15
 * @author Tanox
 * @author URI: https://github.com/Tanox
 * @since 1.8.0
 */
namespace WPCleanAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check-execution tasks for the Diagnostics class.
 */
trait DiagnosticsRunnerTasks {

    public function run_all_checks(): array {
        $results = array(
            'status' => 'success',
            'data' => array(),
            'summary' => array(
                'total' => count( $this->checks ),
                'passed' => 0,
                'warning' => 0,
                'error' => 0
            )
        );

        foreach ( $this->checks as $check_id => $check ) {
            try {
                $result = call_user_func( $check['callback'] );
                $result['id'] = $check_id;
                $result['name'] = $check['name'];
                $result['category'] = $check['category'];
                $result['severity'] = $check['severity'];

                $results['data'][] = $result;

                if ( $result['status'] === 'pass' ) {
                    $results['summary']['passed']++;
                } elseif ( $result['status'] === 'warning' ) {
                    $results['summary']['warning']++;
                } else {
                    $results['summary']['error']++;
                }
            } catch ( \Exception $e ) {
                $results['data'][] = array(
                    'id' => $check_id,
                    'name' => $check['name'],
                    'category' => $check['category'],
                    'severity' => $check['severity'],
                    'status' => 'error',
                    'message' => __( 'Check failed to execute', WPCA_TEXT_DOMAIN ),
                    'details' => $e->getMessage()
                );
                $results['summary']['error']++;
            }
        }

        if ( $results['summary']['error'] > 0 ) {
            $results['status'] = 'error';
        } elseif ( $results['summary']['warning'] > 0 ) {
            $results['status'] = 'warning';
        }

        return $results;
    }

    public function run_check( string $check_id ) {
        if ( ! isset( $this->checks[$check_id] ) ) {
            return array(
                'status' => 'error',
                'message' => __( 'Check not found', WPCA_TEXT_DOMAIN )
            );
        }

        $check = $this->checks[$check_id];
        try {
            $result = call_user_func( $check['callback'] );
            $result['id'] = $check_id;
            $result['name'] = $check['name'];
            $result['category'] = $check['category'];
            $result['severity'] = $check['severity'];
            return $result;
        } catch ( \Exception $e ) {
            return array(
                'id' => $check_id,
                'name' => $check['name'],
                'category' => $check['category'],
                'severity' => $check['severity'],
                'status' => 'error',
                'message' => __( 'Check failed to execute', WPCA_TEXT_DOMAIN ),
                'details' => $e->getMessage()
            );
        }
    }
}
