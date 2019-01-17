<?php
/**
 * This file is part of php-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       1.0.2
 */
namespace Tools\TestSuite;

use BadMethodCallException;
use Exception;
use PHPUnit\Framework\Constraint\IsType;
use Traversable;

/**
 * A trait that provides some assertion methods
 * @method void assertIsArray($var, $message = '') Asserts that `$var` is an array
 * @method void assertIsBool($var, $message = '') Asserts that `$var` is a boolean
 * @method void assertIsCallable($var, $message = '') Asserts that `$var` is a callable
 * @method void assertIsFloat($var, $message = '') Asserts that `$var` is a float
 * @method void assertIsHtml($var, $message = '') Asserts that `$var` is an html string
 * @method void assertIsInt($var, $message = '') Asserts that `$var` is an int
 * @method void assertIsIterable($var, $message = '') Asserts that `$var` is iterable, i.e. that it is an array or an object implementing `Traversable`
 * @method void assertIsJson($var, $message = '') Asserts that `$var` is a json string
 * @method void assertIsObject($var, $message = '') Asserts that `$var` is an object
 * @method void assertIsPositive($var, $message = '') Asserts that `$var` is a positive number
 * @method void assertIsResource($var, $message = '') Asserts that `$var` is a resource
 * @method void assertIsString($var, $message = '') Asserts that `$var` is a string
 * @method void assertIsUrl($var, $message = '') Asserts that `$var` is an url
 */
trait TestTrait
{
    /**
     * Magic `__call()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param mixed $arguments Arguments
     * @return void
     * @see __callStatic()
     * @since 1.1.12
     */
    public function __call($name, $arguments)
    {
        self::__callStatic($name, $arguments);
    }

    /**
     * Magic `__callStatic()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param mixed $arguments Arguments
     * @return void
     * @since 1.1.12
     * @throws BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        if (string_starts_with($name, 'assertIs')) {
            $count = count($arguments);
            if (!$count || $count > 2) {
                throw new BadMethodCallException(sprintf('Method %s::%s() expects at least 1 argument, maximum 2, %d passed', __CLASS__, $name, $count));
            }

            $type = substr($name, 8);

            if (defined(sprintf('%s::TYPE_%s', IsType::class, strtoupper($type)))) {
                $arguments = array_merge([lcfirst($type)], $arguments);
                call_user_func_array([__CLASS__, 'assertInternalType'], $arguments);

                return;
            }

            $function = sprintf('is_%s', strtolower($type));
            if (function_exists($function)) {
                $var = array_shift($arguments);
                $arguments = array_merge([$function($var)], $arguments);
                call_user_func_array([__CLASS__, 'assertTrue'], $arguments);

                return;
            }
        }

        throw new BadMethodCallException(sprintf('Method %s::%s() does not exist', __CLASS__, $name));
    }

    /**
     * Asserts that the array keys are equal to `$expectedKeys`
     * @param array $expectedKeys Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertArrayKeysEqual(array $expectedKeys, array $array, $message = '')
    {
        self::assertIsArray($array);
        $keys = array_keys($array);
        sort($keys);
        sort($expectedKeys);
        self::assertEquals($expectedKeys, $keys, $message);
    }

    /**
     * Asserts that an array or an instance of `Traversable` contains objects
     *  that are instances of `$expectedInstance`
     * @deprecated 1.1.11 Use `assertContainsOnlyInstancesOf()` instead
     * @param string $expectedInstance Expected instance
     * @param array|Traversable $value Values
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.1.0
     */
    protected static function assertContainsInstanceOf($expectedInstance, $value, $message = '')
    {
        deprecationWarning('The `assertContainsInstanceOf()` method is deprecated and will be removed in a later version. Use `assertContainsOnlyInstancesOf()` instead');

        parent::assertContainsOnlyInstancesOf($expectedInstance, $value, $message);
    }

    /**
     * Asserts that a callable throws an exception
     * @param string $expectedException Expected exception
     * @param callable $function A callable you want to test and that should
     *  raise the expected exception
     * @param string|null $expectedMessage The expected message or `null`
     * @return void
     * @since 1.1.7
     */
    protected static function assertException($expectedException, callable $function, $expectedMessage = null)
    {
        if ($expectedException !== Exception::class && !is_subclass_of($expectedException, 'Exception')) {
            self::fail(sprintf('Class `%s` does not exist or is not an %s instance', $expectedException, Exception::class));
        }

        $e = false;
        try {
            call_user_func($function);
        } catch (Exception $e) {
            parent::assertInstanceof($expectedException, $e, sprintf('Expected exception `%s`, unexpected type `%s`', $expectedException, get_class($e)));

            if ($expectedMessage) {
                parent::assertNotEmpty($e->getMessage(), sprintf('Expected message exception `%s`, but no message for the exception', $expectedMessage));
                parent::assertEquals($expectedMessage, $e->getMessage(), sprintf('Expected message exception `%s`, unexpected message `%s`', $expectedMessage, $e->getMessage()));
            }
        } finally {
            parent::assertNotFalse($e, sprintf('Expected exception `%s`, but no exception throw', $expectedException));
        }
    }

