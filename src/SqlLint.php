<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint;

use Liquipedia\SqlLint\Report\IReport;

class SqlLint {

	/**
	 * @return int
	 */
	public static function lint(): int {
		if ( Parameters::getBool( 'help', false ) ) {
			Parameters::displayHelp();
			return 0;
		} else {
			$reportClass = IReport::REPORT_TYPES[ Parameters::get( 'report', 'cli' ) ];

			$report = new $reportClass;
			$runner = new Runner( $report );
			return $runner->run();
		}
	}

}
