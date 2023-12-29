<?php
/** @noinspection PhpUnhandledExceptionInspection */
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

namespace Tools\Test\TestSuite;

use App\ReflectionTraitTestCase;
use Tools\TestSuite\TestCase;

/**
 * ReflectionTraitTest class
 */
class ReflectionTraitTest extends TestCase
{
    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::getProperties()
     */
    public function testGetProperties(): void
    {
        $result = (new ReflectionTraitTestCase('testGetProperties'))->run();
        $this->assertTrue($result->wasSuccessful());
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::getProperty()
     */
    public function testGetProperty(): void
    {
        $result = (new ReflectionTraitTestCase('testGetProperty'))->run();
        $this->assertTrue($result->wasSuccessful());
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::invokeMethod()
     */
    public function testInvokeMethod(): void
    {
        $result = (new ReflectionTraitTestCase('testInvokeMethod'))->run();
        $this->assertTrue($result->wasSuccessful());
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::setProperty()
     */
    public function testSetProperty(): void
    {
        $result = (new ReflectionTraitTestCase('testSetProperty'))->run();
        $this->assertTrue($result->wasSuccessful());
    }
}
