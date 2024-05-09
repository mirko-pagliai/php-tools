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

use PHPUnit\Framework\TestCase;
use Tools\Event\Event;
use Tools\Event\EventList;

/**
 * EventListTest class
 * @group legacy
 */
class EventListTest extends TestCase
{
    /**
     * @var \Tools\Event\EventList
     */
    protected EventList $EventList;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->EventList = new EventList();
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::offsetExists()
     * @uses \Tools\Event\EventList::offsetGet()
     * @uses \Tools\Event\EventList::offsetSet()
     * @uses \Tools\Event\EventList::offsetUnset()
     */
    public function testOffsetMethods(): void
    {
        $Event = new Event('myEvent');
        $this->assertFalse($this->EventList->offsetExists(1));
        $this->assertNull($this->EventList->offsetGet(1));
        $this->EventList->offsetSet(1, $Event);
        $this->assertTrue($this->EventList->offsetExists(1));
        $this->assertSame($Event, $this->EventList->offsetGet(1));
        $this->EventList->offsetUnset(1);
        $this->assertFalse($this->EventList->offsetExists(1));
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::add()
     */
    public function testAdd(): void
    {
        $Event = new Event('myEvent');
        $this->EventList->add($Event);
        $this->assertSame($Event, $this->EventList->offsetGet(0));
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::count()
     */
    public function testCount(): void
    {
        $this->assertSame(0, $this->EventList->count());
        $this->EventList->add(new Event('myEvent'));
        $this->assertSame(1, $this->EventList->count());
        $this->EventList->add(new Event('anotherEvent'));
        $this->assertSame(2, $this->EventList->count());
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::flush()
     */
    public function testFlush(): void
    {
        $this->EventList->add(new Event('myEvent'));
        $this->EventList->flush();
        $this->assertSame(0, $this->EventList->count());
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::hasEvent()
     */
    public function testHasEvent(): void
    {
        $this->EventList->add(new Event('myEvent'));
        $this->assertTrue($this->EventList->hasEvent('myEvent'));
        $this->assertFalse($this->EventList->hasEvent('noExisting'));
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::extract()
     */
    public function testExtract(): void
    {
        $this->assertEmpty($this->EventList->extract('myEvent'));

        $this->EventList->add(new Event('myEvent'));
        $this->EventList->add(new Event('anotherEvent'));
        $this->EventList->add(new Event('myEvent'));
        $result = $this->EventList->extract('myEvent');
        $this->assertCount(2, $result);
        $result = $this->EventList->extract('anotherEvent');
        $this->assertCount(1, $result);
        $result = $this->EventList->extract('noExistingEvent');
        $this->assertCount(0, $result);
    }

    /**
     * @test
     * @uses \Tools\Event\EventList::toArray()
     */
    public function testToArray(): void
    {
        $this->assertEmpty($this->EventList->toArray());

        $this->EventList->add(new Event('myEvent'));
        $this->EventList->add(new Event('anotherEvent'));
        $result = $this->EventList->toArray();
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Event::class, $result);
    }
}
