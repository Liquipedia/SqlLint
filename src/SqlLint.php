<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint;

use Liquipedia\SqlLint\Report\IReport;

class SqlLint {

	/**
	 * @return int
	 */
	public static function lint(): int {
		$args = getopt( '', [ 'report::' ] );
		$reportType = 'cli';
		if ( array_key_exists( 'report', $args ) ) {
			if ( array_key_exists( strval( $args[ 'report' ] ), IReport::REPORT_TYPES ) ) {
				$reportType = strval( $args[ 'report' ] );
			} else {
				die(
					PHP_EOL . 'ERROR: Unknown report type "' . strval( $args[ 'report' ] ) . '",'
					. ' should be one of "' . implode( '", "', array_keys( IReport::REPORT_TYPES ) ) . '"'
					. PHP_EOL . PHP_EOL
				 );
			}
		}
		$reportClass = IReport::REPORT_TYPES[ $reportType ];

		$report = new $reportClass;
		$runner = new Runner( $report );
		return $runner->run();
	}

}
