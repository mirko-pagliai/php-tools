<?php
/** @noinspection PhpUnitAssertCanBeReplacedWithFailInspection */
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
 */

namespace App;

use Tools\TestSuite\TestCase;

/**
 * AssertionFailedTestCase class
 */
class AssertionFailedTestCase extends TestCase
{
    /**
     * test that a test expects that an assertion failed
     * @return void
     */
    public function testAssertionFailed(): void
    {
        $this->expectAssertionFailed();
        $this->assertTrue(false);
    }

    /**
     * test that a test expects that an assertion failed, with the failure message
     * @return void
     */
    public function testAssertionFailedWithMessage(): void
    {
        $this->expectAssertionFailed('this is no true');
        $this->assertTrue(false, 'this is no true');
    }

    /**
     * test that a test expects that an assertion failed, which does not occur (the assertion is right)
     * @return void
     */
    public function testAssertionFailedMissingFailure(): void
    {
        $this->expectAssertionFailed();
        $this->assertTrue(true);
    }

    /**
     * test that a test expects that an assertion failed, which does not occur (the assertion is missing)
     * @return void
     */
    public function testAssertionFailedMissingAssertion(): void
    {
        $this->expectAssertionFailed();
    }

    /**
     * test that a test expects that an assertion failed, but it has another message
     * @return void
     */
    public function testAssertionFailedWithBadMessage(): void
    {
        $this->expectAssertionFailed('this is no true');
        $this->assertTrue(false);
    }
}
