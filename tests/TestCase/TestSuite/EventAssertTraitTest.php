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
 */
namespace Tools\Test\TestSuite;

use PHPUnit\Framework\ExpectationFailedException;
use Tools\Event\Event;
use Tools\Event\EventDispatcher;
use Tools\TestSuite\EventAssertTrait;
use Tools\TestSuite\TestCase;

/**
 * TestTraitTest class
 */
class EventAssertTraitTest extends TestCase
{
    use EventAssertTrait;

    /**
     * Test for `assertEventFired()` and `assertEventNotFired()` methods
     * @ŧest
     */
    public function testAssertEventMethods()
    {
        $EventDispatcher = new EventDispatcher();
        $EventDispatcher->dispatch('myEvent');
        $this->assertEventFired('myEvent', $EventDispatcher);
        $this->assertEventNotFired('noExisting', $EventDispatcher);
    }

    /**
     * Test for `assertEventFired()` method on failure
     * @ŧest
     */
    public function testFailureAssertEventFired()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The `noExisting` event was not fired');
        $this->assertEventFired('noExisting', new EventDispatcher());
    }

    /**
     * Test for `assertEventNotFired()` method on failure
     * @ŧest
     */
    public function testFailureAssertEventNotFired()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The `myEvent` event was not expected to be fired, instead it has been fired');
        $EventDispatcher = new EventDispatcher();
        $EventDispatcher->dispatch('myEvent');
        $this->assertEventNotFired('myEvent', $EventDispatcher);
    }
}
