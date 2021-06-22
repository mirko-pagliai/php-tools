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
use Tools\Event\EventList;
use Tools\TestSuite\TestCase;

/**
 * EventDispatcherTest class
 */
class EventDispatcherTest extends TestCase
{
    /**
     * @var \Tools\Event\EventDispatcher
     */
    protected $EventDispatcher;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->EventDispatcher = new EventDispatcher();
    }

    /**
     * Test for `dispatch()` method
     * @test
     */
    public function testDispatch(): void
    {
        $event = new Event('myEvent');
        $result = $this->EventDispatcher->dispatch($event);
        $this->assertSame($event, $result);
        $this->assertSame($event, $this->EventDispatcher->getEventList()->offsetGet(0));
    }

    /**
     * Test for `getEventList()` method
     * @test
     */
    public function testGetEventList(): void
    {
        $this->assertInstanceOf(EventList::class, $this->EventDispatcher->getEventList());
    }
}
