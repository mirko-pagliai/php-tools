# php-tools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.org/mirko-pagliai/php-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/php-tools)
[![Build status](https://ci.appveyor.com/api/projects/status/dexhrwff7w814wt3?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/php-tools)
[![codecov](https://codecov.io/gh/mirko-pagliai/php-tools/branch/master/graph/badge.svg)](https://codecov.io/gh/mirko-pagliai/php-tools)

*php-tools* adds some useful global functions and some classes and methods.

Did you like this plugin? Its development requires a lot of time for me.
Please consider the possibility of making [a donation](//paypal.me/mirkopagliai):  
even a coffee is enough! Thank you.

[![Make a donation](https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_carte.jpg)](//paypal.me/mirkopagliai)

## Installation
You can install the package via composer:

    $ composer require --prefer-dist mirko-pagliai/php-tools

## Global functions
- `clean_url($url, $removeWWW = false, $removeTrailingSlash = false)` - Cleans an url, removing all unnecessary parts, as fragment (#) and trailing slash
- `create_file($filename, $data = null)` - Creates a file. It also recursively creates the directory where the file will be created
- `create_tmp_file($data = null)` - Creates a tenporary file. The file will be created in `TMP`, if the constant is defined, otherwise in the temporary directory of the system
- `dir_tree($path, $exceptions = false)` - Returns an array of nested directories and files in each directory
- `ends_with($haystack, $needle)` - Checks if a string ends with a string
- `first_value(array $array)` - Returns the first value of an array
- `get_child_methods($class)` - Gets the class methods' names, but unlike the `get_class_methods()` function, this function excludes the methods of the parent class
- `is_external_url($url, $hostname)` - Checks if an url is external. The check is performed by comparing the URL with the passed hostname
- `is_json($string)` - Checks if a string is JSON
- `is_positive($string)` - Checks if a string is a positive number
- `is_slash_term($path)` - Checks if a path ends in a slash (i.e. is slash-terminated)
- `is_url($string)` - Checks if a string is a valid url
- `is_win()` - Returns `true` if the environment is Windows
- `is_writable_resursive($dirname, $checkOnlyDir = true)` - Tells whether a directory and its subdirectories are writable. It can also check that all the files are writable
- `last_value(array $array)` - Returns the last value of an array
- `rmdir_recursive($dirname)` - Removes a directory and all its contents, including subdirectories and files
- `rtr($path)` Returns a path relative to the root. The root path must be set with the `ROOT` environment variable  (using the `putenv()` function) or the `ROOT` constant.
- `starts_with($haystack, $needle)` - Checks if a string starts with a string
- `unlink_resursive($dirname, $exceptions = false)` - Recursively removes all the files contained in a directory and its sub-directories
- `which($command)` - Executes the `which` command and shows the full path of (shell) commands

## "Or fail" functions
- `file_exists_or_fail($filename)` - Checks whether a file or directory exists and throws an exception if the file does not exist
- `is_dir_or_fail($filename)` - Tells whether the filename is a directory and throws an exception if the filename is not a directory
- `is_readable_or_fail($filename)` - Tells whether a file exists and is readable and throws an exception if the file is not readable
- `is_writable_or_fail($filename)` - Tells whether the filename is writable and throws an exception if the file is not writable
- `is_true_or_fail($value, $message = 'The value is not equal to `true`', $exception = \ErrorException::class)` - Throws an exception if the value is not equal to `true`

## Safe functions
- `safe_copy($source, $dest)` - Safe alias for `copy()` function
- `safe_create_file($filename, $data = null)` - Safe alias for `create_file()` function
- `safe_create_tmp_file($data = null)` - Safe alias for `create_tmp_file()` function
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

### BodyParser
`BodyParser` is a class that can tell you if a body contains HTML code and can
    extract links from body.

Available methods are:
- `__construct($body, $url)` - Constructor
- `extractLinks()` - Extracs links from body
- `isHtml()` - Returns `true` if the body contains HTML code

### FileArray
`FileArray` is a class allows you to read and write arrays using text files.

Available methods are:
- `__construct($filename, array $data = [])` - Constructor
- `append($data)` - Appends data to existing data
- `delete($key)` - Deletes a value from its key number. Note that the keys will be re-ordered.
- `exists($key)` - Checks if a key number exists
- `get($key)` - Gets a value from its key number
- `prepend($data)` - Prepends data to existing data
- `read()` - Reads data. The first time, the file content is read. The next time the property value will be returned.
    If there are no data or if the file does not exist, it still returns an empty array
- `take($size, $from = 0)` - Extract a slice of data, with maximum `$size` values. If a second parameter is passed,
    it will determine from what position to start taking values
- `write()` - Writes data to the file

### ReflectionTrait
`ReflectionTrait` is a trait that works as a wrapper for the `Reflection` classes provided by PHP, and allows you to easily:
- invoke protected or private methods;
- set/get protected or private properties.

Available methods are:
- `getProperties(&$object, $filter = null)` - Gets all properties as array with property names as keys. If the object is a
    mock, it removes the properties added by PHPUnit
- `getProperty(&$object, $propertyName)` - Gets a property value
- `invokeMethod(&$object, $methodName, array $parameters = [])` - Invokes a method
- `setProperty(&$object, $propertyName, $propertyValue)` - Sets a property value

This trait comes to test protected and private methods and properties with
PHPUnit.

### TestCaseTrait
`TestCaseTrait` is a trait that provides some assertion methods.

Available methods are:
    assertArrayKeysEqual($expectedKeys, $array, $message = '')
    assertContainsInstanceOf($expectedInstance, $value, $message = '')
    assertObjectPropertiesEqual($expectedProperties, $object, $message = '')
    assertFileExists($filename, $message = '')
    assertFileExtension($expectedExtension, $filename, $message = '')
    assertFileMime($filename, $expectedMime, $message = '')
    assertFileNotExists($filename, $message = '')
    assertFilePerms($filename, $expectedPerms, $message = '')
    assertImageSize($filename, $expectedWidth, $expectedHeight, $message = '')
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
