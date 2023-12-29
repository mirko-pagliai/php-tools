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
 */
class EventListTest extends TestCase
{
    /**
     * @var \Tools\Event\EventList
     */
    protected EventList $EventList;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->EventList = new EventList();
    }

    /**
     * Test for all offset methods
     * @test
     */
    public function testOffsetMethods(): void
    {
        $this->assertFalse($this->EventList->offsetExists(1));
        $this->assertNull($this->EventList->offsetGet(1));
        $this->EventList->offsetSet(1, 'string');
        $this->assertTrue($this->EventList->offsetExists(1));
        $this->assertSame('string', $this->EventList->offsetGet(1));
        $this->EventList->offsetUnset(1);
        $this->assertFalse($this->EventList->offsetExists(1));
    }

    /**
     * Test for `add()` method
     * @test
     */
    public function testAdd(): void
    {
        $Event = new Event('myEvent');
        $this->EventList->add($Event);
        $this->assertSame($Event, $this->EventList->offsetGet(0));
    }

    /**
     * Test for `count()` method
     * @test
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
     * Test for `flush()` method
     * @test
     */
    public function testFlush(): void
    {
        $this->EventList->add(new Event('myEvent'));
        $this->EventList->flush();
        $this->assertSame(0, $this->EventList->count());
    }

    /**
     * Test for `hasEvent()` method
     * @test
     */
    public function testHasEvent(): void
    {
        $this->EventList->add(new Event('myEvent'));
        $this->assertTrue($this->EventList->hasEvent('myEvent'));
        $this->assertFalse($this->EventList->hasEvent('noExisting'));
    }

    /**
     * Test for `extract()` method
     * @test
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
     * Test for `toArray()` method
     * @test
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
