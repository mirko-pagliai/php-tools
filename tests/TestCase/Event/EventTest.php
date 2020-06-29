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

use RuntimeException;
use Tools\Entity;
use Tools\Event\Event;
use Tools\TestSuite\TestCase;

/**
 * EventTest class
 */
class EventTest extends TestCase
{
    /**
     * @var \Tools\Event\Event
     */
    protected $Event;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Event = new Event('myEvent', ['arg1', 'arg2']);
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('myEvent', $this->Event->getName());
    }

    /**
     * Test for `getArg()` method
     * @test
     */
    public function testGetArg()
    {
        $this->assertSame('arg1', $this->Event->getArg(0));
        $this->assertSame('arg2', $this->Event->getArg(1));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Argument with index `2` does not exist');
        $this->assertSame('arg2', $this->Event->getArg(2));
    }

    /**
     * Test for `getArgs()` method
     * @test
     */
    public function testGetArgs()
    {
        $this->assertSame(['arg1', 'arg2'], $this->Event->getArgs());

        //With `ArrayAccess` as event argument
        $stub = $this->getMockForAbstractClass(Entity::class);
        $Event = new Event('myEvent', $stub);
        $this->assertIsArray($Event->getArgs());
        $this->assertNotEmpty($Event->getArgs());
    }
}
