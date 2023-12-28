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
     * @test
     * @uses \Tools\Event\EventDispatcherTrait::getEventDispatcher()
     * @uses \Tools\Event\EventDispatcherTrait::setEventDispatcher()
     */
    public function testGetAndSetEventDispatcher(): void
    {
        $this->assertInstanceOf(EventDispatcher::class, $this->getEventDispatcher());
        $newDispatcher = new EventDispatcher();
        $result = $this->setEventDispatcher($newDispatcher);
        $this->assertInstanceOf(EventDispatcherTraitTest::class, $result);
        $this->assertSame($newDispatcher, $this->getEventDispatcher());
    }

    /**
     * @test
     * @uses \Tools\Event\EventDispatcherTrait::dispatchEvent()
     */
    public function testDispatchEvent(): void
    {
        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher->expects($this->once())->method('dispatch');
        $result = $this->setEventDispatcher($dispatcher)->dispatchEvent('myEvent', ['arg']);
        $this->assertInstanceOf(Event::class, $result);
    }
}
