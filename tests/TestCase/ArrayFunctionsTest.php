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

use PHPUnit\Framework\TestCase;

/**
 * ArrayFunctionsTest class
 */
class ArrayFunctionsTest extends TestCase
{
    /**
     * @test
     * @uses \array_clean()
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
     * @test
     * @uses \array_has_only_numeric_keys()
     */
    public function testArrayHasOnlyNumericKeys(): void
    {
        $this->assertTrue(array_has_only_numeric_keys(['first', 'second']));
        $this->assertTrue(array_has_only_numeric_keys([]));
        $this->assertFalse(array_has_only_numeric_keys(['a' => 'first', 'b' => 'second']));
        $this->assertFalse(array_has_only_numeric_keys(['first', 'b' => 'second']));
    }

    /**
     * @test
     * @uses \array_unique_recursive()
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
     * @test
     * @uses \array_value_first()
     */
    public function testArrayValueFirst(): void
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('first', array_value_first($array));
        $this->assertEquals('first', array_value_first(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_first([]));
    }

    /**
     * @test
     * @uses \array_value_first_recursive()
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
     * @test
     * @uses \array_value_last()
     */
    public function testArrayValueLast(): void
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('third', array_value_last($array));
        $this->assertEquals('third', array_value_last(array_combine(['a', 'b', 'c'], $array)));
        $this->assertEquals(null, array_value_last([]));
    }

    /**
     * @test
     * @uses \array_value_last_recursive()
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

    /**
     * @test
     * @uses \is_array_key_first()
     * @uses \is_array_key_last()
     */
    public function testIsArrayKeyFirstAndIsArrayKeyLast(): void
    {
        $array = ['first', 'second', 'third'];
        $this->assertTrue(is_array_key_first(0, $array));
        $this->assertFalse(is_array_key_first(1, $array));
        $this->assertFalse(is_array_key_first(2, $array));
        $this->assertFalse(is_array_key_first(3, $array));
        $this->assertFalse(is_array_key_last(0, $array));
        $this->assertFalse(is_array_key_last(1, $array));
        $this->assertTrue(is_array_key_last(2, $array));
        $this->assertFalse(is_array_key_last(3, $array));

        $array = ['a' => 'first', 'b' => 'second', 'c' => 'third'];
        $this->assertTrue(is_array_key_first('a', $array));
        $this->assertFalse(is_array_key_first('b', $array));
        $this->assertFalse(is_array_key_first('c', $array));
        $this->assertFalse(is_array_key_first('d', $array));
        $this->assertFalse(is_array_key_last('a', $array));
        $this->assertFalse(is_array_key_last('b', $array));
        $this->assertTrue(is_array_key_last('c', $array));
        $this->assertFalse(is_array_key_last('d', $array));
    }
}
