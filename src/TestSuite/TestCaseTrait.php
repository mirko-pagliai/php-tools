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
    protected function assertFileMime($filename, $expectedMime, $message = '')
    {
        if (!version_compare(PHP_VERSION, '7.0', '>') &&
            in_array($expectedMime, ['image/x-ms-bmp', 'image/vnd.adobe.photoshop'])) {
            $this->markTestSkipped($message);

            return;
        }

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
