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

namespace Tools\Test;

use App\ExampleClass;
use ReflectionProperty;
use Tools\TestSuite\TestCase;

/**
 * Reflection\ReflectionTrait Test Case
 */
class ReflectionTraitTest extends TestCase
{
    /**
     * @var \App\ExampleClass|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $example;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->example = new ExampleClass();
    }

    /**
     * Tests for `getProperties()` method
     * @test
     */
    public function testGetProperties()
    {
        $expected = [
            'privateProperty' => 'this is a private property',
            'firstProperty' => null,
            'secondProperty' => 'a protected property',
            'publicProperty' => 'this is public',
            'staticProperty' => 'a static property',
        ];

        $this->assertEquals($expected, $this->getProperties($this->example));
        $this->assertEquals($expected, $this->getProperties(ExampleClass::class));
        $this->assertEquals($expected, $this->getProperties(new ExampleClass()));

        $this->assertArrayKeysEqual(['publicProperty', 'staticProperty'], $this->getProperties($this->example, ReflectionProperty::IS_PUBLIC));
        $this->assertArrayKeysEqual(['firstProperty', 'secondProperty'], $this->getProperties($this->example, ReflectionProperty::IS_PROTECTED));
        $this->assertArrayKeysEqual(['privateProperty'], $this->getProperties($this->example, ReflectionProperty::IS_PRIVATE));
        $this->assertArrayKeysEqual(['staticProperty'], $this->getProperties($this->example, ReflectionProperty::IS_STATIC));

        unset($expected['privateProperty']);
        $this->example = $this->getMockBuilder(ExampleClass::class)->getMock();
        $this->assertEquals($expected, $this->getProperties($this->example));
    }

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $this->assertNull($this->getProperty($this->example, 'firstProperty'));
        $this->assertEquals('a protected property', $this->getProperty($this->example, 'secondProperty'));
        $this->assertEquals('a protected property', $this->getProperty(ExampleClass::class, 'secondProperty'));
        $this->assertEquals('a protected property', $this->getProperty(new ExampleClass(), 'secondProperty'));
    }

    /**
     * Tests for `invokeMethod()` method
     * @test
     */
    public function testInvokeMethod()
    {
        $this->assertEquals('a protected method', $this->invokeMethod($this->example, 'protectedMethod'));
        $this->assertEquals('example string', $this->invokeMethod($this->example, 'protectedMethod', ['example string']));
        $this->assertEquals('a protected method', $this->invokeMethod(ExampleClass::class, 'protectedMethod'));
        $this->assertEquals('a protected method', $this->invokeMethod(new ExampleClass(), 'protectedMethod'));
    }

    /**
     * Tests for `setProperty()` method
     * @test
     */
    public function testSetProperty()
    {
        $result = $this->setProperty($this->example, 'firstProperty', 'example string');
        $this->assertNull($result);
        $this->assertEquals('example string', $this->example->firstProperty);

        $expectedResult = $this->example->secondProperty;
        $result = $this->setProperty($this->example, 'secondProperty', null);
        $this->assertEquals($expectedResult, $result);
        $this->assertNull($this->example->secondProperty);
    }
}
