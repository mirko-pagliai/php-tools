# 1.x branch
## 1.9 branch
### 1.9.4
* `TestTrait::assertArrayKeysEqual()` is deprecated and will be removed in a future release;
* `TestTrait::assertFileExtension()` is deprecated and will be removed in a future release;
* `TestTrait::assertFileMime()` is deprecated and will be removed in a future release;
* `TestTrait::assertImageSize()` is deprecated and will be removed in a future release;
* `TestTrait::assertIsArrayNotEmpty()` is deprecated and will be removed in a future release;
* `TestTrait::assertIsMock()` is deprecated and will be removed in a future release;
* `TestTrait::assertObjectPropertiesEqual()` is deprecated and will be removed in a future release;
* `TestTrait::assertSameMethods()` is deprecated and will be removed in a future release;
* `TestTrait::createPartialMockForAbstractClass()` is deprecated and will be removed in a future release;
* `TestTrait::deprecated()` is deprecated and will be removed in a future release. Use instead the PHPUnit Bridge;
* `array_clean()`, `array_has_only_numeric_keys()`, `array_to_string()`, `array_unique_recursive()`, `is_stringable()`
  and `uncamelcase()` global functions are deprecated and will be removed in a future release;
* removed `cakephp/core` package;

### 1.9.3
* fixed the signature for `TestTrait::deprecated()` method;
* requires `cakephp/core` 5.0;

### 1.9.2
* `url_to_absolute()` had been deprecated and has been removed;
* `slug()` global function had been deprecated and has been removed.

### 1.9.1-RC2
* requires at least PHPUnit 10;
* can use `cakephp/core` `4.0` or `5.0`;
* `TestTrait::assertException()` had been deprecated and has been removed;
* `BodyParser` had been deprecated and has been removed;
* `TestTrait::assertException()` had been deprecated and has been removed;
* `TestTrait::expectAssertionFailed()` had been deprecated and has been removed.

### 1.9.0-RC1
* requires at least PHP 8.1;
* the `deprecationWarning()` function has been replaced by the ominimum provided by the `cakephp/core` package.
  (`\Cake\Core\deprecationWarning`). Be careful, because the signature is different now. The `TestCase::deprecated()`
  method has been removed and is not in use for now;
* the `TestCase` class had been deprecated and has been removed. Use instead the `PHPUnit\Framework\TestCase` class (and
  possibly `ReflectionTrait` and `TestTrait`). The `deprecated()` method has been moved to `TestTrait`;
* `Entity` and `Exceptionist` classes had been deprecated and have been removed;
* `is_json()`, `objects_map()` and `which()` global functions had been deprecated and have been removed;
* all exception classes provided by this package had been deprecated and have been removed;
* the (old) `CommandTester` class has been removed;
* several (error suppression) rules have been removed from the `sniffer-ruleset.xml` file;
* added tests for PHP 8.2 and 8.3;
* updated `cakephp-codesniffer`, `phpstan` and `psalm` packages. Updated symfony's components.

## 1.8 branch
### 1.8.3
* `url_to_absolute()` is deprecated and will be removed in a later release;
* `slug()` is deprecated and will be removed in a later release.

### 1.8.2
* added tests for PHP 8.2;
* `BodyParser` is deprecated and will be removed in a later release;
* `TestTrait::assertException()` is deprecated and will be removed in a later release;
* `TestTrait::expectAssertionFailed()` is deprecated and will be removed in a later release.

### 1.8.1
* tests for `ReflectionTrait` and `TestTrait` have largely improved;
* the `TestCase` class has been deprecated and will be removed in a later release. Use instead the
  `PHPUnit\Framework\TestCase` class (and possibly `ReflectionTrait` and `TestTrait`). The `deprecated()` method has
  been moved to `TestTrait` (and the signature was updated);
* the `Exceptionist` class is deprecated and will be removed in a later release;
* all exception classes provided by this package are deprecated and will be removed in a later release;
* fixed configuration of phpcs.

### 1.8.0
* `Exceptionist::__callStatic()` method throw a `BadMethodCallException` exception on errors;
* added `TestCase::deprecated()` method, a helper method for check deprecation methods;
* `TestTrait::assertDeprecated()` method was removed, not deeming it useful to deprecate a method that dealt with deprecations;
* some small changes to prepare for PHPUnit 10.

## 1.7 branch
### 1.7.6
* added `is_array_key_first()` global function.

