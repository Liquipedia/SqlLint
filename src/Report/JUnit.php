<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint\Report;

use SimpleXMLElement;

class JUnit extends Report {

	/**
	 * @var SimpleXMLElement
	 */
	private $outputXml;

	public function __construct() {
		$this->outputXml = new SimpleXMLElement(
			'<testsuite'
			. ' name="sqllint"'
			. ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
			. ' xsi:noNamespaceSchemaLocation="'
			. 'https://raw.githubusercontent.com/junit-team/junit5/r5.8.2'
			. '/platform-tests/src/test/resources/jenkins-junit.xsd"'
			. ' />'
		);
	}

	/**
	 * @param string $fileName
	 */
	public function addSuccess( string $fileName ): void {
		$testCase = $this->outputXml->addChild( 'testcase' );
		$finalFileName = realpath( $fileName );
		if ( $finalFileName === false ) {
			// File somehow does not exist anymore
			$finalFileName = $fileName;
		}
		$testCase->addAttribute( 'name', $finalFileName );
	}

	/**
	 * @param string $fileName
	 * @param array<int, array<int, mixed>> $errors
	 */
	public function addError( string $fileName, array $errors ): void {
		$this->exitCode = 1;
		$finalFileName = realpath( $fileName );
		if ( $finalFileName === false ) {
			// File somehow does not exist anymore
			$finalFileName = $fileName;
		}
		foreach ( $errors as $i => $error ) {
			$testCase = $this->outputXml->addChild( 'testcase' );
			$testCase->addAttribute( 'name', $finalFileName . ' (' . ( $i + 1 ) . ')' );
			$failure = $testCase->addChild( 'failure' );
			$failure->addAttribute( 'type', 'ERROR' );
			$failure->addAttribute(
				'message',
				htmlspecialchars( strval( $error[ 0 ] ) )
				. ' (near \'' . htmlspecialchars( strval( $error[ 2 ] ) ) . '\''
				. ' at position ' . htmlspecialchars( strval( $error[ 3 ] ) ) . ')'
			);
		}
	}

	public function printBody(): void {
		echo $this->output;
		echo $this->outputXml->asXML();
	}

}
