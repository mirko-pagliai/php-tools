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

namespace Tools\Test;

use App\ExampleClass;
use ErrorException;
use Exception;
use PHPUnit\Framework\Error\Notice;
use stdClass;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\MethodNotExistsException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotPositiveException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Exception\PropertyNotExistsException;
use Tools\Filesystem;
use Tools\NewExceptionist as Exceptionist;
use Tools\TestSuite\TestCase;

class NewExceptionistTest extends TestCase
{
    /**
     * Test for `__callStatic()` magic method
     * @uses \Tools\NewExceptionist::__callStatic()
     * @test
     */
    public function testCallStaticMagicMethod(): void
    {
        $function = fn() => '';
        $stream = stream_context_create();

        $this->assertSame(stdClass::class, Exceptionist::classExists(stdClass::class));
        $this->assertSame(TMP . 'noExisting', Exceptionist::fileNotExists(TMP . 'noExisting'));
        $this->assertSame(['a'], Exceptionist::isArray(['a']));
        $this->assertSame(true, Exceptionist::isBool(true));
        $this->assertSame($function, Exceptionist::isCallable($function));
        $this->assertSame(TMP, Exceptionist::isDir(TMP));
        $this->assertSame(1.4, Exceptionist::isFloat(1.4));
        $this->assertSame(1, Exceptionist::isInt(1));
        $this->assertSame([1], Exceptionist::isIterable([1]));
        $this->assertSame('string', Exceptionist::isNotArray('string'));
        $this->assertSame(null, Exceptionist::isNull(null));
        $this->assertEquals(new stdClass(), Exceptionist::isObject(new stdClass()));
        $this->assertSame(1, Exceptionist::isPositive(1));
        $this->assertSame($stream, Exceptionist::isResource($stream));
        $this->assertSame('string', Exceptionist::isString('string'));
        $this->assertSame('https://localhost', Exceptionist::isString('https://localhost'));

        foreach ([null, false, true, 1.2, 'd', []] as $value) {
            $this->assertException(fn() => Exceptionist::isInt($value), ErrorException::class, '`' . Exceptionist::class . '::isInt()` returned `false`');
        }

        foreach ([1, '1', 1.0] as $number) {
            $this->assertSame($number, Exceptionist::isPositive($number));
        }

        foreach ([null, false, -1, 'd', []] as $value) {
            $this->assertException(fn() => Exceptionist::isPositive($value), ErrorException::class, '`' . Exceptionist::class . '::isPositive()` returned `false`');
        }

        $this->assertException(fn() => Exceptionist::fileNotExists(tempnam(TMP, 'tmp') ?: ''), ErrorException::class, '`' . Exceptionist::class . '::fileNotExists()` returned `false`');
        $this->assertException(fn() => Exceptionist::isNotPositive(1), ErrorException::class, '`' . Exceptionist::class . '::isNotPositive()` returned `false`');
    }

    /**
     * Test for `__callStatic()` magic method, with a no existing PHP function
     * @uses \Tools\NewExceptionist::__callStatic()
     * @test
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testCallStaticMagicMethodWithNoExistingFunction(): void
    {
        $this->expectNotice();
        $this->expectNoticeMessage('Error calling `is_invalid_method()`: Function is_invalid_method() does not exist');
        Exceptionist::isInvalidMethod(1);
    }

    /**
     * Test for `__callStatic()` magic method, with an error from the PHP function
     * @uses \Tools\NewExceptionist::__callStatic()
     * @test
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testCallStaticMagicMethodWithErrorFromFunction(): void
    {
        $this->assertException(fn() => Exceptionist::arrayCombine('a'), Notice::class, 'Error calling `array_combine()`: array_combine() expects exactly 2 arguments, 1 given');

        $this->expectNotice();
        $this->expectNoticeMessageMatches('#^Error calling `array_combine\(\)`\:#');
        Exceptionist::arrayCombine('a');
    }

    /**
     * Test for `arrayKeyExists()` method
     * @uses \Tools\NewExceptionist::arrayKeyExists()
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
     * @uses \Tools\NewExceptionist::fileExists()
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
     * @uses \Tools\NewExceptionist::inArray()
     * @test
     */
    public function testInArray(): void
    {
        $this->assertSame('a', Exceptionist::inArray('a', ['a', 'b', 'c']));
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', 'c']), NotInArrayException::class, 'The value `a` does not exist in array `[\'b\', \'c\']`');

        //With a no-string-able array
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', true]), NotInArrayException::class, 'The value `a` does not exist in array');
        $this->assertException(fn() => Exceptionist::inArray('a', ['b', 'c'], '`a` is not in array'), NotInArrayException::class, '`a` is not in array');

        //With `null` value
        $this->assertSame(null, Exceptionist::inArray(null, ['string', null]));
        $this->assertException(fn() => Exceptionist::inArray(null, ['b', 'c']), NotInArrayException::class, 'The value does not exist in array `[\'b\', \'c\']`');
    }