### 1.7.5
* added `is_array_key_last()` global function;
* The `Entity` class is deprecated and will be removed in a later release;
* `which()` global function is now deprecated. Use instead `Symfony\Component\Process\ExecutableFinder::find()` method;
* fixed `notice: Use of "self" in callables is deprecated` for `Filesystem` class.

### 1.7.4
* all methods provided by the `Filesystem` class (except for `makePathRelative()`) can now be called statically;
* added `rtr()` global function, a fast and convenient alias for `Filesystem::rtr()`;
* `objects_map()` global function is now deprecated and will be removed in a later release;
* `is_json()` global function is now deprecated. Use instead `json_validate()`.

### 1.7.3
* fixed a bug for `Exceptionist::isInstanceOf()`: this method now accepts instantiated objects or class name as string
    as its first argument;
* removed `Filesystem::isSlashTerm()` method. Use directly `addSlashTerm()` or `concatenate()` methods.

### 1.7.2
* added `TestTrait::createPartialMockForAbstractClass()` method;
* all methods of the `TestTrait` are now public. This has allowed for improved testing.

### 1.7.1
* re-added pguardiario/phpuri as library file.

### 1.7.0
* `\Tools\Exceptionist` has been completely rewritten and made more functional, simpler and better
  documented. We tried to keep compatibility with the old class as much as possible;
* all the exceptions offered by php-tools have been simplified by removing extra constructor parameters,
  properties and methods. Related tests have been deleted. Abstract `FileException` and
  `InvalidValueException` no longer exist;
* `Filesystem::isWritableResursive()` has been removed.

## 1.6 branch
### 1.6.5
* `isReadable()` and `isWritable()` method provided by `Exceptionist` no longer even call the
  `fileExists()` method;
* added `\Tools\TestSuite\TestTrait::assertDeprecated()` method;
* improved `\Tools\TestSuite\TestTrait::assertException()`.

### 1.6.4
* `object_map()` global function and `\Tools\Exceptionist::methodExists()` method throw the new 
  `\Tools\Exception\MethodNotExistsException`;
* by default, the `\Tools\Exceptionist::____callStatic()` magic method now throws an `ErrorException`;
* the `$exception` parameter is deprecated for all methods of `\Tools\Exceptionist` (except `isFalse()`,
  `isTrue()` and consequently `__callStatic()`). Use the default exceptions. Furthermore, using an
   already instantiated exception is also deprecated;
* all methods of `\Tools\Exceptionist` no longer accept a value of `null` as `$message` parameter;
* improved the description of many `\Tools\Exceptionist` magic methods, added many `@template` tags
  and now always refers to `Exception` and no longer to `Throwable`;
* `\Tools\TestSuite\TestTrait::assertException()` new performs a strict comparison and does not
  consider parent classes (`ErrorException` != `Exception`). It correctly ignores the  deprecations
  in determining the exception;
* improved the `array_clean()` global function.

### 1.6.3
* added `Filesystem::isWritableRecursive()` method, which replaces 
    `isWritableResursive()` (which had a serious typo). The previous method 
    will be kept (as deprecated) and will be removed in a later version;
* small and numerous improvements of descriptions, tags and code suggested 
    by PhpStorm.

### 1.6.2
* fixed a little issue for `Exceptionist` and `debug_functions.php`;
* little fixes for `phpstan`, `psalm` and for the `composer.json` file.

### 1.6.1
* `Filesystem::createFile()` returns now the filename as string;
* `get_child_methods()` global function throws an exception if the class does
    not exist and returns only array;
* updated the code with the new features introduced by php 7.4.

### 1.6.0
* `FileArray` has been removed;
* `string_ends_with()`, `string_contains()` and `string_starts_with()` have been
    removed;
* removed the backward compatibility with the previous `Exceptionist::inArray()`
    method, provided by `__call()`;
* `BackwardCompatibilityTrait` has been removed;
* requires at least PHP 7.4 and PHPUnit 9.1. Updated the code with the new
    features introduced by php 7.4.

## 1.5 branch
### 1.5.14
* `FileArray` is deprecated will be removed in a future version.

### 1.5.13
* added `CommandTester` class. This class overrides the one provided by the
    Symfony console component, offering additional methods.

### 1.5.12
* added `EventList::extract()` method;
* added tests for PHP 8.1.

