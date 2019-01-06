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

use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotDirectoryException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\TestSuite\TestTrait;

/**
 * OrFailFunctionsTest class
 */
class OrFailFunctionsTest extends TestCase
{
    use TestTrait;

    /**
     * @var string
     */
    protected $exampleFile = TMP . 'exampleFile';

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        safe_create_file($this->exampleFile, 'a string');
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_unlink($this->exampleFile);
    }

    /**
     * Test for `file_exists_or_fail()` "or fail" function
     * @test
     */
    public function testFileExistsOrFail()
    {
        file_exists_or_fail($this->exampleFile);

        $this->assertException(FileNotExistsException::class, function () {
            file_exists_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` does not exist');
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
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        is_dir_or_fail(dirname($this->exampleFile));

        $this->assertException(NotDirectoryException::class, function () {
            is_dir_or_fail($this->exampleFile);
        }, 'Filename `' . $this->exampleFile . '` is not a directory');
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        is_readable_or_fail($this->exampleFile);

        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not readable');
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        foreach (['string', ['array'], new stdClass, true, 1, 0.1] as $value) {
            is_true_or_fail($value);
        }

        foreach ([null, false, 0, '0', []] as $value) {
            $this->assertException(ErrorException::class, function () use ($value) {
                is_true_or_fail($value);
            }, 'The value is not equal to `true`');
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
            is_true_or_fail(false, null, new stdClass);
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
        is_writable_or_fail($this->exampleFile);

        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` is not writable');
    }
}
