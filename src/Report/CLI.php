<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint\Report;

use PhpMyAdmin\SqlParser\Utils\Error as ParserError;

class CLI extends Report {

	public const SAVE_CURSOR_POSITION = "\x1b7";
	public const RESTORE_CURSOR_POSITION = "\x1b8";
	public const CLEAR_LINE_AFTER_CURRENT_POSITION = "\x1b[K";
	public const GOTO_COLUMN_62 = "\x1b[62G";

	/**
	 * Current line counter for progress bar
	 * @var int
	 */
	private $lineCounter = 0;

	/**
	 * Counter for calculating percentage of files linted
	 * @var int
	 */
	private $totalCounter = 0;

	public function __construct() {
		$this->output = PHP_EOL . PHP_EOL;
	}

	/**
	 * @param string $fileName
	 */
	public function addSuccess( string $fileName ): void {
		echo '.';
		$this->updateCounter();
		$this->addNewLineMaybe();
	}

	/**
	 * @param string $fileName
	 * @param array<int, array<int, mixed>> $errors
	 */
	public function addError( string $fileName, array $errors ): void {
		echo 'E';
		$this->updateCounter();
		$this->addNewLineMaybe();
		$this->exitCode = 1;
		$countErrors = count( $errors );
		$this->output .= 'FILE: ' . realpath( $fileName ) . PHP_EOL;
		$this->output .= str_repeat( '-', 70 ) . PHP_EOL;
		$this->output .= 'FOUND ' . $countErrors . ' ERROR' . ( $countErrors === 1 ? '' : 'S' ) . PHP_EOL;
		$this->output .= str_repeat( '-', 70 ) . PHP_EOL;
		$this->output .= implode( PHP_EOL, ParserError::format( $errors ) );
		$this->output .= PHP_EOL . PHP_EOL;
	}

	public function printBody(): void {
		if ( $this->exitCode === 0 ) {
			echo PHP_EOL . 'No linting error found' . PHP_EOL . PHP_EOL;
		} else {
			echo $this->output;
		}
	}

	/**
	 * Update counters so we can show a nice progress bar
	 */
	private function updateCounter(): void {
		$this->lineCounter++;
		$this->totalCounter++;
		echo self::SAVE_CURSOR_POSITION;
		echo self::GOTO_COLUMN_62;
		echo self::CLEAR_LINE_AFTER_CURRENT_POSITION;
		echo $this->totalCounter . '/' . $this->amount
			. ' (' . round( ( $this->totalCounter / $this->amount ) * 100, 1 ) . '%)';
		echo self::RESTORE_CURSOR_POSITION;
	}

	/**
	 * After 60 files we want a new line so things don't get too wide
	 */
	private function addNewLineMaybe(): void {
		if ( $this->lineCounter >= 60 ) {
			echo PHP_EOL;
			$this->lineCounter = 0;
		}
	}

}