### 1.5.11
* fixed little bug for `Filesystem::makePathAbsolute()` method.

### 1.5.10
* `Exceptionist::__callStatic()` now handles calls containing with the "Not"
    word (e.g. `isNotArray()` or `fileNotExists()`);
* added `EventAssertTrait::assertEventFiredWithArgs()` method;
* added `Filesystem::makePathRelative()` method;
* added `Exceptionist::isFalse()` method;
* fixed little bug for `Filesystem::rtr()`;
* many methods already supported by `Exceptionist` have been documented.

### 1.5.9
* `string_ends_with()`, `string_contains()` and `string_starts_with()` are now
    deprecated. Use instead `str_ends_with()`, `str_contains()` and `str_starts_with()`;
* `array_key_first()` and `array_key_last()` are now provided by `symfony/polyfill-php73`;
* updated for Symfony `6.0` components.

### 1.5.8
* `Entity::set()` can set null or empty values and no longer throws an exception.
    This means that `Entity :: has ()` returns `true` even for properties with
    empty or null value;
* added `Exceptionist::inArray()` method;
* added `Entity::hasValue()` and `Entity::isEmpty()` methods;
* added `array_to_string()` global function;
* `is_stringable()` function now returns `true` for arrays that can be converted
    to strings with `array_to_string ()`.

### 1.5.7
* `deprecationWarning()` global function has been moved to `src/deprecation_functions.php`.
  This file is not loaded automatically by composer, to avoid conflicts with other
    packages that declare the same function (eg CakePHP).

### 1.5.6
* `which()` now uses `ExecutableFinder`, however provided by `symfony/process`.

### 1.5.5
* `which()` now uses `symfony/process` and throws an exception if the binary
    cannot be found.

### 1.5.4
* fixed for `phpunit` 9.5.10;
* little fix for `php` 8.0;
* migration to github actions.

### 1.5.3
* improved `Exceptionist::__callStatic()` magic method when a php function is
    called and expects a single argument.

### 1.5.2
* added `TestTrait::assertIsMock()` and `TestTrait::expectAssertionFailed()`;
* some little fixes.

### 1.5.1
* fixed a bug when a "magic" method of the `Exceptionist` (`__callStatic()`)
    was called with a value of `null` or` false`. Improved error messages
    generated by this method;
* `Exceptionist::isTrue()` no longer accepts an exception instead of the message
    as the second parameter;
* extensive improvement of function descriptions and tags. The level of `phpstan`
    has been raised.

### 1.5.0
* the `Exceptionist` can now set file and line that throws the exception. All
    exception classes provided by `php-tools` now extend the `ErrorException`;
* all filesystem global function have been removed, use `Filesystem` class instead;
* `TestTrait::assertFilePerms()` has been removed. Use instead
    `assertFileIsReadable()`/`assertFileIsWritable()`/
    `assertDirectoryIsReadable()`/`assertDirectoryIsWritable()`;
* the `ReflectionTrait` has been moved on `Tools\TestSuite` namespace;
* changed the order of arguments for the `TestTrait::assertException()` method.
    The callable is now the first argument;
* added alias for old 'Tools\ReflectionTrait' trait. This allows for a smooth
    version upgrade;
* all `_or_fail()` methods have been removed.

## 1.4 branch
### 1.4.8
* ready for PHP 8;
* some little fixes.

### 1.4.7
* added `array_has_only_numeric_keys()` function;
* added `Filesystem::instance()` static method;
* `get_hostname_from_url()` function returns an empty string instead of `null`;
* `Filesystem::createTmpFile()` now throws a `RuntimeException` on failure;
* `Exceptionist::isWritable()` now throws the `NotWritableException` correctly;
* extensive improvement of function descriptions and tags.

### 1.4.6
* added `Exceptionist::isInstanceOf()` method and the `ObjectWrongInstanceException`;
* added `phpstan`, so fixed some code and descriptions.

### 1.4.5
* added `concatenate()`, `makePathAbsolute()` and `normalizePath()` methods for
    `Filesystem`.

### 1.4.4
* added `Filesystem` class, all filesystem global function are now deprecated;
* added `Filesystem::getRoot()` method;
* added `\Tools\TestSuite\BackwardCompatibilityTrait` to provide methods to
    achieve PHPUnit backward compatibility;
* `TestTrait::assertFilePerms()` is deprecated. Use instead
    `assertFileIsReadable()`/`assertFileIsWritable()`/
    `assertDirectoryIsReadable()`/`assertDirectoryIsWritable()`;
