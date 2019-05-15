<?php
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
use RuntimeException;
use stdClass;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotDirectoryException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\PropertyNotExistsException;
use Tools\TestSuite\TestCase;

/**
 * OrFailFunctionsTest class
 */
class OrFailFunctionsTest extends TestCase
{
    /**
     * Test for `file_exists_or_fail()` "or fail" function
     * @test
     */
    public function testFileExistsOrFail()
    {
        file_exists_or_fail(create_tmp_file());

        $this->expectException(FileNotExistsException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` does not exist');
        file_exists_or_fail(TMP . 'noExisting');
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        is_dir_or_fail(TMP);

        $filename = create_tmp_file();
        $this->expectException(NotDirectoryException::class);
        $this->expectExceptionMessage('Filename `' . $filename . '` is not a directory');
        is_dir_or_fail($filename);
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        is_readable_or_fail(create_tmp_file());

        $this->expectException(NotReadableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` is not readable');
        is_readable_or_fail(TMP . 'noExisting');
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        foreach (['string', ['array'], new stdClass(), true, 1, 0.1] as $value) {
            is_true_or_fail($value);
        }

        foreach ([null, false, 0, '0', []] as $value) {
            $this->assertException(ErrorException::class, function () use ($value) {
                is_true_or_fail($value);
            }, 'The value is not equal to `true`');
        }

        //Failure with a `null` message
        try {
            is_true_or_fail(false, null);
        } catch (Exception $e) {
        } finally {
            $this->assertEmpty($e->getMessage());
        }

        //Failure with a custom message
        $this->assertException(ErrorException::class, function () {
            is_true_or_fail(false, '`false` is not `true`');
        }, '`false` is not `true`');

        //Failure with custom message and exception class
        foreach ([RuntimeException::class, 'RuntimeException'] as $exceptionClass) {
            $this->assertException(RuntimeException::class, function () use ($exceptionClass) {
                is_true_or_fail(false, '`false` is not `true`', $exceptionClass);
            }, '`false` is not `true`');
        }

        //Failure with a custom exception class as second argument
        $this->assertException(RuntimeException::class, function () {
            is_true_or_fail(false, RuntimeException::class);
        });

        //Failures with bad exception classes
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, new stdClass());
        }, '`$exception` parameter must be a string');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, stdClass::class);
        }, '`stdClass` is not and instance of `Exception`');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, 'noExisting\Class');
        }, 'Class `noExisting\Class` does not exist');
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        is_writable_or_fail(create_tmp_file());

        $this->expectException(NotWritableException::class);
        $this->expectExceptionMessage('File or directory `' . TMP . 'noExisting` is not writable');
        is_writable_or_fail(TMP . 'noExisting');
    }

    /**
     * Test for `key_exists_or_fail()` "or fail" function
     * @test
     */
    public function testKeyExistsOrFail()
    {
        $array = ['a' => 'alfa', 'beta', 'gamma'];
        key_exists_or_fail('a', $array);
        key_exists_or_fail(['a', 1], $array);

        foreach ([
            'b',
            ['a', 'b'],
            ['b', 'c'],
        ] as $key) {
            $this->assertException(KeyNotExistsException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array);
            }, 'Key `b` does not exist');
        }
    }

    /**
     * Test for `property_exists_or_fail()` "or fail" function
     * @test
     */
    public function testPropertyExistsOrFail()
    {
        $object = new stdClass();
        $object->name = 'My name';
        property_exists_or_fail($object, 'name');

        property_exists_or_fail(new ExampleClass(), 'publicProperty');

        $object = $this->getMockBuilder(ExampleClass::class)
            ->setMethods(['has'])
            ->getMock();

        $object->expects($this->once())
            ->method('has')
            ->with('publicProperty')
            ->willReturn(true);

        property_exists_or_fail($object, 'publicProperty');

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new stdClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');
    }
}
