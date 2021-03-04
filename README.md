# SqlLint
![Code Style](https://github.com/Liquipedia/SqlLint/workflows/Code%20Style/badge.svg)

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
		]
	}
}
```

and run

```sh
composer sqllint
```
[1]:https://github.com/phpmyadmin/sql-parser
[2]:https://getcomposer.org/