* `fileperms_as_octal()` and `fileperms_to_string()` global functions are now
    deprecated and will be removed in a future release;
* some functions have been moved to `array_functions.php` file.

### 1.4.3
* added `array_unique_recursive()` global function;
* added `Exceptionist::methodExists()` method;
* improved the failure message for the `Exceptionist::objectPropertyExists()` method;
* fixed compatibility with PHP 7.4 and phpunit 9.

### 1.4.2
* added `uncamelcase()` global function.

### 1.4.1
* added `slug()` global function;
* added `Exceptionist` class. All `_or_fail()` methods are now deprecated;
* added `EventList::toArray()` method;
* by default, `create_file()`, `dir_tree()` `is_writable_resursive()` and
    `unlink_recursive()` functions can throw an exception. Added the third
    `$ignoreErrors` parameter, which allows to ignore any errors and return a
    default value;
* `rmdir_recursive()` returns a boolean;
* fixed bug for `dir_tree()` function on the filesystem root.

### 1.4.0
* requires at least PHP 7.2.5;
* added all classes for event management and the `EventAssertTrait` to assert
    whether events were fired or not;
* added `string_contains()` global function;
* fixed bug for `rtr()` global function;
* fixed bug for `objects_map()` global function. It now works if the class
    provides the `__call()` method.

## 1.3 branch
### 1.3.4
* updated `sniffer-ruleset.xml`.

### 1.3.3
* added `is_localhost()` global function;
* some functions have been moved to `network_functions.php` file.

### 1.3.2
* fixed bug for `Entity::set()` method. It can throw an exception for empty
    property.

### 1.3.1
* compatibility with Symfony Components `5.x`.

### 1.3.0
* `is_absolute()` global function had been deprecated and has now been removed;
* `FileException` and `InvalidValueException` have been moved in the `Tools`
    namespace, and are no longer in `Tools\Exception`;
* some default exception messages have been simplified;
* updated for `php` 7.1 and `phpunit` 8.

## 1.2 branch
### 1.2.16
* little fixes.

### 1.2.15
* fixed little bug for `debug()` global function;
* added `sniffer-ruleset.xml` and fixed `phpcs.xml.dist` file.

### 1.2.14
* `debug()`/`dd()`/`dump()` functions now use a template;
* APIs are now generated by `phpDocumentor` and no longer by` apigen`.

### 1.2.13
* `assertException()` does not catch exceptions thrown from phpunit errors;
* fixed bug for `key_exists_or_fail()` and `property_exists_or_fail()` functions.

### 1.2.12
* all `or_fail()` functions return the value that has been checked;
* added `FileException` and `InvalidValueException` abstract exceptions;
* fixed little bug for `debug()` and `dd()` functions: it checks if the `dump()`
    function exists;
* `is_absolute()` function is deprecated. Use `Filesystem::isAbsolutePath()` instead;
* some functions have been moved to `filesystem_functions.php` file.

### 1.2.11
* added `debug()` and `dd()` global functions;
* all `_or_fail()` functions can take a string or a `Throwable` instance as
    `$exception` parameter;
* `FileNotExistsException`, `NotDirectoryException`, `NotReadableException` and
    `NotWritableException` exceptions can take a `$path` parameter and implement
    a `getFilePath()` method;
* `KeyNotExistsException` can take a `$key` parameter and implements a
    `getKeyName()` method;
* `NotInArrayException` and `NotPositiveException` can take a `$value` parameter
    and implement a `getValue()` method;
* `PropertyNotExistsException` can take a `$$property` parameter and implements
    a `getPropertyName()` method.

### 1.2.10
* added `TestTrait::skipIf()` method.

### 1.2.9
* fixed little bug for `dir_tree()` global function;
* added tests for lower dependencies.

### 1.2.8
* `can_be_string()` renamed as `is_stringable()`. `can_be_string()` is
    deprecated and will be removed in a later release;
* added `is_absolute()` global function;
* uses `symfony/dom-crawler` for `BodyParser` class;
* uses `symfony/filesystem` and `symfony/finder` for some global functions.

### 1.2.7
* `getProperty()`, `getProperties()` and `invokeMethod()` methods from the
    `ReflectionTrait` can now take a class name as string, rather than just an
    instantiated object;
