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

use Tools\TestSuite\TestCase;

/**
 * ArrayFunctionsTest class
 */
class ArrayFunctionsTest extends TestCase
{
    /**
     * Test for `array_clean()` global function
     * @uses \array_clean()
     * @test
     */
    public function testArrayClean(): void
    {
        $filterMethod = fn($value): bool => $value && $value != 'third';

        $array = ['first', 'second', false, 0, 'second', 'third', null, '', 'fourth'];
        $this->assertSame(['first', 'second', 'third', 'fourth'], array_clean($array));
        $this->assertSame(['first', 'second', 'fourth'], array_clean($array, $filterMethod));

        //With a string as `$callback`
        $array = ['string', 1];
        $this->assertSame(['string'], array_clean($array, 'is_string'));

        $array = ['a' => 'first', 0 => 'second', false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame(['a' => 'first', 0 => 'second', 'c' => 'third'], array_clean($array));
        $this->assertSame(['a' => 'first', 0 => 'second'], array_clean($array, $filterMethod));

        $expected = ['a' => 'first', 1 => false, 'c' => 'third', 'd' => 'second'];
        $this->assertSame($expected, array_clean($array, $filterMethod, ARRAY_FILTER_USE_KEY));
    }

    /**
     * Test for `array_has_only_numeric_keys()` global function
     * @test
     */
    public function testArrayHasOnlyNumericKeys(): void
    {
        $this->assertTrue(array_has_only_numeric_keys(['first', 'second']));
        $this->assertTrue(array_has_only_numeric_keys([]));
        $this->assertFalse(array_has_only_numeric_keys(['a' => 'first', 'b' => 'second']));
        $this->assertFalse(array_has_only_numeric_keys(['first', 'b' => 'second']));
    }

    /**
     * Test for `array_unique_recursive()` global function
     * @test
     */
    public function testArrayUniqueRecursive(): void
    {
        $array = [
            ['first', 'second'],
            ['first', 'second'],
            ['other'],
        ];

        $this->assertSame([['first', 'second'], ['other']], array_unique_recursive($array));
    }

    /**
     * Test for `array_value_first()` global function
     * @test
     */
    public function testArrayValueFirst(): void
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
    public function testArrayValueFirstRecursive(): void
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
    public function testArrayValueLast(): void
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
    public function testArrayValueLastRecursive(): void
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
}
