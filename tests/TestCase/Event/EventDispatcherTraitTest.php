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
namespace Tools\Test\Event;

use Tools\Event\Event;
use Tools\Event\EventDispatcher;
use Tools\Event\EventDispatcherTrait;
use Tools\TestSuite\TestCase;

/**
 * EventDispatcherTraitTest class
 */
class EventDispatcherTraitTest extends TestCase
{
    use EventDispatcherTrait;

    /**
     * Test for `getEventDispatcher()` and `setEventDispatcher()` methods
     * @test
     */
    public function testGetAndSetEventDispatcher(): void
    {
        $this->assertInstanceOf(EventDispatcher::class, $this->getEventDispatcher());
        $newDispatcher = new EventDispatcher();
        $result = $this->setEventDispatcher($newDispatcher);
        $this->assertInstanceOf(__CLASS__, $result);
        $this->assertSame($newDispatcher, $this->getEventDispatcher());
    }

    /**
     * Test for `dispatchEvent()` method
     * @test
     * @todo Remove the `error_reporting()` in a future release
     */
    public function testDispatchEvent(): void
    {
        $errorLevel = error_reporting();
        error_reporting(E_ALL ^ E_DEPRECATED);

        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->setMethods(['dispatch'])
            ->getMock();
        $dispatcher->expects($this->once())
            ->method('dispatch');

        $result = $this->setEventDispatcher($dispatcher)->dispatchEvent('myEvent', ['arg']);
        $this->assertInstanceOf(Event::class, $result);
        error_reporting($errorLevel);
    }
}