* `getMethodInstance()` and `getPropertyInstance()` methods from the
    `ReflectionTrait` renamed as `_getMethodInstance()` and `_getPropertyInstance()`.

### 1.2.6
* fixed bug, the `TestCase::tearDown()` method removes temporary files only if
    these are not temporary system files;
* added `add_slash_term()` global function;
* added `in_array_or_fail()` global function and the `NotInArrayException`.

### 1.2.5
* added `can_be_string()` global function;
* added `is_positive_or_fail()` global function and the `NotPositiveException`.

### 1.2.4
* fixed bug for `is_url()` global function, it correctly recognizes the url with
    brackets.

### 1.2.3
* fixed bug for `BodyParser::extractLinks()`, urls are returned without the
    trailing slash.

### 1.2.2
* fixed bug for `url_to_absolute()` global function.

### 1.2.1
* fixed bug for `is_url()` global function;
* fixed bug for `url_to_absolute()` global function.

### 1.2.0
* added `fileperms_as_octal()` and `fileperms_to_string()` global functions;
* arguments of the `assertFileMime()` assert method have been reversed
    (`$expectedMime, $filename, $message = ''`). If `$expectedMime` is an array,
    it asserts that the filename has at least one of those values;
* arguments of the `assertFilePerms()` assert method have been reverse
    (`$expectedPerms, $filename, $message = ''`);
* arguments of the `assertImageSize()` assert method have been reverse
    (`$expectedWidth, $expectedHeight, $filename, $message = ''`);
* for the `assertFileExtension()` assert method, if `$expectedMime` is an array,
    it asserts that the filename has at least one of those values;
* removed deprecated `ends_with()` and `starts_with()` global functions, use
    instead `string_ends_with()` and `string_starts_with()`;
* removed deprecated `first_key()`, `first_value()`, `first_value_recursive()`,
    `last_key()`, `last_value()` and `last_value_recursive()` global functions,
    use instead `array_key_first()`, `array_value_first()`,
    `array_value_first_recursive()`, `array_key_last()`, `array_value_last()` and
    `array_value_last_recursive()`;
* removed deprecated `is_win()` global function, use instead `IS_WIN` constant;
* removed deprecated `assertContainsInstanceOf()`, `assertFileExists()` and
    `assertFileNotExists()` assert methods;
* `assertFileExtension()`, `assertFileMime()` and `assertFilePerms()` methods now
    take a string as first `$filename` argument, so they no longer take an array.
    If you want to check an array of filename, use the `array_map()` function;
* removed deprecated `TestCaseTrait`. Use `TestTrait` instead;
* removed deprecated `Apache` class;
* removed all deprecated "safe" functions;
* updated for phpunit 7.

## 1.1 branch
### 1.1.16
* added `url_to_absolute()` global function;
* removed `BodyParser::_turnUrlAsAbsolute()` and `BodyParser::isHtml()` methods.

### 1.1.15
* added `Entity` class.

### 1.1.14
* added `property_exists_or_fail()` global function and the
    `PropertyNotExistsException` exception class.

### 1.1.13
* added `array_clean()` global function;
* added `is_html()` global function. This also provides the `assertIsHtml()`
    assertion method for the `TestTrait`;
* `Apache` class is now deprecated and will be removed in a later version.

### 1.1.12
* added `is_iterable()` global function;
* `assertIsArray()`, `assertIsInt()`, `assertIsObject()` and `assertIsString()`
    methods of `TestTrait` are now provided by `__call()` and `__callStatic()`
    methods. These also provide some other "assertIs" methods (see API);
* `assertFileExtension()`, `assertFileMime()`, `assertFilePerms()` methods are
    deprecated when used with an array of filename and in a later version they
    will take a string as argument. `assertFileExists()` and `assertFileNotExists()`
    methods are deprecated and will be removed in a later version, because the
    same methods are provided by PHPUnit and take a string as argument;
* fixed bug for `assertException()` assert method, it checks if the
    `$expectedException` is a subclass of `Exception`;
* fixed bug for `assertIsArrayNotEmpty()` assert method, it executes
    `array_filter()` on the array to verify that it does not contain a value
    that is nevertheless equal to empty;
