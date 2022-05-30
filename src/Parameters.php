<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint;

class Parameters {

	private const PARAMETERS = [
		'help' => [
			'description' => 'Displays this help command',
			'default' => false,
		],
		'report' => [
			'values' => [
				'cli',
				'junit',
			],
			'default' => 'cli',
			'description' => 'Defines the output formatter',
		],
	];

	/**
	 * @var array<string, string>|null
	 */
	private static $stringParameters = null;

	/**
	 * @var array<string, bool>|null
	 */
	private static $boolParameters = null;

	/**
	 * @param string $parameter
	 * @return string
	 */
	public static function get( string $parameter ): string {
		self::initialiseCliParamsMaybe();

		if ( self::$stringParameters !== null && array_key_exists( $parameter, self::$stringParameters ) ) {
			if (
				array_key_exists( 'values', self::PARAMETERS[ $parameter ] )
				&& in_array( self::$stringParameters[ $parameter ], self::PARAMETERS[ $parameter ][ 'values' ] )
			) {
				return self::$stringParameters[ $parameter ];
			} else {
				$message =
					PHP_EOL . 'ERROR: Unknown parameter "' . $parameter . '"';
				if ( array_key_exists( 'values', self::PARAMETERS[ $parameter ] ) ) {
					$message .= ', should be one of'
						. ' "' . implode( '", "', self::PARAMETERS[ $parameter ][ 'values' ] ) . '"';
				}
				$message .= PHP_EOL . PHP_EOL;
				die( $message );
			}
		} elseif ( array_key_exists( $parameter, self::PARAMETERS ) ) {
			return strval( self::PARAMETERS[ $parameter ][ 'default' ] );
		} else {
			die(
				PHP_EOL . 'ERROR: Unknown parameter "' . $parameter . '"' . PHP_EOL . PHP_EOL
			);
		}
	}

	/**
	 * @param string $parameter
	 * @return bool
	 */
	public static function getBool( string $parameter ): bool {
		self::initialiseCliParamsMaybe();

		if ( self::$boolParameters !== null && array_key_exists( $parameter, self::$boolParameters ) ) {
			return true;
		} elseif ( array_key_exists( $parameter, self::PARAMETERS ) ) {
			return boolval( self::PARAMETERS[ $parameter ][ 'default' ] );
		} else {
			return false;
		}
	}

	public static function displayHelp(): void {
		$parameterMaxLength = max( array_map( 'strlen', array_keys( self::PARAMETERS ) ) );
		$parameterSpacer = $parameterMaxLength + 6;
		$help = 'Available parameters:' . PHP_EOL . PHP_EOL;

		foreach ( self::PARAMETERS as $key => $value ) {
			$help .= ' --' . $key . str_repeat( ' ', $parameterMaxLength - strlen( $key ) + 3 )
				. $value[ 'description' ] . PHP_EOL;
			if ( array_key_exists( 'values', $value ) ) {
				$help .= str_repeat( ' ', $parameterSpacer )
					. 'Values: One of "' . implode( '", "', $value[ 'values' ] ) . '"' . PHP_EOL
					. str_repeat( ' ', $parameterSpacer )
					. 'Default: "' . $value[ 'default' ] . '"' . PHP_EOL;
			}
		}
		echo $help;
	}

	private static function initialiseCliParamsMaybe(): void {
		if ( self::$stringParameters === null ) {
			$params = [];
			foreach ( self::PARAMETERS as $key => $value ) {
				$paramType = $key;
				if ( array_key_exists( 'values', $value ) ) {
					$paramType .= '::';
				}
				$params[] = $paramType;
			}
			$opts = getopt( '', $params );
			foreach ( self::PARAMETERS as $key => $value ) {
				if ( array_key_exists( $key, $opts ) ) {
					if ( is_array( $opts[ $key ] ) ) {
						die( PHP_EOL . 'ERROR: More than one value for "' . $key . '"' . PHP_EOL . PHP_EOL );
					} elseif ( is_string( $opts[ $key ] ) ) {
						self::$stringParameters[ $key ] = $opts[ $key ];
					} elseif ( is_bool( $opts[ $key ] ) ) {
						self::$boolParameters[ $key ] = $opts[ $key ];
					}
				}
			}
		}
	}

}
