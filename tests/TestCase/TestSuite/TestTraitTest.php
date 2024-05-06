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

namespace Tools\Test\TestSuite;

use App\SkipTestCase;
use ArrayIterator;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestStatus\Skipped;
use PHPUnit\Framework\TestStatus\Success;
use stdClass;
use Tools\TestSuite\TestTrait;
use Traversable;

/**
 * TestTraitTest class
 */
class TestTraitTest extends TestCase
{
    use TestTrait;

    /**
     * @var \PHPUnit\Framework\TestCase
     */
    protected TestCase $TestCase;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->TestCase = new class ('myTest') extends TestCase {
            use TestTrait;
        };
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::__call()
     * @uses \Tools\TestSuite\TestTrait::__callStatic()
     */
    public function testMagicCallAndCallStatic(): void
    {
        $Traversable = new class implements IteratorAggregate {
            public function getIterator(): Traversable
            {
                return new ArrayIterator([]);
            }
        };

        foreach ([
             'assertIsArray' => ['array'],
             'assertIsBool' => true,
             'assertIsCallable' => fn() => '',
             'assertIsFloat' => 1.1,
             'assertIsInt' => 1,
             'assertIsIterable' => $Traversable,
             'assertIsJson' => '{"a":1,"b":2,"c":3,"d":4,"e":5}',
             'assertIsObject' => new stdClass(),
             'assertIsPositive' => '1',
             'assertIsResource' => tmpfile(),
             'assertIsString' => 'string',
             'assertIsUrl' => 'http://localhost',
         ] as $assertMethod => $value) {
            $this->TestCase->$assertMethod($value);
            /** @var callable $staticCallable */
            $staticCallable = [$this->TestCase, $assertMethod];
            forward_static_call($staticCallable, $value);
        }
    }

    /**
     * Test for `__callStatic()` method with a no existing method
     * @test
     * @uses \Tools\TestSuite\TestTrait::__callStatic()
     */
    public function testMagicCallWithNoExistingMethod(): void
    {
        $this->expectExceptionMessage('Method ' . get_class($this->TestCase) . '::noExistingMethod() does not exist');
        $this->TestCase->noExistingMethod('string');
    }

    /**
     * Test for `__callStatic()` method with a no existing "assertIs" method
     * @test
     * @uses \Tools\TestSuite\TestTrait::__callStatic()
     */
    public function testMagicCallWithNoExistingAssertIsMethod(): void
    {
        $this->expectExceptionMessage('Method ' . get_class($this->TestCase) . '::assertIsNoExistingType() does not exist');
        $this->TestCase->assertIsNoExistingType('string');
    }

    /**
     * Test for `__callStatic()` method missing arguments
     * @test
     * @uses \Tools\TestSuite\TestTrait::__callStatic()
     */
    public function testMagicCallMissingArgs(): void
    {
        $this->expectExceptionMessage('Method ' . get_class($this->TestCase) . '::assertIsJson() expects at least 1 argument, maximum 2, 0 passed');
        $this->TestCase->assertIsJson();
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::skipIf()
     */
    public function testSkipIf(): void
    {
        $Test = (new SkipTestCase('testSkipIfTrue'));
        $Test->run();
        $this->assertInstanceOf(Skipped::class, $Test->status());

        $Test = (new SkipTestCase('testSkipIfFalse'));
        $Test->run();
        $this->assertInstanceOf(Success::class, $Test->status());
    }
}