    /**
     * Asserts that one or more filenames exist.
     *
     * Unlike the original method, this method can take an array or a
     *  `Traversable` instance.
     * @deprecated 1.1.12 The same method is provided by PHPUnit and takes a string as argument
     * @param string|array|Traversable $filename Filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileExists($filename, $message = '')
    {
        if (!is_string($filename)) {
            deprecationWarning('The `assertFileExists()` method is deprecated and will be removed in a later version. The same method is provided by PHPUnit and takes a string as argument. To check an array of filename, use the `array_map()` function');
        }

        foreach (is_string($filename) ? [$filename] : $filename as $filename) {
            parent::assertFileExists($filename, $message);
        }
    }

    /**
     * Asserts that one or more filenames have the `$expectedExtension`.
     *
     * It is not necessary they actually exist.
     * The assertion is case-insensitive (eg, for `PIC.JPG`, the expected
     *  extension is `jpg`).
     * @param string $expectedExtension Expected extension
     * @param string|array|Traversable $filename Filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileExtension($expectedExtension, $filename, $message = '')
    {
        if (!is_string($filename)) {
            deprecationWarning('The `assertFileExtension()` method is deprecated when used with an array of filename. To check an array of filename, use the `array_map()` function');
        }

        foreach (is_string($filename) ? [$filename] : $filename as $filename) {
            self::assertEquals($expectedExtension, get_extension($filename), $message);
        }
    }

    /**
     * Asserts that one or more filenames have a MIME content type
     * @param string|array|Traversable $filename Filenames
     * @param string $expectedMime MIME content type
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @todo $filename and $expectedMime arguments should be reversed
     */
    protected static function assertFileMime($filename, $expectedMime, $message = '')
    {
        if (!is_string($filename)) {
            deprecationWarning('The `assertFileMime()` method is deprecated when used with an array of filename. To check an array of filename, use the `array_map()` function');
        }

        foreach (is_string($filename) ? [$filename] : $filename as $filename) {
            self::assertFileExists($filename);
            self::assertEquals($expectedMime, mime_content_type($filename), $message);
        }
    }

    /**
     * Asserts that one or more filenames do not exist.
     *
     * Unlike the original method, this method can take an array or a
     *  `Traversable` instance.
     * @deprecated 1.1.12 The same method is provided by PHPUnit and takes a string as argument
     * @param string|array|Traversable $filename Filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileNotExists($filename, $message = '')
    {
        if (!is_string($filename)) {
            deprecationWarning('The `assertFileNotExists()` method is deprecated and will be removed in a later version. The same method is provided by PHPUnit and takes a string as argument. To check an array of filename, use the `array_map()` function');
        }

        foreach (is_string($filename) ? [$filename] : $filename as $filename) {
            parent::assertFileNotExists($filename, $message);
        }
    }

    /**
     * Asserts that one or more filenames have some file permissions.
     *
     * If only one permission value is passed, asserts that all files have that
     *  value. If more permission values are passed, asserts that all files have
     *  at least one of those values.
     * @param string|array|Traversable $filename Filenames
     * @param string|int|array $expectedPerms Expected permission values as a
     *  four-chars string or octal value
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.9
     */
    protected static function assertFilePerms($filename, $expectedPerms, $message = '')
    {
        if (!is_string($filename)) {
            deprecationWarning('The `assertFilePerms()` method is deprecated when used with an array of filename. To check an array of filename, use the `array_map()` function');
        }

        $expectedPerms = array_map(function ($perm) {
            return is_string($perm) ? $perm : sprintf("%04o", $perm);
        }, (array)$expectedPerms);

        foreach (is_string($filename) ? [$filename] : $filename as $filename) {
            parent::assertFileExists($filename);
            self::assertContains(substr(sprintf('%o', fileperms($filename)), -4), $expectedPerms, $message);
        }
    }

    /**
     * Asserts that an image file has `$expectedWidth` and `$expectedHeight`
     * @param string $filename Path to the tested file
     * @param int $expectedWidth Expected image width
     * @param int $expectedHeight Expected mage height
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertImageSize($filename, $expectedWidth, $expectedHeight, $message = '')
    {
        self::assertFileExists($filename, $message);
        list($width, $height) = getimagesize($filename);
        self::assertEquals($width, $expectedWidth);
        self::assertEquals($height, $expectedHeight);
    }

    /**
     * Asserts that `$var` is an array and is not empty
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.6
     * @todo array_filter?
     */
    protected static function assertIsArrayNotEmpty($var, $message = '')
    {
        self::assertIsArray($var, $message);
        self::assertNotEmpty(array_filter($var), $message);
    }

    /**
     * Asserts that the object properties are equal to `$expectedProperties`
     * @param array $expectedProperties Expected properties
     * @param object $object Object you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected function assertObjectPropertiesEqual(array $expectedProperties, $object, $message = '')
    {
        self::assertIsObject($object);
        self::assertArrayKeysEqual($expectedProperties, (array)$object, $message);
    }

    /**
     * Asserts that `$firstClass` and `$secondClass` have the same methods
     * @param mixed $firstClass First class as string or object
     * @param mixed $secondClass Second class as string or object
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertSameMethods($firstClass, $secondClass, $message = '')
    {
        list($firstClass, $secondClass) = [get_class_methods($firstClass), get_class_methods($secondClass)];
        sort($firstClass);
        sort($secondClass);
        self::assertEquals($firstClass, $secondClass, $message);
    }
}
