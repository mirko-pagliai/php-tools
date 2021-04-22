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
 * @since       1.4.4
 */
namespace Tools\TestSuite;

use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * This trait provides methods to achieve PHPUnit backward compatibility
 */
trait BackwardCompatibilityTrait
{
    /**
     * Asserts that a directory does not exist
     * @param string $directory Directory path
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertDirectoryDoesNotExist($directory, $message = '')
    {
        if (!method_exists(PHPUnitTestCase::class, 'assertDirectoryDoesNotExist')) {
            self::assertDirectoryNotExists($directory, $message);

            return;
        }

        parent::assertDirectoryDoesNotExist($directory, $message);
    }

    /**
     * Asserts that a file does not exist
     * @param string $filename Filename
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertFileDoesNotExist($filename, $message = '')
    {
        if (!method_exists(PHPUnitTestCase::class, 'assertFileDoesNotExist')) {
            self::assertFileNotExists($filename, $message);

            return;
        }

        parent::assertFileDoesNotExist($filename, $message);
    }

    /**
     * Asserts that a string matches a given regular expression
     * @param string $pattern A regular expression
     * @param string $string A string to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public static function assertMatchesRegularExpression($pattern, $string, $message = '')
    {
        if (!method_exists(PHPUnitTestCase::class, 'assertMatchesRegularExpression')) {
            self::assertRegExp($pattern, $string, $message);

            return;
        }

        parent::assertMatchesRegularExpression($pattern, $string, $message);
    }

    /**
     * Expects a notice
     * @return void
     */
    public function expectNotice()
    {
        if (!method_exists(PHPUnitTestCase::class, 'expectNotice')) {
            $this->expectException(Notice::class);

            return;
        }

        parent::expectNotice();
    }

    /**
     * Expects a deprecation
     * @return void
     */
    public function expectDeprecation()
    {
        if (!method_exists(PHPUnitTestCase::class, 'expectDeprecation')) {
            $this->expectException(Deprecated::class);

            return;
        }

        parent::expectDeprecation();
    }

    /**
     * Expectes thath the exception message matches `$regularExpression`.
     * @param string $regularExpression A regular expression
     * @return void
     */
    public function expectExceptionMessageMatches($regularExpression)
    {
        if (!method_exists(PHPUnitTestCase::class, 'expectExceptionMessageMatches')) {
            /* @phpstan-ignore-next-line */
            $this->expectExceptionMessageRegExp($regularExpression);

            return;
        }

        parent::expectExceptionMessageMatches($regularExpression);
    }
}
