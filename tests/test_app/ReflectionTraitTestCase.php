<?php
declare(strict_types=1);

namespace App;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tools\TestSuite\ReflectionTrait;

/**
 * ReflectionTraitTestCase
 */
class ReflectionTraitTestCase extends TestCase
{
    use ReflectionTrait;

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::getProperties()
     */
    public function testGetProperties()
    {
        $Example = new ExampleClass();

        $expected = [
            'privateProperty' => 'this is a private property',
            'firstProperty' => null,
            'secondProperty' => 'a protected property',
            'publicProperty' => 'this is public',
            'staticProperty' => 'a static property',
        ];
        $this->assertEquals($expected, $this->getProperties($Example));
        $this->assertEquals($expected, $this->getProperties(ExampleClass::class));

        $this->assertEquals(['publicProperty', 'staticProperty'], array_keys($this->getProperties($Example, ReflectionProperty::IS_PUBLIC)));
        $this->assertEquals(['firstProperty', 'secondProperty'], array_keys($this->getProperties($Example, ReflectionProperty::IS_PROTECTED)));
        $this->assertEquals(['privateProperty'], array_keys($this->getProperties($Example, ReflectionProperty::IS_PRIVATE)));
        $this->assertEquals(['staticProperty'], array_keys($this->getProperties($Example, ReflectionProperty::IS_STATIC)));

        unset($expected['privateProperty']);
        $Example = $this->getMockBuilder(ExampleClass::class)->getMock();
        $this->assertEquals($expected, $this->getProperties($Example));
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::getProperty()
     */
    public function testGetProperty(): void
    {
        $Example = new ExampleClass();

        $this->assertNull($this->getProperty($Example, 'firstProperty'));
        $this->assertEquals('a protected property', $this->getProperty($Example, 'secondProperty'));
        $this->assertEquals('a protected property', $this->getProperty(ExampleClass::class, 'secondProperty'));
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::invokeMethod()
     */
    public function testInvokeMethod(): void
    {
        $Example = new ExampleClass();

        $this->assertEquals('a protected method', $this->invokeMethod($Example, 'protectedMethod'));
        $this->assertEquals('example string', $this->invokeMethod($Example, 'protectedMethod', ['example string']));
        $this->assertEquals('a protected method', $this->invokeMethod(ExampleClass::class, 'protectedMethod'));
    }

    /**
     * @test
     * @uses \Tools\TestSuite\ReflectionTrait::setProperty()
     */
    public function testSetProperty(): void
    {
        $Example = new ExampleClass();

        $result = $this->setProperty($Example, 'firstProperty', 'example string');
        $this->assertNull($result);
        $this->assertEquals('example string', $Example->firstProperty);

        $expectedResult = $Example->secondProperty;
        $result = $this->setProperty($Example, 'secondProperty', null);
        $this->assertEquals($expectedResult, $result);
        $this->assertNull($Example->secondProperty);
    }
}
