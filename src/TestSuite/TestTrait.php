<?php
declare(strict_types=1);

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
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;
use Tools\Filesystem;

/**
 * A trait that provides some assertion methods.
 * @method static void assertIsArray($var, ?string $message = '') Asserts that `$var` is an array
 * @method static void assertIsBool($var, ?string $message = '') Asserts that `$var` is a boolean
 * @method static void assertIsCallable($var, ?string $message = '') Asserts that `$var` is a callable
 * @method static void assertIsFloat($var, ?string $message = '') Asserts that `$var` is a float
 * @method static void assertIsHtml($var, ?string $message = '') Asserts that `$var` is an html string
 * @method static void assertIsInt($var, ?string $message = '') Asserts that `$var` is an int
 * @method static void assertIsIterable($var, ?string $message = '') Asserts that `$var` is iterable, i.e. that it is an array or an object implementing `Traversable`
 * @method static void assertIsJson($var, ?string $message = '') Asserts that `$var` is a json string
 * @method static void assertIsObject($var, ?string $message = '') Asserts that `$var` is an object
 * @method static void assertIsPositive($var, ?string $message = '') Asserts that `$var` is a positive number
 * @method static void assertIsResource($var, ?string $message = '') Asserts that `$var` is a resource
 * @method static void assertIsString($var, ?string $message = '') Asserts that `$var` is a string
 * @method static void assertIsUrl($var, ?string $message = '') Asserts that `$var` is an url
 */
trait TestTrait
{
    /**
     * Magic `__call()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @see __callStatic()
     * @since 1.1.12
     */
    public function __call(string $name, array $arguments): void
    {
        self::__callStatic($name, $arguments);
    }

    /**
     * Magic `__callStatic()` method.
     *
     * Provides some "assertIs" methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments): void
    {
        if (string_starts_with($name, 'assertIs')) {
            $count = count($arguments);
            if (!$count || $count > 2) {
                throw new BadMethodCallException(sprintf(
                    'Method %s::%s() expects at least 1 argument, maximum 2, %d passed',
                    __CLASS__,
                    $name,
                    $count
                ));
            }

            $function = sprintf('is_%s', strtolower(substr($name, 8)));
            if (is_callable($function)) {
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
     * @param array<int, string|int> $expectedKeys Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertArrayKeysEqual(array $expectedKeys, array $array, string $message = ''): void
    {
        $keys = array_keys($array);
        sort($keys);
        sort($expectedKeys);
        self::assertEquals($expectedKeys, $keys, $message);
    }

    /**
     * Asserts that a callable throws an exception
     * @param callable $function A callable you want to test and that should
     *  raise the expected exception
     * @param string $expectedException Expected exception
     * @param string $expectedMessage The expected message
     * @return void
     * @since 1.1.7
     */
    protected static function assertException(callable $function, string $expectedException = Exception::class, string $expectedMessage = ''): void
    {
        if (!is_subclass_of($expectedException, Throwable::class)) {
            self::fail(sprintf('Class `%s` does not exist or is not an exception', $expectedException));
        }

        try {
            call_user_func($function);
        } catch (Exception $e) {
            parent::assertInstanceof(
                $expectedException,
                $e,
                sprintf('Expected exception `%s`, unexpected type `%s`', $expectedException, get_class($e))
            );

            if ($expectedMessage) {
                parent::assertNotEmpty(
                    $e->getMessage(),
                    sprintf('Expected message exception `%s`, but no message for the exception', $expectedMessage)
                );
                parent::assertEquals($expectedMessage, $e->getMessage(), sprintf(
                    'Expected message exception `%s`, unexpected message `%s`',
                    $expectedMessage,
                    $e->getMessage()
                ));
            }
        }

        if (!isset($e)) {
            self::fail(sprintf('Expected exception `%s`, but no exception throw', $expectedException));
        }
    }

