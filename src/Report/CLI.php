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

	/**
	 * Mark if this is a tty which supports control codes
	 * @var bool
	 */
	private $isTty = false;

	public function __construct() {
		$this->output = PHP_EOL . PHP_EOL;
		if ( defined( 'STDOUT' ) ) {
			$this->isTty = stream_isatty( STDOUT );
		}
	}

	/**
	 * @param string $fileName
	 */
	public function addSuccess( string $fileName ): void {
		$this->markProgress( '.' );
	}

	/**
	 * @param string $fileName
	 * @param array<int, array<int, mixed>> $errors
	 */
	public function addError( string $fileName, array $errors ): void {
		$this->markProgress( 'E' );
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
	 * @param string $letter
	 */
	private function markProgress( string $letter ): void {
		echo $letter;
		if ( $this->isTty ) {
			$this->updateCounter();
			$this->addNewLineMaybe();
		} else {
			$this->addNonTtyCounterMaybe();
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
		echo $this->makeCounterText();
		echo self::RESTORE_CURSOR_POSITION;
	}

	/**
	 * Update counters so we can show a nice progress bar
	 */
	private function addNonTtyCounterMaybe(): void {
		if ( $this->lineCounter >= 60 ) {
			echo ' ';
			echo $this->makeCounterText();
			echo PHP_EOL;
			$this->lineCounter = 0;
		}
	}

	/**
	 * Make text for percentage
	 * @return string
	 */
	private function makeCounterText(): string {
		return $this->totalCounter . '/' . $this->amount
			. ' (' . round( ( $this->totalCounter / $this->amount ) * 100, 1 ) . '%)';
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
