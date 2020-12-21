# Weeglot

Technical test to Weeglot

## Requirements

PHP >= 7.2.5

## Installation

```bash
composer install
```

## Usage

There is two command to test the api google translate.

To test the version 2:
```bash
php bin/console app:translateV2
```

To test the version 3:
```bash
php bin/console app:translate
```
It is the same data of the original project

You can test the OffsetEncodingAlgorithm Class with:

```bash
php bin/console test
```
but it is already test in the two commands.
