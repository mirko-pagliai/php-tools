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
use Exception;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotDirectoryException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotPositiveException;
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
        $file = create_tmp_file();
        $this->assertSame($file, file_exists_or_fail($file));

        $this->assertException(FileNotExistsException::class, function () {
            file_exists_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` does not exist');
        $this->assertException(FileNotExistsException::class, function () {
            file_exists_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            file_exists_or_fail(TMP . 'noExisting', new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `in_array_or_fail()` "or fail" function
     * @test
     */
    public function testInArrayOrFail()
    {
        $this->assertSame('a', in_array_or_fail('a', ['a', 'b']));

        $this->assertException(NotInArrayException::class, function () {
            in_array_or_fail('a', []);
        }, 'The value `a` is not in array');
        $this->assertException(NotInArrayException::class, function () {
            in_array_or_fail('a', [], 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            in_array_or_fail('a', [], new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        $this->assertSame(TMP, is_dir_or_fail(TMP));

        $filename = create_tmp_file();

        $this->assertException(NotDirectoryException::class, function () use ($filename) {
            is_dir_or_fail($filename);
        }, 'Filename `' . $filename . '` is not a directory');
        $this->assertException(NotDirectoryException::class, function () use ($filename) {
            is_dir_or_fail($filename, 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () use ($filename) {
            is_dir_or_fail($filename, new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_positive_or_fail()` "or fail" function
     * @test
     */
    public function testIsPositiveOrFail()
    {
        $this->assertSame(1, is_positive_or_fail(1));
        $this->assertSame('1', is_positive_or_fail('1'));

        $this->assertException(NotPositiveException::class, function () {
            is_positive_or_fail(-1);
        }, 'The value `-1` is not a positive');
        $this->assertException(NotPositiveException::class, function () {
            is_positive_or_fail(-1, 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            is_positive_or_fail(-1, new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        $file = create_tmp_file();
        $this->assertSame($file, is_readable_or_fail($file));

        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not readable');
        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            is_readable_or_fail(TMP . 'noExisting', new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        foreach (['string', ['array'], new ExampleClass(), true, 1, 0.1] as $value) {
            $this->assertSame($value, is_true_or_fail($value));
        }

        foreach ([null, false, 0, '0', []] as $value) {
            $this->assertException(Exception::class, function () use ($value) {
                is_true_or_fail($value);
            }, 'The value is not equal to `true`');
        }

        //Failure with an empty string message
        try {
            is_true_or_fail(false, '');
        } catch (Exception $e) {
            $this->assertEmpty($e->getMessage());
        } finally {
            if (!isset($e)) {
                self::fail('No exception throw');
            }
        }

        //Failure with a custom message
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, '`false` is not `true`');
        }, '`false` is not `true`');

        //Failure with custom message and exception class string
        foreach ([Exception::class, 'Exception'] as $exceptionClass) {
            $this->assertException(Exception::class, function () use ($exceptionClass) {
                is_true_or_fail(false, '`false` is not `true`', $exceptionClass);
            }, '`false` is not `true`');
        }

        //Failure with a custom exception class string as second argument
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, Exception::class);
        });

        //Failure with custom message and an instantiated exception
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, new Exception('an exception'));
        }, 'an exception');

        //Failures with bad exception classes
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, new ExampleClass());
        }, '`$exception` parameter must be a string');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, null, ExampleClass::class);
        }, '`App\ExampleClass` is not and instance of `Throwable`');
        $this->assertException(Exception::class, function () {
            is_true_or_fail(false, '', 'noExisting\Class');
        }, 'Class `noExisting\Class` does not exist');
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        $file = create_tmp_file();
        $this->assertSame($file, is_writable_or_fail($file));

        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not writable');
        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            is_writable_or_fail(TMP . 'noExisting', new Exception('an exception'));
        }, 'an exception');
    }

    /**
     * Test for `key_exists_or_fail()` "or fail" function
     * @test
     */
    public function testKeyExistsOrFail()
    {
        $array = ['a' => 'alfa', 'beta', 'gamma'];
        $this->assertSame('a', key_exists_or_fail('a', $array));
        $this->assertSame(['a', 1], key_exists_or_fail(['a', 1], $array));

        foreach ([
            'b',
            ['a', 'b'],
            ['b', 'c'],
        ] as $key) {
            $this->assertException(KeyNotExistsException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array);
            }, 'Key `b` does not exist');
            $this->assertException(KeyNotExistsException::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array, 'an exception');
            }, 'an exception');
            $this->assertException(Exception::class, function () use ($array, $key) {
                key_exists_or_fail($key, $array, new Exception('an exception'));
            }, 'an exception');
        }
    }

    /**
     * Test for `property_exists_or_fail()` "or fail" function
     * @test
     */
    public function testPropertyExistsOrFail()
    {
        $this->assertSame('publicProperty', property_exists_or_fail(new ExampleClass(), 'publicProperty'));

        $object = new ExampleClass();
        $object->name = 'My name';
        $this->assertSame('name', property_exists_or_fail($object, 'name'));

        $object = $this->getMockBuilder(ExampleClass::class)
            ->setMethods(['has'])
            ->getMock();

        $object->expects($this->once())
            ->method('has')
            ->with('publicProperty')
            ->willReturn(true);

        $this->assertSame('publicProperty', property_exists_or_fail($object, 'publicProperty'));

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting');
        }, 'Object does not have `noExisting` property');
        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting', 'an exception');
        }, 'an exception');
        $this->assertException(Exception::class, function () {
            property_exists_or_fail(new ExampleClass(), 'noExisting', new Exception('an exception'));
        }, 'an exception');
    }
}
