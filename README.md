# php-tools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.org/mirko-pagliai/php-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/php-tools)
[![Build status](https://ci.appveyor.com/api/projects/status/dexhrwff7w814wt3?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/php-tools)
[![codecov](https://codecov.io/gh/mirko-pagliai/php-tools/branch/master/graph/badge.svg)](https://codecov.io/gh/mirko-pagliai/php-tools)

*php-tools* adds some useful global functions and some classes and methods.

## Installation
You can install the package via composer:

    $ composer require --prefer-dist mirko-pagliai/php-tools

## Global functions
- `clean_url($url)` Cleans an url, removing all unnecessary parts, as fragment (#) and trailing slash
- `dir_tree($path, $exceptions = false)` Returns an array of nested directories and files in each directory
- `get_child_methods($class)` Gets the class methods' names, but unlike the `get_class_methods()` function, this function excludes the methods of the parent class
- `is_external_url($url, $hostname)` Checks if an url is external. The check is performed by comparing the URL with the passed hostname
- `is_json($string)` Checks if a string is JSON
- `is_positive($string)` Checks if a string is a positive number
- `is_slash_term($path)` Checks if a path ends in a slash (i.e. is slash-terminated)
- `is_url($string)` Checks if a string is a valid url
- `is_win()` Returns `true` if the environment is Windows
- `is_writable_resursive($dirname, $checkOnlyDir = true)` - Tells whether a directory and its subdirectories are writable. It can also check that all the files are writable
- `rmdir_recursive($dirname)` - Removes a directory and all its contents, including subdirectories and files
- `rtr($path)` Returns a path relative to the root. The root path must be set with the `ROOT` environment variable  (using the `putenv()` function) or the `ROOT` constant.
- `unlink_resursive($dirname, $exceptions = false)` - Recursively removes all the files contained in a directory and its sub-directories
- `which($command)` Executes the `which` command and shows the full path of (shell) commands

## "Or fail" functions
- `file_exists_or_fail($filename)` - Checks whether a file or directory exists and throws an exception if the file does not exist
- `is_dir_or_fail($filename)` - Tells whether the filename is a directory and throws an exception if the filename is not a directory
- `is_readable_or_fail($filename)` - Tells whether a file exists and is readable and throws an exception if the file is not readable
- `is_writable_or_fail($filename)` - Tells whether the filename is writable and throws an exception if the file is not writable

## Safe functions
- `safe_copy($source, $dest)` - Safe alias for `copy()` function
- `safe_mkdir($pathname, $mode = 0777, $recursive = false)` - Safe alias for `mkdir()` function
- `safe_rmdir($dirname)` - Safe alias for `rmdir()` function
- `safe_rmdir_recursive($dirname)` - Safe alias for `rmdir_recursive()` function
- `safe_symlink($target, $link)` - Safe alias for `symlink()` function
- `safe_unlink($filename)` - Safe alias for `unlink()` function
- `safe_unlink_recursive($dirname, $exceptions = false)` - Safe alias for `safe_unlink_recursive()` function
- `safe_unserialize($str)` - Safe alias for `unserialize()` function

## Classes and methods
### Apache
`Apache` is a class that provides some useful methods for interacting with Apache.

Available methods are:
- `is_enabled($module)` - Checks if a module is enabled
- `version()` - Gets the version

### FileArray
`FileArray` is a class allows you to read and write arrays using text files.

Available methods are:
- `__construct($filename, array $data = [])` - Constructor
- `append($data)` - Appends data to existing data
- `delete($key)` - Deletes a value from its key number. Note that the keys will
    be re-ordered.
- `get($key)` - Gets a value from its key number
- `prepend($data)` - Prepends data to existing data
- `read()` - Reads data. The first time, the file content is read. The next time
    the property value will be returned. If there are no data or if the file
    does not exist, it still returns an empty array
- `write()` - Writes data to the file

### ReflectionTrait
`ReflectionTrait` is a trait that works as a wrapper for the `Reflection` classes provided by PHP, and allows you to easily:
- invoke protected or private methods;
- set/get protected or private properties.

Available methods are:
- `getProperty(&$object, $propertyName)` - Gets a property value
- `invokeMethod(&$object, $methodName, array $parameters = [])` - Invokes a method
- `setProperty(&$object, $propertyName, $propertyValue)` - Sets a property value
    
This trait comes to test protected and private methods and properties with
PHPUnit.

### TestCaseTrait
`TestCaseTrait` is a trait that provides some assertion methods.

Available methods are:

    assertArrayKeysEqual($expectedKeys, $array, $message = '')
    assertObjectPropertiesEqual($expectedProperties, $object, $message = '')
    assertFileExists($filename, $message = '')
    assertFileExtension($expectedExtension, $filename, $message = '')
    assertFileMime($filename, $expectedMime, $message = '')
    assertFileNotExists($filename, $message = '')
    assertFilePerms($filename, $expectedPerms, $message = '')
    assertImageSize($filename, $expectedWidth, $expectedHeight, $message = '')
    assertInstanceOf($expectedInstance, $object, $message = '')
    assertIsArray($var, $message = '')
    assertIsArrayNotEmpty($var, $message = '')
    assertIsInt($var, $message = '')
    assertIsObject($var, $message = '')
    assertIsString($var, $message = '')
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
