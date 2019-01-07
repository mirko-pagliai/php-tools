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

use Exception;
use PHPUnit\Framework\Error\Deprecated;
use Traversable;

/**
 * A trait that provides some assertion methods
 */
trait TestTrait
{
    /**
     * Asserts that the array keys are equal to `$expectedKeys`
     * @param array $expectedKeys Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertArrayKeysEqual($expectedKeys, $array, $message = '')
    {
        self::assertIsArray($array);
        self::assertEquals($expectedKeys, array_keys($array), $message);
    }

    /**
     * Asserts that an array or an instance of `Traversable` contains objects
     *  that are instances of `$expectedInstance`
     * @param string $expectedInstance Expected instance
     * @param array|Traversable $value Values
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.1.0
     */
    protected static function assertContainsInstanceOf($expectedInstance, $value, $message = '')
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            self::fail('The value is not an array or an instance of Traversable');
        }

        foreach ($value as $object) {
            parent::assertInstanceOf($expectedInstance, $object, $message);
        }
    }

    /**
     * Asserts that the object properties are equal to `$expectedProperties`
     * @param array $expectedProperties Expected properties
     * @param array $object Ojbect to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected function assertObjectPropertiesEqual($expectedProperties, $object, $message = '')
    {
        self::assertIsObject($object);
        self::assertArrayKeysEqual($expectedProperties, (array)$object, $message);
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
     * Asserts that one or more filename exist.
     *
     * Unlike the original method, this method can take an array or a
     *  `Traversable` instance.
     * @param string|array|Traversable $filename Filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileExists($filename, $message = '')
    {
        $filename = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filename as $var) {
            parent::assertFileExists($var, $message);
        }
    }

    /**
     * Asserts that a filename has the `$expectedExtension`
     * @param string $expectedExtension Expected extension
     * @param string $filename Path to the tested file
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileExtension($expectedExtension, $filename, $message = '')
    {
        self::assertEquals($expectedExtension, get_extension($filename), $message);
    }

    /**
     * Asserts that a file has a MIME content type
     * @param string $filename Path to the tested file
     * @param string $expectedMime MIME content type, like `text/plain` or `application/octet-stream`
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileMime($filename, $expectedMime, $message = '')
    {
        self::assertFileExists($filename, $message);
        self::assertEquals($expectedMime, mime_content_type($filename), $message);
    }

    /**
     * Asserts that one or more filename do not exist.
     *
     * Unlike the original method, this method can take an array or a
     *  `Traversable` instance.
     * @param string|array|Traversable $filename Filenames
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileNotExists($filename, $message = '')
    {
        $filename = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filename as $var) {
            parent::assertFileNotExists($var, $message);
        }
    }

    /**
     * Asserts that a filename has file permissions
     * @param mixed $filename Filename or filenames
     * @param string $expectedPerms Expected permissions as a four-chars string
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.9
     */
    public static function assertFilePerms($filename, $expectedPerms, $message = '')
    {
        $filename = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filename as $var) {
            parent::assertFileExists($var);
            self::assertContains(substr(sprintf('%o', fileperms($var)), -4), (array)$expectedPerms, $message);
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
     * Asserts that `$var` is an array
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertIsArray($var, $message = '')
    {
        self::assertTrue(is_array($var), $message);
    }

    /**
     * Asserts that `$var` is an array and is not empty
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.6
     */
    protected static function assertIsArrayNotEmpty($var, $message = '')
    {
        self::assertIsArray($var, $message);
        self::assertNotEmpty($var, $message);
    }

    /**
     * Asserts that `$callable` function is deprecated
     * @param callable $callable A callable you want to test and that is marked
     *  as deprecated
     * @param string|null $expectedMessage The expected message or `null`
     * @return void
     * @since 1.1.11
     */
    protected static function assertIsDeprecated(callable $callable, $expectedMessage = null)
    {
        $deprecatedText = ' - [internal], line: ??
 You can disable deprecation warnings by setting `error_reporting()` to `E_ALL & ~E_USER_DEPRECATED`.';

        if ($expectedMessage && !ends_with($expectedMessage, $deprecatedText)) {
            $expectedMessage .= $deprecatedText;
        }

        self::assertException(Deprecated::class, $callable, $expectedMessage);
    }

    /**
     * Asserts that `$var` is an integer
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.4
     */
    protected static function assertIsInt($var, $message = '')
    {
        self::assertTrue(is_int($var), $message);
    }

    /**
     * Asserts that `$var` is an object
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertIsObject($var, $message = '')
    {
        self::assertTrue(is_object($var), $message);
    }

    /**
     * Asserts that `$var` is a string
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertIsString($var, $message = '')
    {
        self::assertTrue(is_string($var), $message);
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
        static::assertEquals(get_class_methods($firstClass), get_class_methods($secondClass), $message);
    }
}
