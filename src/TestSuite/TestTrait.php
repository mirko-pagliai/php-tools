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
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tools\Filesystem;

/**
 * A trait that provides some assertion methods.
 * @method static void assertIsArray(mixed $var, ?string $message = '') Asserts that `$var` is an array
 * @method static void assertIsBool(mixed $var, ?string $message = '') Asserts that `$var` is a boolean
 * @method static void assertIsCallable(mixed $var, ?string $message = '') Asserts that `$var` is a callable
 * @method static void assertIsFloat(mixed $var, ?string $message = '') Asserts that `$var` is a float
 * @method static void assertIsHtml(mixed $var, ?string $message = '') Asserts that `$var` is a html string
 * @method static void assertIsInt(mixed $var, ?string $message = '') Asserts that `$var` is an int
 * @method static void assertIsIterable(mixed $var, ?string $message = '') Asserts that `$var` is iterable, i.e. that it is an array or an object implementing `Traversable`
 * @method static void assertIsJson(mixed $var, ?string $message = '') Asserts that `$var` is a json string
 * @method static void assertIsObject(mixed $var, ?string $message = '') Asserts that `$var` is an object
 * @method static void assertIsPositive(mixed $var, ?string $message = '') Asserts that `$var` is a positive number
 * @method static void assertIsResource(mixed $var, ?string $message = '') Asserts that `$var` is a resource
 * @method static void assertIsString(mixed $var, ?string $message = '') Asserts that `$var` is a string
 * @method static void assertIsUrl(mixed $var, ?string $message = '') Asserts that `$var` is an url
 */
trait TestTrait
{
    /**
     * Magic `__call()` method.
     *
     * Provides some `assertIs*()` methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     */
    public function __call(string $name, array $arguments): void
    {
        self::__callStatic($name, $arguments);
    }

    /**
     * Magic `__callStatic()` method.
     *
     * Provides some `assertIs*()` methods (eg, `assertIsString()`).
     * @param string $name Name of the method
     * @param array $arguments Arguments
     * @return void
     * @since 1.1.12
     * @throws \BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments): void
    {
        if (str_starts_with($name, 'assertIs')) {
            $count = count($arguments);
            if (!$count || $count > 2) {
                throw new BadMethodCallException(sprintf('Method %s::%s() expects at least 1 argument, maximum 2, %d passed', __CLASS__, $name, $count));
            }

            /** @var callable $function */
            $function = match ($name) {
                'assertIsJson' => 'json_validate',
                default => 'is_' . strtolower(substr($name, 8)),
            };

