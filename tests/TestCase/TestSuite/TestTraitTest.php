<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
use App\AssertionFailedTestCase;
use App\ExampleChildClass;
use App\ExampleClass;
use App\ExampleOfTraversable;
use App\SkipTestCase;
use BadMethodCallException;
use ErrorException;
use Exception;
use GdImage;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Error\Deprecated;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use stdClass;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;
use Tools\TestSuite\TestTrait;

/**
 * TestTraitTest class
 */
class TestTraitTest extends TestCase
{
    /**
     * @var \Tools\TestSuite\TestCase
     */
    protected TestCase $TestCase;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->TestCase = new class ('test') extends TestCase {
        };
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::__call()
     * @uses \Tools\TestSuite\TestTrait::__callStatic()
     */
    public function testMagicCallAndCallStatic(): void
    {
        foreach ([
            'assertIsArray' => ['array'],
            'assertIsBool' => true,
            'assertIsCallable' => fn() => '',
            'assertIsFloat' => 1.1,
            'assertIsHtml' => '<b>html</b>',
            'assertIsInt' => 1,
            'assertIsIterable' => new ExampleOfTraversable(),
            'assertIsJson' => '{"a":1,"b":2,"c":3,"d":4,"e":5}',
            'assertIsObject' => new stdClass(),
            'assertIsPositive' => '1',
            'assertIsResource' => tmpfile(),
            'assertIsString' => 'string',
            'assertIsUrl' => 'http://localhost',
        ] as $assertMethod => $value) {
            $this->TestCase->$assertMethod($value);
            TestCase::$assertMethod($value);
        }

        //Missing argument
        $this->assertException(
            [$this->TestCase, 'assertIsJson'],
            BadMethodCallException::class,
            'Method ' . get_parent_class($this->TestCase) . '::assertIsJson() expects at least 1 argument, maximum 2, 0 passed'
        );

        //Calling a no existing method or a no existing `assertIs*()` method
        foreach (['assertIsNoExistingType', 'noExistingMethod'] as $method) {
            $this->assertException(fn() => $this->TestCase->$method('string'), BadMethodCallException::class, 'Method ' . get_parent_class($this->TestCase) . '::' . $method . '() does not exist');
        }
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
     * @uses \Tools\TestSuite\TestTrait::assertException()
     */
    public function testAssertException(): void
    {
        $this->TestCase->assertException(function () {
            throw new Exception();
        });
        $this->TestCase->assertException(function () {
            throw new Exception('right exception message');
        }, Exception::class, 'right exception message');

        //It correctly ignores deprecations
        $this->TestCase->assertException(function () {
            deprecationWarning('1.0', 'This is a deprecation!');
            throw new ErrorException('This is an error exception');
        }, ErrorException::class, 'This is an error exception');

        //No exception throw
        try {
            $this->TestCase->assertException('time');
        } catch (AssertionFailedError $e) {
            $this->assertSame('Expected exception `Exception`, but no exception throw', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }

        //The comparison is strict, it does not consider parent classes (`ErrorException` != `Exception`)
        try {
            $this->TestCase->assertException(function () {
                throw new ErrorException();
            });
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected exception `Exception`, unexpected type `ErrorException`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }

        //No existing exception or invalid exception class
        foreach (['noExistingException', stdClass::class] as $class) {
            try {
                $this->TestCase->assertException(function () {
                    throw new Exception();
                }, $class);
            } catch (AssertionFailedError $e) {
                $this->assertSame('Class `' . $class . '` is not a throwable or does not exist', $e->getMessage());
            } finally {
                if (!isset($e)) {
                    self::fail();
                }
                unset($e);
            }
        }

        //Unexpected exception type
        try {
            $this->TestCase->assertException(function () {
                throw new Exception();
            }, BadMethodCallException::class);
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected exception `' . BadMethodCallException::class . '`, unexpected type `Exception`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }

        //Wrong exception message
        try {
            $this->TestCase->assertException(function () {
                throw new Exception('Wrong');
            }, Exception::class, 'Right');
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected message exception `Right`, unexpected message `Wrong`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }

        //Expected exception message, but no message
        try {
            $this->TestCase->assertException(function () {
                throw new Exception();
            }, Exception::class, 'Right');
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected message exception `Right`, but no message for the exception', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }
    }

    /**
     * Test for `assertException` with a deprecation (it can't assert deprecations)
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertException()
     */
    public function testAssertExceptionWithDeprecation(): void
    {
        $this->skipIf(!class_exists(Deprecated::class));

        //Can't assert deprecations
        try {
            $this->TestCase->assertException(function () {
                deprecationWarning('This is a deprecation');
            }, Deprecated::class);
        } catch (Notice $e) {
            $this->assertSame('You cannot use `assertException()` for deprecations, use instead `assertDeprecated()`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail();
            }
            unset($e);
        }
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
    public function testAssertIsArrayNotEmpty(): void
    {
        $this->TestCase->assertIsArrayNotEmpty(['value']);

        foreach ([
            [],
            [[]],
            [false],
            [null],
            [''],
        ] as $array) {
            $this->assertException(fn() => $this->TestCase->assertIsArrayNotEmpty($array), ExpectationFailedException::class);
        }
    }

    /**
     * @test
     * @uses \Tools\TestSuite\TestTrait::assertIsMock()
     */
    public function testAssertIsMock(): void
    {
        $MockObject = $this->getMockBuilder(stdClass::class)->getMock();
        $this->TestCase->assertIsMock($MockObject);

        $this->expectAssertionFailed('Failed asserting that a `stdClass` object is a mock');
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
     * @uses \Tools\TestSuite\TestTrait::expectAssertionFailed()
     */
    public function testExpectAssertionFailed(): void
    {
        $result = (new AssertionFailedTestCase('testAssertionFailed'))->run();
        $this->assertSame(0, $result->failureCount());

        $result = (new AssertionFailedTestCase('testAssertionFailedWithMessage'))->run();
        $this->assertSame(0, $result->failureCount());

        $result = (new AssertionFailedTestCase('testAssertionFailedMissingFailure'))->run();
        $this->assertSame(1, $result->failureCount());
        $this->assertSame('Failed asserting that exception of type "PHPUnit\Framework\AssertionFailedError" is thrown.', $result->failures()[0]->exceptionMessage());

        $result = (new AssertionFailedTestCase('testAssertionFailedMissingAssertion'))->run();
        $this->assertSame(1, $result->failureCount());
        $this->assertSame('Failed asserting that exception of type "PHPUnit\Framework\AssertionFailedError" is thrown.', $result->failures()[0]->exceptionMessage());

        $result = (new AssertionFailedTestCase('testAssertionFailedWithBadMessage'))->run();
        $this->assertSame(1, $result->failureCount());
        $this->assertSame('Failed asserting that exception message \'Failed asserting that false is true.\' contains \'this is no true\'.', $result->failures()[0]->exceptionMessage());
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
        $this->expectExceptionMessage('Is this trait used by a class that extends `' . PHPUnitTestCase::class . '`?');
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
        $result = (new SkipTestCase('testSkipIfTrue'))->run();
        $this->assertSame(1, $result->skippedCount());

        $result = (new SkipTestCase('testSkipIfFalse'))->run();
        $this->assertSame(0, $result->skippedCount());
    }
}
