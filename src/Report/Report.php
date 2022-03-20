<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint\Report;

abstract class Report implements IReport {

	/**
	 * This will be printed after all files have been processed
	 * @var string
	 */
	protected $output = '';

	/**
	 * @var int
	 */
	protected $amount;

	/**
	 * Set total amount of files to be linted so we can show a progress bar
	 * @param int $amount
	 */
	public function setAmountFiles( int $amount ): void {
		$this->amount = $amount;
	}

	/**
	 * This is the exit code that will be passed out of the program
	 * @var int
	 */
	protected $exitCode = 0;

	/**
	 * Returns the exit code that will be passed out of the program
	 * @return int
	 */
	public function getExitCode(): int {
		return $this->exitCode;
	}

}