* `first_key()`, `last_key()`, `first_value()`, `first_value_recursive()`,
    `last_value()` and `last_value_recursive()` functions are now deprecated and
    will be removed in a later version. Use instead `array_key_first()`,
    `array_key_last()`, `array_value_first()`, `array_value_last(),
    `array_value_first_recursive()` and `array_value_last_recursive()`;
* `ends_with()` and `starts_with()` functions are now deprecated and will be removed
    in a later version. Use instead `string_ends_with()` and `string_starts_with()`.

### 1.1.11
* added `TestCase` class;
* added `objects_map()` global function;
* `assertFileExtension()` and `assertFileMime()` assert methods can take string
    or an array or a `Traversable` of files;
* all `ReflectionTrait` methods are now protected. The `setProperty()` method
    now returns the old value or `null`;
* `TestCaseTrait` is now deprecated and will be removed in a later version. Use
    `TestTrait` instead. The `createSomeFiles()` method has been removed and now
    is a global function only for tests;
* fixed bug for `assertArrayKeysEqual()` and `assertSameMethods()` assert methods,
    the values are sorted alphabetically before being compared;
* fixed bug for `assertFilePerms()`. Now it works correctly and take permission
    values as string or octal value;
* fixed bug for `first_key()`, `first_value()`, `last_key()` and `last_value()`
    function: they return `null` with empty array;
* fixed bug for `is_url()` function with no-string values;
* `is_win()` method is now deprecated and will be
    removed in a later version; Use the `IS_WIN` constant instead;
* `assertContainsInstanceOf()` method is now deprecated and will be removed in a
    later version. Use `assertContainsOnlyInstancesOf()` instead';
* all "safe" methods are now deprecated and will be removed in a later version.

### 1.1.10
* added `first_key()`, `first_value_recursive()`, `last_key()` and
    `last_value_recursive()` global functions;
* added `key_exists_or_fail()` function.

### 1.1.9
* `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` functions now have `$message` as second argument and
    `$exception` as third argument.

### 1.1.8
* `create_tmp_file()` and `safe_create_tmp_file()` methods now accept a
    directory as a second argument and a prefix as the third argument.

### 1.1.7
* added `is_true_or_fail()` and `deprecationWarning()` global functions;
* added `create_file()` and `create_tmp_file()` global functions and
    `safe_create_file()` and `safe_create_tmp_file()` safe functions;
* added `assertException()` assert method;
* added some exception classes;
* `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` functions now throw specific exception classes.

### 1.1.6
* added `ends_with()` and `starts_with()` global functions.

### 1.1.5
* fixed `clean_url()` function, added third parameter `$removeTrailingSlash`.

### 1.1.4
* added `ReflectionTrait::getProperties()` method.

### 1.1.3
* added `BodyParser` class.

### 1.1.2
* added `$removeWWW` optional parameter to `clean_url()` global function.

### 1.1.1
* added `first_value()` and `last_value()` global functions.

### 1.1.0
* added `assertContainsInstanceOf()` assertion method. Removed
    `assertInstanceOf()` (you can use the original method).

## 1.0 branch
### 1.0.10
* added `FileArray` class.

### 1.0.9
* added `assertFilePerms()` assertion method.

### 1.0.8
* fixed bug for `unlink_recursive()` method with symlinks under Windows;
* `unlink_recursive()` returns void.

### 1.0.7
* added `dir_tree()` and `is_writable_resursive()` global functions;
* added `unlink_recursive()` and `safe_unlink_recursive()` functions.

### 1.0.6
* added `rmdir_recursive()` and `safe_rmdir_recursive()` functions;
* added `file_exists_or_fail()`, `is_dir_or_fail()`, `is_readable_or_fail()` and
    `is_writable_or_fail()` "or fail" functions;
* added `assertIsArrayNotEmpty()` assertion method.

### 1.0.5
* added `safe_copy()` and `safe_unserialized()` safe aliases.

### 1.0.4
* added `safe_mkdir()`, `safe_rmdir()`, `safe_symlink()` and `safe_unlink()`
    safe aliases;
* added `is_external_url()` global function;
* added `assertIsInt()` assertion method.

### 1.0.3
* added `clean_url()` and `is_slash_term()` global functions.

### 1.0.2
* added `Tools\Apache` class with some methods;
* added `Tools\TestSuite\TestCaseTrait` with some assertion methods;
* added `get_class_short_name()` and `get_hostname_from_url()` global functions;
* fixed `rtr()` global function. It can also use the `ROOT` environment variable.

### 1.0.1
* added `get_child_methods()` global function.

### 1.0.0
* first release.
