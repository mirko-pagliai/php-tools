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

use App\ExampleClass;
use BadMethodCallException;
use ErrorException;
use Exception;
use PHPUnit\Framework\Error\Notice;
use stdClass;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Exception\PropertyNotExistsException;
use Tools\Exceptionist;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;

/**
 * ExceptionistTest class
 */
class ExceptionistTest extends TestCase
{
    /**
     * Test to verify that the exceptions thrown by the `Exceptionist` report
     *  the correct file and line
     * @test
     */
    public function testLineAndFile(): void
    {
        try {
            $line = __LINE__ + 1;
            Exceptionist::isTrue(false);
        } catch (ErrorException $e) {
        } finally {
            $this->assertSame(__FILE__, $e->getFile());
            $this->assertSame($line, $e->getLine());
            unset($e);
        }

        try {
            $line = __LINE__ + 1;
            Exceptionist::isReadable(DS . 'noExisting');
        } catch (ErrorException $e) {
        } finally {
            $this->assertSame(__FILE__, $e->getFile());
            $this->assertSame($line, $e->getLine());
        }
    }

    /**
     * Test for `__callStatic()` magic method
     * @test
     */
    public function testCallStaticMagicMethod(): void
    {
        $function = fn() => '';
        $stream = stream_context_create();

        $this->assertSame(true, Exceptionist::isBool(true));
        $this->assertSame($function, Exceptionist::isCallable($function));
        $this->assertSame(1.4, Exceptionist::isFloat(1.4));
        $this->assertSame(1, Exceptionist::isInt(1));
        $this->assertSame([1], Exceptionist::isIterable([1]));
        $this->assertSame(null, Exceptionist::isNull(null));
        $this->assertSame($stream, Exceptionist::isResource($stream));
        $this->assertEquals(new stdClass(), Exceptionist::isObject(new stdClass()));

        foreach ([null, false, true, 1.2, 'd', []] as $var) {
            $this->assertException(fn() => Exceptionist::isInt($var), Exception::class, '`' . Exceptionist::class . '::isInt()` returned `false`');
        }

        foreach ([1, '1', 1.0] as $number) {
            $this->assertSame($number, Exceptionist::isPositive($number));
        }

        foreach ([null, false, -1, 'd', []] as $var) {
            $this->assertException(fn() => Exceptionist::isPositive($var), Exception::class, '`' . Exceptionist::class . '::isPositive()` returned `false`');
        }
    }

    /**
     * Test for `__callStatic()` magic method, with an error from the PHP function
     * @test
     */
    public function testCallStaticMagicMethodWithErrorFromFunction(): void
    {
        $this->expectNotice();
        $this->expectExceptionMessageMatches('#^Error calling `array_combine\(\)`\:#');
        /** @noinspection PhpUndefinedMethodInspection */
        Exceptionist::arrayCombine(['a', 'b']);
    }

    /**
     * Test for `__callStatic()` magic method, containing with the "Not" word
     * @test
     */
    public function testCallStaticMagicMethodWithNotWord(): void
    {
        $this->assertSame(TMP . 'noExisting', Exceptionist::fileNotExists(TMP . 'noExisting'));
        $this->assertSame('string', Exceptionist::isNotArray('string'));

        $this->assertException(fn() => Exceptionist::fileNotExists(tempnam(TMP, 'tmp') ?: ''), Exception::class, '`' . Exceptionist::class . '::fileNotExists()` returned `false`');

        $this->assertException(fn() => Exceptionist::isNotPositive(1), Exception::class, '`' . Exceptionist::class . '::isNotPositive()` returned `false`');
    }

    /**
     * Test for `__callStatic()` magic method, with a no existing PHP function
     * @test
     */
    public function testCallStaticMagicMethodWithNoExistingFunction(): void
    {
        $this->expectNotice();
        $this->expectExceptionMessage('Function `not_existing_method()` does not exist');
        /** @noinspection PhpUndefinedMethodInspection */
        Exceptionist::notExistingMethod(1);
    }

    /**
     * Test for `arrayKeyExists()` method
     * @test
     */
    public function testArrayKeysExists(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 4 => 4];
        $this->assertSame('a', Exceptionist::arrayKeyExists('a', $array));
        $this->assertSame(['a', 4], Exceptionist::arrayKeyExists(['a', 4], $array));

