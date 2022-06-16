<?php

declare( strict_types=1 );

namespace Liquipedia\SqlLint;

class Parameters {

	private const PARAMETERS = [
		'help' => [
			'type' => self::PARAM_TYPE_BOOL,
			'default' => false,
			'description' => 'Displays this help command',
		],
		'report' => [
			'type' => self::PARAM_TYPE_STRING,
			'values' => [
				'cli',
				'junit',
			],
			'default' => 'cli',
			'description' => 'Defines the output formatter',
		],
	];
	public const PARAM_TYPE_BOOL = 'bool';
	public const PARAM_TYPE_STRING = 'string';

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
		self::initialiseParamsMaybe();

		if ( self::$stringParameters !== null && array_key_exists( $parameter, self::$stringParameters ) ) {
			if (
				self::PARAMETERS[ $parameter ][ 'type' ] === self::PARAM_TYPE_STRING
				&& array_key_exists( 'values', self::PARAMETERS[ $parameter ] )
				&& in_array( self::$stringParameters[ $parameter ], self::PARAMETERS[ $parameter ][ 'values' ] )
			) {
				return self::$stringParameters[ $parameter ];
			} else {
				$message =
					PHP_EOL . 'ERROR: Unknown value for parameter "' . $parameter . '"';
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
		self::initialiseParamsMaybe();

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
			$help .= str_repeat( ' ', $parameterSpacer )
				. 'Type: ' . $value[ 'type' ] . PHP_EOL;
			if ( array_key_exists( 'values', $value ) ) {
				$help .= str_repeat( ' ', $parameterSpacer )
					. 'Values: One of "' . implode( '", "', $value[ 'values' ] ) . '"' . PHP_EOL
					. str_repeat( ' ', $parameterSpacer )
					. 'Default: "' . $value[ 'default' ] . '"' . PHP_EOL;
			}
		}
		echo $help;
	}

	private static function initialiseParamsMaybe(): void {
		if ( self::$stringParameters === null ) {
			self::$stringParameters = [];
			self::$boolParameters = [];
			// Config file gets initialised first because CLI params are
			// supposed to overwrite the config file if set
			self::initialiseConfigFileParams();
			self::initialiseCLIParams();
		}
	}

	private static function initialiseConfigFileParams(): void {
		$configFile = '.sqllint.json';
		$configFileWithPath = realpath( $configFile );
		if ( !$configFileWithPath ) {
			return;
		}
		$json = file_get_contents( $configFileWithPath );
		if ( is_string( $json ) ) {
			$jsonArray = json_decode( $json, true );
			$opts = [];
			if ( is_array( $jsonArray ) ) {
				foreach ( $jsonArray as $key => $value ) {
					if ( is_string( $key ) && ( is_string( $value ) || is_bool( $value ) ) ) {
						$opts[$key] = $value;
					}
				}
			}
			self::setParameterValues( $opts );
		}
	}

	private static function initialiseCLIParams(): void {
		$params = [];
		foreach ( self::PARAMETERS as $key => $value ) {
			$paramType = $key;
			if ( $value[ 'type' ] === self::PARAM_TYPE_STRING ) {
				$paramType .= '::';
			}
			$params[] = $paramType;
		}
		$opts = getopt( '', $params );
		self::setParameterValues( $opts );
	}

	/**
	 * @param array<string, string|bool|array<int, mixed>> $opts
	 */
	private static function setParameterValues( array $opts ): void {
		foreach ( self::PARAMETERS as $key => $value ) {
			if ( array_key_exists( $key, $opts ) ) {
				if ( is_array( $opts[ $key ] ) ) {
					die( PHP_EOL . 'ERROR: More than one value for "' . $key . '"' . PHP_EOL . PHP_EOL );
				} elseif ( $value[ 'type' ] === self::PARAM_TYPE_STRING ) {
					if ( is_string( $opts[ $key ] ) ) {
						self::$stringParameters[ $key ] = $opts[ $key ];
					} else {
						die(
							PHP_EOL . 'ERROR: Parameter "' . $key . '" should be of type "'
								. self::PARAM_TYPE_STRING . '"' . PHP_EOL . PHP_EOL
						);
					}
				} elseif ( $value[ 'type' ] === self::PARAM_TYPE_BOOL ) {
					if ( is_bool( $opts[ $key ] ) ) {
						self::$boolParameters[ $key ] = true;
					} else {
						die(
							PHP_EOL . 'ERROR: Parameter "' . $key . '" should be of type "'
								. self::PARAM_TYPE_BOOL . '"' . PHP_EOL . PHP_EOL
						);
					}
				}
			}
		}
	}

}
