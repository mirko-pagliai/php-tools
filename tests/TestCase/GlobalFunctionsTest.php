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
use PHPUnit\Framework\Error\Deprecated;
use stdClass;
use Tools\TestSuite\TestCase;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `array_clean()` global function
     * @test
     */
    public function testArrayClean()
    {
        $filterMethod = function ($value) {
            return $value && $value != 'third';
        };

        $array = ['first', 'second', false, 0, 'second', 'third', null, '', 'fourth'];
        $this->assertSame(['first', 'second', 'third', 'fourth'], array_clean($array));
        $this->assertSame(['first', 'second', 'fourth'], array_clean($array, $filterMethod));

        $array = ['a' => 'first', 0 => 'second', false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame(['a' => 'first', 0 => 'second', 'c' => 'third'], array_clean($array));
        $this->assertSame(['a' => 'first', 0 => 'second'], array_clean($array, $filterMethod));

        $expected = ['a' => 'first', 1 => false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame($expected, array_clean($array, $filterMethod, ARRAY_FILTER_USE_KEY));
    }

    /**
     * Test for `array_key_first()` global function
     * @test
     */
    public function testArrayKeyFirst()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals(0, array_key_first($array));
        $this->assertEquals('a', array_key_first(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_key_first([]));
    }

    /**
     * Test for `array_key_last()` global function
     * @test
     */
    public function testArrayKeyLast()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals(2, array_key_last($array));
        $this->assertEquals('c', array_key_last(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_key_last([]));
    }

    /**
     * Test for `array_value_first()` global function
     * @test
     */
    public function testArrayValueFirst()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('first', array_value_first($array));
        $this->assertEquals('first', array_value_first(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_first([]));
    }

    /**
     * Test for `array_value_first_recursive()` global function
     * @test
     */
    public function testArrayValueFirstRecursive()
    {
        $this->assertEquals(null, array_value_first_recursive([]));
        foreach ([
            ['first', 'second', 'third', 'fourth'],
            ['first', ['second', 'third'], ['fourth']],
            [['first', 'second'], ['third'], ['fourth']],
            [[['first'], 'second'], ['third'], [['fourth']]],
        ] as $array) {
            $this->assertEquals('first', array_value_first_recursive($array));
        }
    }

    /**
     * Test for `array_value_last()` global function
     * @test
     */
    public function testArrayValueLast()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('third', array_value_last($array));
        $this->assertEquals('third', array_value_last(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_last([]));
    }

    /**
     * Test for `array_value_last_recursive()` global function
     * @test
     */
    public function testArrayValueLastRecursive()
    {
        $this->assertEquals(null, array_value_last_recursive([]));
        foreach ([
            ['first', 'second', 'third', 'fourth'],
            ['first', ['second', 'third'], ['fourth']],
            [['first', 'second'], ['third'], ['fourth']],
            [[['first'], 'second'], ['third'], [['fourth']]],
        ] as $array) {
            $this->assertEquals('fourth', array_value_last_recursive($array));
        }
    }

    /**
     * Test for `deprecationWarning()` global function
     * @test
     */
    public function testDeprecationWarning()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        deprecationWarning('This method is deprecated');
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessageRegExp('/^This method is deprecated/');
        $this->expectExceptionMessageRegExp('/You can disable deprecation warnings by setting `error_reporting\(\)` to `E_ALL & ~E_USER_DEPRECATED`\.$/');
        deprecationWarning('This method is deprecated');
    }

    /**
     * Test for `get_child_methods()` global function
     * @test
     */
    public function testGetChildMethods()
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
    public function testGetClassShortName()
    {
        foreach (['\App\ExampleClass', 'App\ExampleClass', ExampleClass::class, new ExampleClass()] as $class) {
            $this->assertEquals('ExampleClass', get_class_short_name($class));
        }
    }

    /**
     * Test for `is_html()` global function
     * @test
     */
    public function testIsHtml()
    {
        $this->assertTrue(is_html('<b>string</b>'));
        $this->assertFalse(is_html('string'));
    }

    /**
     * Test for `is_json()` global function
     * @test
     */
    public function testIsJson()
    {
        $this->assertTrue(is_json('{"a":1,"b":2,"c":3,"d":4,"e":5}'));
        $this->assertFalse(is_json('this is a no json string'));
    }

    /**
     * Test for `is_positive()` global function
     * @test
     */
    public function testIsPositive()
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
    public function testSlug()
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
    public function testIsStringable()
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
    public function testObjectsMap()
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
    public function testStringContains()
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
    public function testStringEndsWith()
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
    public function testStringStartsWith()
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
    public function testUncamelcase()
    {
        foreach (['ThisIsASlug', 'thisIsASlug'] as $string) {
            $this->assertSame('this_is_a_slug', uncamelcase($string));
        }
    }

    /**
     * Test for `which()` global function
     * @test
     */
    public function testWhich()
    {
        $expected = IS_WIN ? '"C:\Program Files\Git\usr\bin\cat.exe"' : '/bin/cat';
        $this->assertStringEndsWith($expected, which('cat'));
        $this->assertNull(which('noExistingBin'));
    }
}
