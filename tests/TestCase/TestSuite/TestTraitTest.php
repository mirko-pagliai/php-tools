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

use App\AbstractExampleClass;
use App\AnotherExampleChildClass;
use App\ExampleChildClass;
use App\ExampleClass;
use App\SkipTestCase;
use ArrayIterator;
use GdImage;
use IteratorAggregate;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestStatus\Skipped;
use PHPUnit\Framework\TestStatus\Success;
use stdClass;
use Tools\Filesystem;
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
             'assertIsHtml' => '<b>html</b>',
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
     * @uses \Tools\TestSuite\TestTrait::assertArrayKeysEqual()
     */
    public function testAssertArrayKeysEqual(): void
    {
        $this->TestCase->assertArrayKeysEqual([], []);

        foreach ([
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'value2', 'key1' => 'value1'],
        ] as $array) {
            $this->TestCase->assertArrayKeysEqual(['key1', 'key2'], $array);
        }

        $this->TestCase->assertArrayKeysEqual([0, 1, 2], ['first', 'second', 'third']);

        $this->expectException(AssertionFailedError::class);
        $this->TestCase->assertArrayKeysEqual(['key2'], $array);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertFileExtension()
     */
    public function testAssertFileExtension(): void
    {
        $this->TestCase->assertFileExtension('jpg', 'file.jpg');
        $this->TestCase->assertFileExtension('jpeg', 'FILE.JPEG');
        $this->TestCase->assertFileExtension(['jpg', 'jpeg'], 'file.jpg');
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertFileMime()
     */
    public function testAssertFileMime(): void
    {
        $file = Filesystem::instance()->createTmpFile('string');
        $this->TestCase->assertFileMime('text/plain', $file);
        $this->TestCase->assertFileMime(['text/plain', 'inode/x-empty'], $file);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertImageSize()
     */
    public function testAssertImageSize(): void
    {
        $resource = imagecreatetruecolor(120, 20);
        if (!$resource instanceof GdImage && !is_resource($resource)) {
            $this->fail('Unable to create a valid resource image');
        }
        imagejpeg($resource, TMP . 'pic.jpg');
        $this->TestCase->assertImageSize(120, 20, TMP . 'pic.jpg');
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsArrayNotEmpty()
     */
    public function testAssertIsArrayNotEmptyWithEmptyArray(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->assertIsArrayNotEmpty([]);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsArrayNotEmpty()
     */
    public function testAssertIsArrayNotEmptyWithBoolean(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->assertIsArrayNotEmpty(false);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsArrayNotEmpty()
     */
    public function testAssertIsArrayNotEmptyWithNull(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->assertIsArrayNotEmpty(null);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsArrayNotEmpty()
     */
    public function testAssertIsArrayNotEmptyWithString(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->assertIsArrayNotEmpty('');
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsMock()
     */
    public function testAssertIsMock(): void
    {
        $MockObject = $this->getMockBuilder(stdClass::class)->getMock();
        $this->TestCase->assertIsMock($MockObject);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Failed asserting that a `stdClass` object is a mock');
        $this->TestCase->assertIsMock(new stdClass());
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertObjectPropertiesEqual()
     */
    public function testAssertObjectPropertiesEqual(): void
    {
        $object = new stdClass();
        $object->first = 'first value';
        $object->second = 'second value';
        $this->TestCase->assertObjectPropertiesEqual(['first', 'second'], $object);
        $this->TestCase->assertObjectPropertiesEqual(['second', 'first'], $object);

        $this->expectException(ExpectationFailedException::class);
        $this->TestCase->assertObjectPropertiesEqual(['first'], $object);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertSameMethods()
     */
    public function testAssertSameMethods(): void
    {
        $exampleClass = new ExampleClass();
        $this->TestCase->assertSameMethods($exampleClass, ExampleClass::class);
        $this->TestCase->assertSameMethods($exampleClass, get_class($exampleClass));

        $copyExampleClass = &$exampleClass;
        $this->TestCase->assertSameMethods($exampleClass, $copyExampleClass);

        $this->TestCase->assertSameMethods(ExampleChildClass::class, AnotherExampleChildClass::class);

        $this->expectException(AssertionFailedError::class);
        $this->TestCase->assertSameMethods(ExampleClass::class, AnotherExampleChildClass::class);
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::createPartialMockForAbstractClass()
     */
    public function testCreatePartialMockForAbstractClass(): void
    {
        $result = $this->TestCase->createPartialMockForAbstractClass(AbstractExampleClass::class);
        $this->assertIsMock($result);
        $this->assertInstanceOf(AbstractExampleClass::class, $result);

        $this->expectException(PHPUnitException::class);
        $this->expectExceptionMessage('Is this trait used by a class that extends `' . TestCase::class . '`?');
        $BadClass = new class {
            use TestTrait;
        };
        $BadClass->createPartialMockForAbstractClass(AbstractExampleClass::class);
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
