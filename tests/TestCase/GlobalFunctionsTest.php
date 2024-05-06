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
use PHPUnit\Framework\TestCase;
use Tools\TestSuite\TestTrait;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    use TestTrait;

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
     * @uses rtr()
     */
    public function testRtr(): void
    {
        $this->assertSame('my' . DS . 'folder', rtr(ROOT . 'my' . DS . 'folder'));
    }
}
