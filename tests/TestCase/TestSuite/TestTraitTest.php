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
use stdClass;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;

/**
 * TestTraitTest class
 */
class TestTraitTest extends TestCase
{
    /**
     * Test for `__call()` and `__callStatic()` magic methods
     * @test
     */
    public function testMagicCallAndCallStatic(): void
    {
        $function = fn() => '';
        //Methods that use the `assertInternalType()` method
        foreach ([
            'assertIsArray' => ['array'],
            'assertIsBool' => true,
            'assertIsCallable' => $function,
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
            $this->{$assertMethod}($value);
            self::{$assertMethod}($value);
        }

        //Missing argument
        $this->assertException(
            [$this, 'assertIsJson'],
            BadMethodCallException::class,
            'Method ' . get_parent_class($this) . '::assertIsJson() expects at least 1 argument, maximum 2, 0 passed'
        );

        //Calling a no existing method or a no existing "assertIs" method
        foreach (['assertIsNoExistingType', 'noExistingMethod'] as $method) {
            $this->assertException(fn() => $this->$method('string'), BadMethodCallException::class, 'Method ' . get_parent_class($this) . '::' . $method . '() does not exist');
        }
    }

    /**
     * Tests for `assertArrayKeysEqual()` method
     * @test
     */
    public function testAssertArrayKeysEqual(): void
    {
        $this->assertArrayKeysEqual([], []);

        foreach ([
            ['key1' => 'value1', 'key2' => 'value2'],
            ['key2' => 'value2', 'key1' => 'value1'],
        ] as $array) {
            $this->assertArrayKeysEqual(['key1', 'key2'], $array);
        }

        $this->assertArrayKeysEqual([0, 1, 2], ['first', 'second', 'third']);

        $this->expectException(AssertionFailedError::class);
        $this->assertArrayKeysEqual(['key2'], $array);
    }

    /**
     * Tests for `assertException()` method
     * @uses \Tools\TestSuite\TestTrait::assertException()
     * @test
     */
    public function testAssertException(): void
    {
        $this->assertException(function () {
            throw new Exception();
        });
        $this->assertException(function () {
            throw new Exception('right exception message');
        }, Exception::class, 'right exception message');

        //It correctly ignores the deprecations
        $this->assertException(function () {
            deprecationWarning('This is a deprecation!');
            throw new ErrorException('This is an error exception');
        }, ErrorException::class, 'This is an error exception');

        //No exception throw
        try {
            $this->assertException('time');
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected exception `Exception`, but no exception throw', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail('No exception throw');
            }
            unset($e);
        }

        //No existing exception or invalid exception class
        foreach (['noExistingException', stdClass::class] as $class) {
            try {
                $this->assertException(function () {
                    throw new Exception();
                }, $class);
            } catch (AssertionFailedError $e) {
                $this->assertStringStartsWith('Class `' . $class . '` does not exist or is not an exception', $e->getMessage());
            } finally {
                if (!isset($e)) {
                    self::fail('No exception throw');
                }
                unset($e);
            }
        }

        //Unexpected exception type
        try {
            $this->assertException(function () {
                throw new Exception();
            }, BadMethodCallException::class);
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected exception `' . BadMethodCallException::class . '`, unexpected type `Exception`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail('No exception throw');
            }
            unset($e);
        }

        //Wrong exception message
        try {
            $this->assertException(function () {
                throw new Exception('Wrong');
            }, Exception::class, 'Right');
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected message exception `Right`, unexpected message `Wrong`', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail('No exception throw');
            }
            unset($e);
        }

        //Expected exception message, but no message
        try {
            $this->assertException(function () {
                throw new Exception();
            }, Exception::class, 'Right');
        } catch (AssertionFailedError $e) {
            $this->assertStringStartsWith('Expected message exception `Right`, but no message for the exception', $e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail('No exception throw');
            }
            unset($e);
        }
    }

    /**
     * Test for `assertFileExtension()` method
     * @test
     */
    public function testAssertFileExtension(): void
    {
        $this->assertFileExtension('jpg', 'file.jpg');
        $this->assertFileExtension('jpeg', 'FILE.JPEG');
        $this->assertFileExtension(['jpg', 'jpeg'], 'file.jpg');
    }

    /**
     * Test for `assertFileMime()` method
     * @test
     */
    public function testAssertFileMime(): void
    {
        $file = Filesystem::instance()->createTmpFile('string');
        $this->assertFileMime('text/plain', $file);
        $this->assertFileMime(['text/plain', 'inode/x-empty'], $file);
    }

    /**
     * Test for `assertImageSize()` method
     * @test
     */
    public function testAssertImageSize(): void
    {
        $resource = imagecreatetruecolor(120, 20);
        if (!$resource instanceof GdImage && !is_resource($resource)) {
            $this->fail('Unable to create a valid resource image');
        }
        imagejpeg($resource, TMP . 'pic.jpg');
        $this->assertImageSize(120, 20, TMP . 'pic.jpg');
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method
     * @test
     */
    public function testAssertIsArrayNotEmpty(): void
    {
        $this->assertIsArrayNotEmpty(['value']);

        foreach ([
            [],
            [[]],
            [false],
            [null],
            [''],
        ] as $array) {
            $this->assertException(fn() => $this->assertIsArrayNotEmpty($array), AssertionFailedError::class);
        }
    }

    /**
     * Tests for `assertIsMock()` method
     * @test
     */
    public function testAssertIsMock(): void
    {
        $mock = $this->getMockBuilder(stdClass::class)->getMock();
        $this->assertIsMock($mock);

        $this->expectAssertionFailed('Failed asserting that a `stdClass` object is a mock');
        $this->assertIsMock(new stdClass());
    }

    /**
     * Tests for `assertObjectPropertiesEqual()` method
     * @test
     */
    public function testAssertObjectPropertiesEqual(): void
    {
        $object = new stdClass();
        $object->first = 'first value';
        $object->second = 'second value';
        $this->assertObjectPropertiesEqual(['first', 'second'], $object);
        $this->assertObjectPropertiesEqual(['second', 'first'], $object);

        $this->expectException(AssertionFailedError::class);
        $this->assertObjectPropertiesEqual(['first'], $object);
    }

    /**
     * Test for `assertSameMethods()` method
     * @test
     */
    public function testAssertSameMethods(): void
    {
        $exampleClass = new ExampleClass();
        $this->assertSameMethods($exampleClass, ExampleClass::class);
        $this->assertSameMethods($exampleClass, get_class($exampleClass));

        $copyExampleClass = &$exampleClass;
        $this->assertSameMethods($exampleClass, $copyExampleClass);

        $this->assertSameMethods(ExampleChildClass::class, AnotherExampleChildClass::class);

        $this->expectException(AssertionFailedError::class);
        $this->assertSameMethods(ExampleClass::class, AnotherExampleChildClass::class);
    }

    /**
     * Test for `expectAssertionFailed()` method
     * @test
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
     * Test for `skipIf()` method
     * @test
     */
    public function testSkipIf(): void
    {
        $result = (new SkipTestCase('testSkipIfTrue'))->run();
        $this->assertSame(1, $result->skippedCount());

        $result = (new SkipTestCase('testSkipIfFalse'))->run();
        $this->assertSame(0, $result->skippedCount());
    }
}
