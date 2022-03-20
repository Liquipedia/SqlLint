<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint;

class SqlLint {

	/**
	 * @return int
	 */
	public static function lint(): int {
		$report = new Report\CLI;
		$runner = new Runner( $report );
		return $runner->run();
	}

}