            if (is_callable($function)) {
                $var = array_shift($arguments);
                /** @var callable $callable */
                $callable = [__CLASS__, 'assertTrue'];
                call_user_func_array($callable, [$function($var), ...array_values($arguments)]);

                return;
            }
        }

        throw new BadMethodCallException(sprintf('Method %s::%s() does not exist', __CLASS__, $name));
    }

    /**
     * Asserts that the array keys are equal to `$expectedKeys`
     * @param array-key[] $expectedKeys Expected keys
     * @param array $array Array to check
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public static function assertArrayKeysEqual(array $expectedKeys, array $array, string $message = ''): void
    {
        $keys = array_keys($array);
        sort($keys);
        sort($expectedKeys);
        self::assertEquals($expectedKeys, $keys, $message);
    }

    /**
     * Asserts that a filename has the `$expectedExtension`.
     *
     * If `$expectedExtension` is an array, asserts that the filename has at least one of those values.
     *
     * It is not necessary it actually exists.
     * The assertion is case-insensitive (eg, for `PIC.JPG`, the expected extension is `jpg`).
     * @param string|string[] $expectedExtension Expected extension or an array of extensions
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public static function assertFileExtension(string|array $expectedExtension, string $filename, string $message = ''): void
    {
        self::assertContains(Filesystem::instance()->getExtension($filename), (array)$expectedExtension, $message);
    }

    /**
     * Asserts that a filename have a MIME content type.
     *
     * If `$expectedMime` is an array, asserts that the filename has at least one of those values.
     * @param string|string[] $expectedMime MIME content type or an array of types
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public static function assertFileMime(string|array $expectedMime, string $filename, string $message = ''): void
    {
        self::assertFileExists($filename);
        self::assertContains(mime_content_type($filename), (array)$expectedMime, $message);
    }

    /**
     * Asserts that an image file has `$expectedWidth` and `$expectedHeight`
     * @param int $expectedWidth Expected image width
     * @param int $expectedHeight Expected mage height
     * @param string $filename Path to the tested file
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public static function assertImageSize(int $expectedWidth, int $expectedHeight, string $filename, string $message = ''): void
    {
        self::assertFileExists($filename);
        [$actualWidth, $actualHeight] = getimagesize($filename) ?: [0 => 0, 1 => 0];
        self::assertEquals($actualWidth, $expectedWidth, $message);
        self::assertEquals($actualHeight, $expectedHeight, $message);
    }

    /**
     * Asserts that `$var` is an array and is not empty
     * @param mixed $var Variable to check
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @since 1.0.6
     */
    public static function assertIsArrayNotEmpty(mixed $var, string $message = ''): void
    {
        self::assertIsArray($var, $message);
        self::assertNotEmpty(array_filter($var), $message);
    }

    /**
     * Asserts that an object is an instance of `MockObject`
     * @param object $object Object
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @since 1.5.2
     */
    public static function assertIsMock(object $object, string $message = ''): void
    {
        self::assertInstanceOf(MockObject::class, $object, $message ?: 'Failed asserting that a `' . get_class($object) . '` object is a mock');
    }

    /**
     * Asserts that the object properties are equal to `$expectedProperties`
     * @param string[] $expectedProperties Expected properties
     * @param object|object[] $object Object you want to check or an array of objects
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public function assertObjectPropertiesEqual(array $expectedProperties, object|array $object, string $message = ''): void
    {
        self::assertArrayKeysEqual($expectedProperties, (array)$object, $message);
    }

    /**
     * Asserts that `$firstClass` and `$secondClass` classes have the same methods
     * @param string|object $firstClass First class as string or object
     * @param string|object $secondClass Second class as string or object
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public static function assertSameMethods(string|object $firstClass, string|object $secondClass, string $message = ''): void
    {
        [$firstClassMethods, $secondClassMethods] = [get_class_methods($firstClass), get_class_methods($secondClass)];
        sort($firstClassMethods);
        sort($secondClassMethods);
        self::assertEquals($firstClassMethods, $secondClassMethods, $message);
    }

    /**
     * Returns a partial mock object for the specified abstract class.
     *
     * This works like the `createPartialMock()` method, but uses abstract classes and allows you to set constructor arguments
     * @param class-string $originalClassName Abstract class you want to mock
     * @param string[] $mockedMethods Methods you want to mock
     * @param array $arguments Constructor arguments
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @since 1.7.1
     * @psalm-suppress InternalClass, InternalMethod
     */
    public function createPartialMockForAbstractClass(string $originalClassName, array $mockedMethods = [], array $arguments = []): MockObject
    {
        if (!$this instanceof TestCase) {
            throw new Exception('Is this trait used by a class that extends `' . TestCase::class . '`?');
        }

        return $this->getMockForAbstractClass($originalClassName, $arguments, '', true, true, true, $mockedMethods);
    }

    /**
     * Helper method for check deprecation methods
     * @param callable $callable callable function that will receive asserts
     * @return void
     * @since 1.8.0
     * @codeCoverageIgnore
     */
    public function deprecated(callable $callable): void
    {
        $previousHandler = set_error_handler(
            function ($code, $message, $file, $line, $context = null) use (&$previousHandler, &$deprecation): bool {
                if ($code == E_USER_DEPRECATED) {
                    $deprecation = true;

                    return true;
                }
                if ($previousHandler) {
                    return $previousHandler($code, $message, $file, $line, $context);
                }

                return false;
            }
        );
        try {
            $callable();
        } finally {
        }
        $this->assertTrue($deprecation, 'Should have at least one deprecation warning');
    }

    /**
     * Skips the test if the condition is `true`
     * @param bool $shouldSkip Whether the test should be skipped
     * @param string $message The message to display
     * @return bool
     */
    public function skipIf(bool $shouldSkip, string $message = ''): bool
    {
        if ($shouldSkip) {
            /** @codeCoverageIgnore  */
            self::markTestSkipped($message);
        }

        return $shouldSkip;
    }
}
