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
