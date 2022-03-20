<?php

namespace Liquipedia\SqlLint;

use Liquipedia\SqlLint\Report\IReport;
use PhpMyAdmin\SqlParser\Lexer;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Utils\Error as ParserError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class Runner {

	/**
	 * @var IReport
	 */
	private $report;

	/**
	 * @param IReport $report
	 */
	public function __construct( IReport $report ) {
		$this->report = $report;
	}

	/**
	 * @return int
	 */
	public function run(): int {
		$directory = new RecursiveDirectoryIterator( './' );
		$iterator = new RecursiveIteratorIterator( $directory );
		$regex = new RegexIterator( $iterator, '/^.+\.sql$/i', RecursiveRegexIterator::GET_MATCH );
		$items = [];
		foreach ( $regex as $item ) {
			$items[] = $item[ 0 ];
		}
		$this->report->setAmountFiles( count( $items ) );
		foreach ( $items as $fileName ) {
			$fileContent = file_get_contents( $fileName );
			$lexer = new Lexer( $fileContent );
			$parser = new Parser( $lexer->list );
			$errors = ParserError::get( [ $lexer, $parser ] );
			if ( count( $errors ) > 0 ) {
				$this->report->addError( $fileName, $errors );
			} else {
				$this->report->addSuccess( $fileName );
			}
		}

		$this->report->printBody();

		return $this->report->getExitCode();
	}

}
