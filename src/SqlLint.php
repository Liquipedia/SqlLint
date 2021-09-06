<?php

namespace Liquipedia\SqlLint;

use PhpMyAdmin\SqlParser\Lexer;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Utils\Error as ParserError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class SqlLint {

	public static function lint() {
		$return = 0;
		$output = PHP_EOL;
		$directory = new RecursiveDirectoryIterator( './' );
		$iterator = new RecursiveIteratorIterator( $directory );
		$regex = new RegexIterator( $iterator, '/^.+\.sql$/i', RecursiveRegexIterator::GET_MATCH );
		$items = [];
		foreach ( $regex as $item ) {
			$items[] = $item[ 0 ];
		}
		foreach ( $items as $file ) {
			$fileContent = file_get_contents( $file );
			$lexer = new Lexer( $fileContent );
			$parser = new Parser( $lexer->list );
			$errors = ParserError::get( [ $lexer, $parser ] );
			if ( count( $errors ) > 0 ) {
				$countErrors = count( $errors );
				$return = 1;
				$output .= 'FILE: ' . realpath( $file ) . PHP_EOL;
				$output .= str_repeat( '-', 70 ) . PHP_EOL;
				$output .= 'FOUND ' . $countErrors . ' ERROR' . ( $countErrors === 1 ? '' : 'S' ) . PHP_EOL;
				$output .= str_repeat( '-', 70 ) . PHP_EOL;
				$output .= implode( PHP_EOL, ParserError::format( $errors ) );
				$output .= PHP_EOL . PHP_EOL;
				echo 'E';
			} else {
				echo '.';
			}
		}
		if ( $return === 0 ) {
			echo PHP_EOL . 'No linting errors found' . PHP_EOL . PHP_EOL;
		} else {
			echo $output;
		}

		return $return;
	}

}