    /**
     * Asserts that a filename has the `$expectedExtension`.
     *
     * If `$expectedExtension` is an array, asserts that the filename has at
     *  least one of those values.
     *
     * It is not necessary it actually exists.
     * The assertion is case-insensitive (eg, for `PIC.JPG`, the expected
     *  extension is `jpg`).
     * @param string|array<string> $expectedExtension Expected extension
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileExtension($expectedExtension, string $filename, string $message = ''): void
    {
        self::assertContains(Filesystem::instance()->getExtension($filename), (array)$expectedExtension, $message);
    }

    /**
     * Asserts that a filename have a MIME content type.
     *
     * If `$expectedMime` is an array, asserts that the filename has at
     *  least one of those values.
     * @param string|array<string> $expectedMime MIME content type
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertFileMime($expectedMime, string $filename, string $message = ''): void
    {
        self::assertFileExists($filename);
        self::assertContains(mime_content_type($filename), (array)$expectedMime, $message);
    }

    /**
     * Asserts that an image file has `$expectedWidth` and `$expectedHeight`
     * @param int $expectedWidth Expected image width
     * @param int $expectedHeight Expected mage height
     * @param string $filename Path to the tested file
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertImageSize(int $expectedWidth, int $expectedHeight, string $filename, string $message = ''): void
    {
        self::assertFileExists($filename);
        [$actualWidth, $actualHeight] = getimagesize($filename);
        self::assertEquals($actualWidth, $expectedWidth, $message);
        self::assertEquals($actualHeight, $expectedHeight, $message);
    }

    /**
     * Asserts that `$var` is an array and is not empty
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.0.6
     */
    protected static function assertIsArrayNotEmpty($var, string $message = ''): void
    {
        self::assertIsArray($var, $message);
        self::assertNotEmpty(array_filter($var), $message);
    }

    /**
     * Asserts that an object is a mock (instance of `MockObject`)
     * @param object $object Object
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     * @since 1.5.2
     */
    protected static function assertIsMock(object $object, string $message = ''): void
    {
        $message = $message ?: 'Failed asserting that a `' . get_class($object) . '` object is a mock';
        self::assertInstanceOf(MockObject::class, $object, $message);
    }

    /**
     * Asserts that the object properties are equal to `$expectedProperties`
     * @param array<string> $expectedProperties Expected properties
     * @param object $object Object you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected function assertObjectPropertiesEqual(array $expectedProperties, $object, string $message = ''): void
    {
        self::assertArrayKeysEqual($expectedProperties, (array)$object, $message);
    }

    /**
     * Asserts that `$firstClass` and `$secondClass` have the same methods
     * @param string|object $firstClass First class as string or object
     * @param string|object $secondClass Second class as string or object
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertSameMethods($firstClass, $secondClass, string $message = ''): void
    {
        [$firstClassMethods, $secondClassMethods] = [get_class_methods($firstClass), get_class_methods($secondClass)];
        sort($firstClassMethods);
        sort($secondClassMethods);
        self::assertEquals($firstClassMethods, $secondClassMethods, $message);
    }

    /**
     * Expects the next assertion to fail. Optionally it can verify that the
     *  exception message is also the same.
     *
     * Convenient wrapper for `expectException()` and `expectExceptionMessage()`
     * @param string $withMessage
     * @return void
     * @since 1.5.2
     */
    public function expectAssertionFailed(string $withMessage = ''): void
    {
        $this->expectException(AssertionFailedError::class);
        if ($withMessage) {
            $this->expectExceptionMessage($withMessage);
        }
    }

    /**
     * Skips the test if the condition is `true`
     * @param bool $shouldSkip Whether or not the test should be skipped
     * @param string $message The message to display
     * @return bool
     */
    public function skipIf(bool $shouldSkip, string $message = ''): bool
    {
        if ($shouldSkip) {
            $this->markTestSkipped($message);
        }

        return $shouldSkip;
    }
}
