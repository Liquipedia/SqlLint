# SqlLint
![Code Style](https://github.com/Liquipedia/SqlLint/workflows/Code%20Style/badge.svg)
<a href="https://packagist.org/packages/liquipedia/sqllint"><img src="https://img.shields.io/packagist/dt/liquipedia/sqllint" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/liquipedia/sqllint"><img src="https://img.shields.io/packagist/v/liquipedia/sqllint" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/liquipedia/sqllint"><img src="https://img.shields.io/packagist/l/liquipedia/sqllint" alt="License"></a>

This is a thin wrapper around the SqlParser from the [phpMyAdmin project][1] which can be used to lint any amount of sql files from the command line.

## Installation

Please use [Composer][2] to install:

```sh
composer require liquipedia/sqllint
```

## Usage

### Command line utility

```sh
./vendor/bin/sqllint
```

```sh
./vendor/bin/sqllint --report=junit > report-junit.xml
```

### Via composer script

Add this to your composer.json

```json
{
	"require-dev": {
		"liquipedia/sqllint": "*"
	},
	"scripts": {
		"sqllint": [
			"sqllint"
		],
		"sqllint-junit": [
			"sqllint --report=junit > report-junit.xml"
		]
	}
}
```

and run

```sh
composer sqllint
```

### Parameter
To check out parameters, please refer to the `src/Parameters.php` file or use the `--help` parameter.

### `.sqllint.json`
Parameters for SqlLint can optionally also be provided via a `.sqllint.json` file in the project root. Parameters passed via command line always have precedence over the parameters set in `.sqllint.json`.

[1]:https://github.com/phpmyadmin/sql-parser
[2]:https://getcomposer.org/
