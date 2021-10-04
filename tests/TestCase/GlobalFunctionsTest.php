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

use App\ExampleChildClass;
use App\ExampleClass;
use App\ExampleOfStringable;
use BadMethodCallException;
use stdClass;
use Tools\TestSuite\TestCase;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `deprecationWarning()` global function
     * @test
     */
    public function testDeprecationWarning(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        deprecationWarning('This method is deprecated');
        error_reporting($current);

        $this->expectDeprecation();
        $this->expectExceptionMessageMatches('/^This method is deprecated/');
        $this->expectExceptionMessageMatches('/You can disable deprecation warnings by setting `error_reporting\(\)` to `E_ALL & ~E_USER_DEPRECATED`\.$/');
        deprecationWarning('This method is deprecated');
    }

    /**
     * Test for `get_child_methods()` global function
     * @test
     */
    public function testGetChildMethods(): void
    {
        $this->assertEquals(['throwMethod', 'childMethod', 'anotherChildMethod'], get_child_methods(ExampleChildClass::class));

        //This class has no parent, so the result is similar to the `get_class_methods()` method
        $this->assertEquals(get_class_methods(ExampleClass::class), get_child_methods(ExampleClass::class));

        //No existing class
        $this->assertNull(get_child_methods('\NoExistingClass'));
    }

    /**
     * Test for `get_class_short_name()` global function
     * @test
     */
    public function testGetClassShortName(): void
    {
        foreach (['\App\ExampleClass', 'App\ExampleClass', ExampleClass::class, new ExampleClass()] as $class) {
            $this->assertEquals('ExampleClass', get_class_short_name($class));
        }
    }

    /**
     * Test for `is_html()` global function
     * @test
     */
    public function testIsHtml(): void
    {
        $this->assertTrue(is_html('<b>string</b>'));
        $this->assertFalse(is_html('string'));
    }

    /**
     * Test for `is_json()` global function
     * @test
     */
    public function testIsJson(): void
    {
        $this->assertTrue(is_json('{"a":1,"b":2,"c":3,"d":4,"e":5}'));
        $this->assertFalse(is_json('this is a no json string'));
    }

    /**
     * Test for `is_positive()` global function
     * @test
     */
    public function testIsPositive(): void
    {
        $this->assertTrue(is_positive(1));
        $this->assertTrue(is_positive('1'));

        foreach ([0, -1, 1.1, '0', '1.1'] as $string) {
            $this->assertFalse(is_positive($string));
        }
    }

    /**
     * Test for `slug()` global function
     * @test
     */
    public function testSlug(): void
    {
        foreach ([
            'This is a Slug',
            'This\'is a slug',
            'This\\Is\\A\\Slug',
            'This ìs a slùg',
            'this_is_a_slug',
        ] as $string) {
            $this->assertSame('this-is-a-slug', slug($string));
        }

        $this->assertSame('This-is-a-Slug', slug('This is a Slug', false));
    }

    /**
     * Test for `is_stringable()` global function
     * @test
     */
    public function testIsStringable(): void
    {
        foreach (['1', 1, 1.1, -1, 0, true, false] as $value) {
            $this->assertTrue(is_stringable($value));
        }

        foreach ([null, [], new stdClass()] as $value) {
            $this->assertFalse(is_stringable($value));
        }

        //This class implements the `__toString()` method
        $this->assertTrue(is_stringable(new ExampleOfStringable()));
    }

    /**
     * Test for `objects_map()` global function
     * @test
     */
    public function testObjectsMap(): void
    {
        $arrayOfObjects = [new ExampleClass(), new ExampleClass()];

        $result = objects_map($arrayOfObjects, 'setProperty', ['publicProperty', 'a new value']);
        $this->assertEquals(['a new value', 'a new value'], $result);

        foreach ($arrayOfObjects as $object) {
            $this->assertEquals('a new value', $object->publicProperty);
        }

        //With a no existing method
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Class `' . ExampleClass::class . '` does not have a method `noExistingMethod`');
        objects_map([new ExampleClass()], 'noExistingMethod');
    }

    /**
     * Test for `string_contains()` global function
     * @test
     */
    public function testStringContains(): void
    {
        foreach (['aaa', 'aaab', 'baaaa', 'baaac'] as $var) {
            $this->assertTrue(string_contains($var, 'aaa'));
        }

        $this->assertFalse(string_contains('abcd', 'e'));
    }

    /**
     * Test for `string_ends_with()` global function
     * @test
     */
    public function testStringEndsWith(): void
    {
        $string = 'a test with some words';
        foreach (['', 's', 'some words', $string] as $var) {
            $this->assertTrue(string_ends_with($string, $var));
        }
        foreach ([' ', 'b', 'a test'] as $var) {
            $this->assertFalse(string_ends_with($string, $var));
        }
    }

    /**
     * Test for `string_starts_with()` global function
     * @test
     */
    public function testStringStartsWith(): void
    {
        $string = 'a test with some words';
        foreach (['', 'a', 'a test', $string] as $var) {
            $this->assertTrue(string_starts_with($string, $var));
        }
        foreach ([' ', 'some words', 'test'] as $var) {
            $this->assertFalse(string_starts_with($string, $var));
        }
    }

    /**
     * Test for `uncamelcase()` global function
     * @test
     */
    public function testUncamelcase(): void
    {
        foreach (['ThisIsASlug', 'thisIsASlug'] as $string) {
            $this->assertSame('this_is_a_slug', uncamelcase($string));
        }
    }

    /**
     * Test for `which()` global function
     * @test
     */
    public function testWhich(): void
    {
        $expected = IS_WIN ? 'C:\Program Files\Git\usr\bin\cat.exe' : '/bin/cat';
        $this->assertStringEndsWith($expected, which('cat') ?? '');

        $this->expectExceptionMessage('Unable to execute `' . (IS_WIN ? 'where' : 'which') . '` for the `noExistingBin` command');
        which('noExistingBin');
    }
}
