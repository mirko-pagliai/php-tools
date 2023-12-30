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
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tools\TestSuite\TestTrait;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    use TestTrait;

    /**
     * @test
     * @uses array_to_string()
     */
    public function testArrayToString(): void
    {
        $this->assertSame('[\'a\', \'1\', \'0.5\', \'c\']', array_to_string(['a', 1, 0.5, 'c']));
        $this->assertSame('[]', array_to_string([]));

        $ToStringClass = new class {
            public function __toString()
            {
                return __CLASS__;
            }
        };
        //This class implements the `__toString()` method
        $this->assertSame('[\'a\', \'' . (string)$ToStringClass . '\']', array_to_string(['a', $ToStringClass]));

        foreach ([['a', true], ['a', ['b', 'c']]] as $array) {
            $this->assertException(fn() => array_to_string($array), LogicException::class, 'Cannot convert array to string, some values are not stringable');
        }
    }

    /**
     * @test
     * @uses get_child_methods()
     */
    public function testGetChildMethods(): void
    {
        $this->assertEquals(['throwMethod', 'childMethod', 'anotherChildMethod'], get_child_methods(ExampleChildClass::class));

        //This class has no parent, so the result is similar to the `get_class_methods()` method
        $this->assertEquals(get_class_methods(ExampleClass::class), get_child_methods(ExampleClass::class));

        $this->expectExceptionMessage('Class `\NoExistingClass` does not exist');
        /** @phpstan-ignore-next-line */
        get_child_methods('\NoExistingClass');
    }

    /**
     * @test
     * @uses get_class_short_name()
     */
    public function testGetClassShortName(): void
    {
        foreach (['\App\ExampleClass', 'App\ExampleClass', ExampleClass::class, new ExampleClass()] as $class) {
            $this->assertEquals('ExampleClass', get_class_short_name($class));
        }
    }

    /**
     * @test
     * @uses is_html()
     */
    public function testIsHtml(): void
    {
        $this->assertTrue(is_html('<b>string</b>'));
        $this->assertFalse(is_html('string'));
    }

    /**
     * @test
     * @uses is_positive()
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
     * @test
     * @uses slug()
     * @noinspection SpellCheckingInspection
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
     * @test
     * @uses is_stringable()
     */
    public function testIsStringable(): void
    {
        foreach (['1', 1, 1.1, -1, 0, true, false] as $value) {
            $this->assertTrue(is_stringable($value));
        }

        foreach ([null, new stdClass()] as $value) {
            $this->assertFalse(is_stringable($value));
        }

        $ToStringClass = new class {
            public function __toString()
            {
                return __CLASS__;
            }
        };
        $this->assertTrue(is_stringable([]));
        $this->assertTrue(is_stringable(['a', 1, 0.5, 'c']));
        $this->assertFalse(is_stringable(['a', true]));
        $this->assertFalse(is_stringable(['a', ['b', ['c']]]));
        $this->assertTrue(is_stringable($ToStringClass));
    }

    /**
     * @test
     * @uses rtr()
     */
    public function testRtr(): void
    {
        $this->assertSame('my' . DS . 'folder', rtr(ROOT . 'my' . DS . 'folder'));
    }

    /**
     * @test
     * @uses uncamelcase()
     */
    public function testUncamelcase(): void
    {
        foreach (['ThisIsASlug', 'thisIsASlug'] as $string) {
            $this->assertSame('this_is_a_slug', uncamelcase($string));
        }
    }
}
