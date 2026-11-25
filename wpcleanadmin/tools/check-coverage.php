<?php
/**
 * Coverage gate checker
 *
 * 解析 PHPUnit 生成的 Clover 报告并校验总覆盖率阈值。
 *
 * 用法：php tools/check-coverage.php /tmp/clover.xml 70
 *
 * @package WPCleanAdmin
 * @version 1.9.18
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 1 );
}

$report_file = isset( $argv[1] ) ? $argv[1] : '';
$threshold   = isset( $argv[2] ) ? (float) $argv[2] : 70.0;

if ( '' === $report_file || ! file_exists( $report_file ) ) {
	fwrite( STDERR, "Coverage report not found: {$report_file}\n" );
	exit( 1 );
}

$xml = simplexml_load_file( $report_file );

if ( false === $xml ) {
	fwrite( STDERR, "Unable to parse coverage report: {$report_file}\n" );
	exit( 1 );
}

$metrics = $xml->project->metrics;

if ( ! isset( $metrics['statements'] ) ) {
	fwrite( STDERR, "Coverage report has no statement metrics\n" );
	exit( 1 );
}

$statements = (int) $metrics['statements'];
$covered    = (int) $metrics['coveredstatements'];
$total      = $statements > 0 ? $statements : 1;
$percentage = ( $covered / $total ) * 100;

printf( "Total coverage: %.2f%% (%d/%d statements), threshold %.2f%%\n", $percentage, $covered, $statements, $threshold );

if ( $percentage + 0.005 < $threshold ) {
	fwrite( STDERR, "Coverage below threshold\n" );
	exit( 1 );
}

exit( 0 );
