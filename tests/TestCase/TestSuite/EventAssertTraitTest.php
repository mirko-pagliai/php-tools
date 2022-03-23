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
     * Test for `assertEventFired()`, `assertEventFiredWithArgs()` and
     *  `assertEventNotFired()` methods
     * @ŧest
     */
    public function testAssertEventMethods(): void
    {
        $EventDispatcher = new EventDispatcher();
        $EventDispatcher->dispatch(new Event('myEvent'));
        $this->assertEventFired('myEvent', $EventDispatcher);
        $this->assertEventNotFired('noExisting', $EventDispatcher);

        $EventDispatcher->dispatch(new Event('anotherEvent', ['arg1', 'arg2']));
        $this->assertEventFiredWithArgs('anotherEvent', ['arg1', 'arg2'], $EventDispatcher);
    }

    /**
     * Test for `assertEventFired()` method on failure
     * @ŧest
     */
    public function testFailureAssertEventFired(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The `noExisting` event was not fired');
        $this->assertEventFired('noExisting', new EventDispatcher());
    }

    /**
     * Test for `assertEventFiredWithArgs()` method on failure
     * @ŧest
     */
    public function testFailureAssertEventFiredWithArgs(): void
    {
        $EventDispatcher = new EventDispatcher();
        $EventDispatcher->dispatch(new Event('anotherEvent', ['arg1', 'arg2']));

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The `anotherEvent` event was not fired with the specified arguments `arg3`');
        $this->assertEventFiredWithArgs('anotherEvent', ['arg3'], $EventDispatcher);
    }

    /**
     * Test for `assertEventNotFired()` method on failure
     * @ŧest
     */
    public function testFailureAssertEventNotFired(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The `myEvent` event was not expected to be fired, instead it has been fired');
        $EventDispatcher = new EventDispatcher();
        $EventDispatcher->dispatch(new Event('myEvent'));
        $this->assertEventNotFired('myEvent', $EventDispatcher);
    }
}