    /**
     * Test for `isFalse()` method
     * @uses \Tools\NewExceptionist::isFalse()
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
     * Test for `isInstanceOf()` method
     * @uses \Tools\NewExceptionist::isInstanceOf()
     * @test
     */
    public function testIsInstanceOf(): void
    {
        $instance = new stdClass();
        $this->assertSame($instance, Exceptionist::isInstanceOf($instance, stdClass::class));

        $this->assertException(fn() => Exceptionist::isInstanceOf($instance, ExampleClass::class), ObjectWrongInstanceException::class, 'Object `stdClass` is not an instance of `App\ExampleClass`');
    }

    /**
     * Test for `isReadable()` method
     * @uses \Tools\NewExceptionist::isReadable()e
     * @test
     */
    public function testIsReadable(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->assertSame($file, Exceptionist::isReadable($file));

        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` is not readable');
        Exceptionist::isReadable(TMP . 'noExisting');
    }

    /**
     * Test for `isTrue()` method
     * @uses \Tools\NewExceptionist::isTrue()
     * @test
     */
    public function testIsTrue(): void
    {
        $this->assertTrue(Exceptionist::isTrue(true));
        $this->assertSame('string', Exceptionist::isTrue('string'));

        foreach ([
            [false, '`false` is not equal to `true`'],
            [null, '`null` is not equal to `true`'],
            [[], 'An empty array is not equal to `true`'],
            ['', 'An empty string is not equal to `true`'],
            [0, 'Value `0` is not equal to `true`'],
        ] as [$value, $expectedMessage]) {
            $this->assertException(fn() => Exceptionist::isTrue($value), ErrorException::class, $expectedMessage);
        }

        //With custom message and custom exception
        $this->assertException(fn() => Exceptionist::isTrue(false, 'your value is not true!', NotPositiveException::class), NotPositiveException::class, 'your value is not true!');
    }

    /**
     * Test for `isTrue()` method, with check on file and line
     * @uses \Tools\NewExceptionist::isTrue()
     * @test
     * @todo add test with another method
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function testIsTrueFileAndLine(): void
    {
        try {
            $line = __LINE__ + 1;
            Exceptionist::isTrue(false);
        } catch (ErrorException $e) {
            $this->assertSame(__FILE__, $e->getFile());
            $this->assertSame($line, $e->getLine());
        } finally {
            if (!isset($e)) {
                $this->fail();
            }
            unset($e);
        }

        try {
            $line = __LINE__ + 1;
            Exceptionist::inArray('a', ['b', 'c']);
        } catch (NotInArrayException $e) {
            $this->assertSame(__FILE__, $e->getFile());
            $this->assertSame($line, $e->getLine());
        } finally {
            if (!isset($e)) {
                $this->fail();
            }
        }
    }

    /**
     * Test for `isTrue()` method, with invalid exceptions
     * @uses \Tools\NewExceptionist::isTrue()
     * @test
     */
    public function testIsTrueWithInvalidException(): void
    {
        foreach ([Exception::class, stdClass::class, 'invalidString'] as $invalidException) {
            $this->assertException(fn() => Exceptionist::isTrue(false, '', $invalidException), Notice::class, 'The exception must be an `ErrorException` or must extend it');
        }
    }

    /**
     * Test for `isWritable()` method
     * @uses \Tools\NewExceptionist::isWritable()
     * @test
     */
    public function testIsWritable(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->assertSame($file, Exceptionist::isWritable($file));

        $this->expectException(NotWritableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` is not writable');
        Exceptionist::isWritable(TMP . 'noExisting');
    }

    /**
     * Test for `methodExists()` method
     * @uses \Tools\NewExceptionist::methodExists()
     * @test
     */
    public function testMethodExists(): void
    {
        foreach ([new ExampleClass(), ExampleClass::class] as $object) {
            $this->assertSame([$object, 'setProperty'], Exceptionist::methodExists($object, 'setProperty'));
        }

        $this->expectException(MethodNotExistsException::class);
        $this->expectExceptionMessage('Method `' . ExampleClass::class . '::noExisting()` does not exist');
        Exceptionist::methodExists($object, 'noExisting');
    }

    /**
     * Test for `objectPropertyExists()` method
     * @uses \Tools\NewExceptionist::objectPropertyExists()
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
}