        $this->expectException(KeyNotExistsException::class);
        $this->expectExceptionMessage('Key `5` does not exist');
        Exceptionist::arrayKeyExists([5], $array);
    }

    /**
     * Test for `fileExists()` method
     * @test
     */
    public function testFileExists(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->assertSame($file, Exceptionist::fileExists($file));

        $this->expectException(FileNotExistsException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::fileExists(TMP . 'noExisting');
    }

    /**
     * Test for `inArray()` method
     * @uses \Tools\Exceptionist::inArray()
     * @test
     */
    public function testInArray(): void
    {
        $this->assertSame('a', Exceptionist::inArray('a', ['a', 'b', 'c']));
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', 'c']), NotInArrayException::class, 'The value `a` does not exist in array `[\'b\', \'c\']`');

        //With a no-stringable array
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', true]), NotInArrayException::class, 'The value `a` does not exist in array');
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', 'c'], '`a` is not in array', ErrorException::class), ErrorException::class, '`a` is not in array');

        //With `null` value
        $this->assertSame(null, Exceptionist::inArray(null, ['string', null]));
        $this->assertException(fn() => Exceptionist::inArray(null, ['b', 'c']), NotInArrayException::class, 'The value does not exist in array `[\'b\', \'c\']`');
    }

    /**
     * Test for `isInstanceOf()` method
     * @test
     */
    public function testInstanceOf(): void
    {
        $instance = new stdClass();
        $this->assertSame($instance, Exceptionist::isInstanceOf($instance, stdClass::class));

        $this->expectException(ObjectWrongInstanceException::class);
        $this->expectExceptionMessage('`stdClass` is not an instance of `App\ExampleClass`');
        Exceptionist::isInstanceOf($instance, ExampleClass::class);
    }

    /**
     * Test for `isFalse()` method
     * @test
     */
    public function testIsFalse(): void
    {
        $this->assertSame(false, Exceptionist::isFalse(false));
        $this->assertSame(0, Exceptionist::isFalse(0));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('`false` is not equal to `true`');
        Exceptionist::isFalse(true);
    }

    /**
     * Test for `isReadable()` method
     * @test
     */
    public function testIsReadable(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->assertSame($file, Exceptionist::isReadable($file));

        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::isReadable(TMP . 'noExisting');
    }

    /**
     * Test for `isWritable()` method
     * @test
     */
    public function testIsWritable(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->assertSame($file, Exceptionist::isWritable($file));

        $this->expectException(NotWritableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::isWritable(TMP . 'noExisting');
    }

    /**
     * Test for `methodExists()` method
     * @test
     */
    public function testMethodExists(): void
    {
        foreach ([new ExampleClass(), ExampleClass::class] as $object) {
            $this->assertSame([ExampleClass::class, 'setProperty'], Exceptionist::methodExists($object, 'setProperty'));
        }

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method `' . ExampleClass::class . '::noExisting()` does not exist');
        Exceptionist::methodExists($object, 'noExisting');
    }

    /**
     * Test for `objectPropertyExists()` method
     * @test
     */
    public function testObjectPropertyExists(): void
    {
        $this->assertSame('publicProperty', Exceptionist::objectPropertyExists(new ExampleClass(), 'publicProperty'));

        $object = new stdClass();
        $object->name = 'My name';
        $object->surname = 'My surname';
        $this->assertSame('name', Exceptionist::objectPropertyExists($object, 'name'));
        $this->assertSame(['name', 'surname'], Exceptionist::objectPropertyExists($object, ['name', 'surname']));

        $Mock = $this->createMock(ExampleClass::class);
        $Mock->expects($this->once())
            ->method('has')
            ->with('publicProperty')
            ->willReturn(true);

        $this->assertSame('publicProperty', Exceptionist::objectPropertyExists($Mock, 'publicProperty'));

        $this->expectException(PropertyNotExistsException::class);
        $this->expectExceptionMessage('Property `' . ExampleClass::class . '::$noExisting` does not exist');
        Exceptionist::objectPropertyExists(new ExampleClass(), 'noExisting');
    }

    /**
     * Test for `isTrue()` method
     * @uses \Tools\Exceptionist::isTrue()
     * @test
     */
    public function testIsTrue(): void
    {
        $this->assertTrue(Exceptionist::isTrue(true));
        $this->assertSame('string', Exceptionist::isTrue('string'));

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('`false` is not equal to `true`');
        Exceptionist::isTrue(false);
    }

    /**
     * Test for `isTrue()` method, with some failure values
     * @uses \Tools\Exceptionist::isTrue()
     * @test
     */
    public function testIsTrueWithFailureValues(): void
    {
        foreach ([
            [null, '`null` is not equal to `true`'],
            [[], 'An empty array is not equal to `true`'],
            ['', 'An empty string is not equal to `true`'],
            [0, 'Value `0` is not equal to `true`'],
        ] as $exception) {
            [$value, $expectedMessage] = $exception;
            $this->assertException(fn() => Exceptionist::isTrue($value), Exception::class, $expectedMessage);
        }
    }

    /**
     * Test for `isTrue()` method, with custom message and custom exception
     * @uses \Tools\Exceptionist::isTrue()
     * @test
     */
    public function testIsTrueFailureWithCustomMessageAndCustomException(): void
    {
        $message = 'it\'s not `true`';
        $this->assertException(fn() => Exceptionist::isTrue(false, $message), Exception::class, $message);
        $this->assertException(fn() => Exceptionist::isTrue(false, '', new ErrorException($message)), ErrorException::class, $message);
    }

    /**
     * Test for `isTrue()` method, with an invalid exception class
     * @uses \Tools\Exceptionist::isTrue()
     * @test
     */
    public function testIsTrueFailureWithInvalidExceptionClass(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertException(fn() => Exceptionist::isTrue(false, '', new stdClass()), Notice::class, '`$exception` parameter must be an instance of `Exception` or a class string');
    }
}
