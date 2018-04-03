# php-tools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.org/mirko-pagliai/php-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/php-tools)
[![Build status](https://ci.appveyor.com/api/projects/status/dexhrwff7w814wt3?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/php-tools)
[![Coverage Status](https://img.shields.io/codecov/c/github/mirko-pagliai/php-tools.svg?style=flat-square)](https://codecov.io/github/mirko-pagliai/php-tools)

*php-tools* adds some useful global functions and some classes and methods.

## Installation
You can install the package via composer:

    $ composer require --prefer-dist mirko-pagliai/php-tools

## Tests
Tests are divided into two groups, `onlyUnix` and `onlyWindows`. This is
necessary because some commands to be executed in the terminal are only valid
for an environment.

By default, phpunit is executed like this:

    vendor/bin/phpunit --exclude-group=onlyWindows

On Windows, it must be done this way:

    vendor/bin/phpunit --exclude-group=onlyUnix

## Versioning
For transparency and insight into our release cycle and to maintain backward 
compatibility, *php-tools* will be maintained under the 
[Semantic Versioning guidelines](http://semver.org).
