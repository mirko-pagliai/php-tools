# php-tools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.org/mirko-pagliai/php-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/php-tools)
[![Build status](https://ci.appveyor.com/api/projects/status/dexhrwff7w814wt3?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/php-tools)
[![Coverage Status](https://img.shields.io/codecov/c/github/mirko-pagliai/php-tools.svg?style=flat-square)](https://codecov.io/github/mirko-pagliai/php-tools)

*php-tools* adds some useful global functions and some classes and methods.

## Installation
You can install the package via composer:

    $ composer require --prefer-dist mirko-pagliai/php-tools

## Global functions
- `get_child_methods($class)` Gets the class methods' names, but unlike the `get_class_methods()` function, this function excludes the methods of the parent class
- `is_json($string)` Checks if a string is JSON
- `is_positive(string)` Checks if a string is a positive number
- `is_slash_term($path)` Checks if a path ends in a slash (i.e. is slash-terminated)
- `is_url(string)` Checks if a string is a valid url
- `is_win()` Returns `true` if the environment is Windows
- `rtr()` Returns a path relative to the root. The root path must be set with the `ROOT` environment variable  (using the `putenv()` function) or the `ROOT` constant.
- `which($command)` Executes the `which` command and shows the full path of (shell) commands

## Classes and methods
### Apache
`Apache` is a class that provides some useful methods for interacting with Apache.

Available methods are:
    
    is_enabled($module)
    version()

### ReflectionTrait
`ReflectionTrait` is a trait that works as a wrapper for the `Reflection` classes provided by PHP, and allows you to easily:
- invoke protected or private methods;
- set/get protected or private properties.

Available methods are:

    invokeMethod(&$object, $methodName, array $parameters = [])
    getProperty(&$object, $propertyName)
    setProperty(&$object, $propertyName, $propertyValue)
    
This trait comes to test protected and private methods and properties with
PHPUnit.

### TestCaseTrait
`TestCaseTrait` is a trait that provides some assertion methods.

Available methods are:

    assertFileExtension($expectedExtension, $filename, $message = '')
    assertFileMime($filename, $expectedMime, $message = '')
    assertImageSize($filename, $expectedWidth, $expectedHeight, $message = '')
    assertSameMethods($firstClass, $secondClass, $message = '')

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
