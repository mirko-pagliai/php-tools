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
        $filenames = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filenames as $filename) {
            parent::assertFileExists($filename, $message);
        }
    }

    /**
     * Asserts that a filename has the `$expectedExtension`.
     *
     * It is not necessary for the file to actually exist.
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
        $filenames = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filenames as $filename) {
            self::assertEquals($expectedExtension, get_extension($filename), $message);
        }
    }

    /**
     * Asserts that a file has a MIME content type
     * @param string|array|Traversable $filename Filenames
     * @param string $expectedMime MIME content type
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @todo $filename and $expectedMime arguments should be reversed
     */
    protected static function assertFileMime($filename, $expectedMime, $message = '')
    {
        $filenames = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filenames as $filename) {
            self::assertFileExists($filename);
            self::assertEquals($expectedMime, mime_content_type($filename), $message);
        }
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
        $filenames = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];

        foreach ($filenames as $filename) {
            parent::assertFileNotExists($filename, $message);
        }
    }

    /**
     * Asserts that one or more filename has some file permissions.
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
    public static function assertFilePerms($filename, $expectedPerms, $message = '')
    {
        $filenames = is_array($filename) || $filename instanceof Traversable ? $filename : [$filename];
        $expectedPerms = is_array($expectedPerms) ? $expectedPerms : [$expectedPerms];

        $expectedPerms = array_map(function ($perm) {
            return is_string($perm) ? $perm : sprintf("%04o", $perm);
        }, $expectedPerms);

        foreach ($filenames as $filename) {
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
     * @todo array_filter?
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

        static::assertEquals($firstClass, $secondClass, $message);
    }
}
