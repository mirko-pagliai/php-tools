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
use ErrorException;
use Exception;
use PHPUnit\Framework\Error\Notice;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotReadableException;
use Tools\Exception\PropertyNotExistsException;
use Tools\Exceptionist;
use Tools\TestSuite\TestCase;

/**
 * ExceptionistTest class
 */
class ExceptionistTest extends TestCase
{
    /**
     * Test for `__callStatic()` magic method
     * @test
     */
    public function testCallStaticMagicMethod()
    {
        $inArrayArgs = ['a', ['a', 'b', 'c']];
        $this->assertSame($inArrayArgs, Exceptionist::inArray($inArrayArgs));
        $this->assertSame($inArrayArgs, Exceptionist::inArray($inArrayArgs, '`a` is not in array', \LogicException::class));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('`false` is not equal to `true`');
        Exceptionist::inArray(['d', ['a', 'b', 'c']]);
    }

    /**
     * Test for `__callStatic()` magic method, with an error from the PHP function
     * @test
     */
    public function testCallStaticMagicMethodWithErrorFromFunction()
    {
        $this->expectException(Notice::class);
        $this->expectExceptionMessage('Error calling `in_array()`: in_array() expects at least 2 parameters, 1 given');
        Exceptionist::inArray(['a']);
    }

    /**
     * Test for `__callStatic()` magic method, with a no existing PHP function
     * @test
     */
    public function testCallStaticMagicMethodWithNoExistingFunction()
    {
        $this->expectException(Notice::class);
        $this->expectExceptionMessage('Function `not_existing_method()` does not exist');
        Exceptionist::notExistingMethod(1);
    }

    /**
     * Test for `arrayKeyExists()` method
     * @test
     */
    public function testArrayKeysExists()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertSame('a', Exceptionist::arrayKeyExists('a', $array));
        $this->assertSame(['a', 'c'], Exceptionist::arrayKeyExists(['a', 'c'], $array));

        $this->expectException(KeyNotExistsException::class);
        $this->expectExceptionMessage('Key `d` does not exist');
        Exceptionist::fileExists(Exceptionist::arrayKeyExists(['d'], $array));
    }

    /**
     * Test for `fileExists()` method
     * @test
     */
    public function testFileExists()
    {
        $file = create_tmp_file();
        $this->assertSame($file, Exceptionist::fileExists($file));

        $this->expectException(FileNotExistsException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::fileExists(TMP . 'noExisting');
    }

    /**
     * Test for `isReadable()` method
     * @test
     */
    public function testIsReadable()
    {
        $file = create_tmp_file();
        $this->assertSame($file, Exceptionist::isReadable($file));

        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::isReadable(TMP . 'noExisting');
    }

    /**
     * Test for `isWritable()` method
     * @test
     */
    public function testIsWritable()
    {
        $file = create_tmp_file();
        $this->assertSame($file, Exceptionist::isWritable($file));

        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        Exceptionist::isWritable(TMP . 'noExisting');
    }

    /**
     * Test for `objectPropertyExists()` method
     * @test
     */
    public function testObjectPropertyExists()
    {
        $this->assertSame('publicProperty', Exceptionist::objectPropertyExists(new ExampleClass(), 'publicProperty'));

        $object = new ExampleClass();
        $object->name = 'My name';
        $object->surname = 'My surname';
        $this->assertSame('name', Exceptionist::objectPropertyExists($object, 'name'));
        $this->assertSame(['name', 'surname'], Exceptionist::objectPropertyExists($object, ['name', 'surname']));

        $object = $this->getMockBuilder(ExampleClass::class)
            ->setMethods(['has'])
            ->getMock();

        $object->expects($this->once())
            ->method('has')
            ->with('publicProperty')
            ->willReturn(true);

        $this->assertSame('publicProperty', Exceptionist::objectPropertyExists($object, 'publicProperty'));

        $this->expectException(PropertyNotExistsException::class);
        $this->expectExceptionMessage('Object does not have `noExisting` property');
        Exceptionist::objectPropertyExists(new ExampleClass(), 'noExisting');
    }

    /**
     * Test for `isTrue()` method
     * @test
     */
    public function testIsTrue()
    {
        $this->assertTrue(Exceptionist::isTrue(true));
        $this->assertSame('string', Exceptionist::isTrue('string'));

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('`false` is not equal to `true`');
        Exceptionist::isTrue(false);
    }

    /**
     * Test for `isTrue()` method, with some failure values
     * @test
     */
    public function testIsTrueWithFailureValues()
    {
        foreach ([
            [null, '`null` is not equal to `true`'],
            [[], 'An empty array is not equal to `true`'],
            ['', 'An empty string is not equal to `true`'],
            [0, 'Value `0` is not equal to `true`'],
        ] as $exception) {
            [$value, $expectedMessage] = $exception;
            $this->assertException(Exception::class, function () use ($value) {
                Exceptionist::isTrue($value);
            }, $expectedMessage);
        }
    }

    /**
     * Test for `isTrue()` method, with custom message and custom exception
     * @test
     */
    public function testIsTrueFailureWithCustomMessageAndCustomException()
    {
        $message = 'it\'s not `true`';

        $this->assertException(Exception::class, function () use ($message) {
            Exceptionist::isTrue(false, $message);
        }, $message);

        $this->assertException(ErrorException::class, function () use ($message) {
            Exceptionist::isTrue(false, new \ErrorException($message));
        }, $message);

        $this->assertException(ErrorException::class, function () use ($message) {
            Exceptionist::isTrue(false, $message, ErrorException::class);
        }, $message);
    }
}
