<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint\Report;

interface IReport {

	public const REPORT_TYPES = [
		'cli' => CLI::class,
		'junit' => JUnit::class,
	];

	/**
	 * Set total amount of files to be linted so we can show a progress bar
	 * @param int $amount
	 */
	public function setAmountFiles( int $amount ): void;

	/**
	 * @param string $fileName
	 */
	public function addSuccess( string $fileName ): void;

	/**
	 * @param string $fileName
	 * @param array<int, array<int, mixed>> $errors
	 */
	public function addError( string $fileName, array $errors ): void;

	/**
	 * @return int
	 */
	public function getExitCode(): int;

	public function printBody(): void;

}
