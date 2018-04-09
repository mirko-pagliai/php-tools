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

/**
 * A trait that provides some assertion methods
 */
trait TestCaseTrait
{
    /**
     * Asserts that the array keys are equal to `$expected`
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
     * Asserts for the extension of a file
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
     * Asserts that an image file has size
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
     * Asserts that a variable is an array
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
     * Asserts that a variable is an object
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
     * Asserts that a variable is a string
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
     * Asserts that two classes or objects have the same methods
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
