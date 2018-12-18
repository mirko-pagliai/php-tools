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
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

/**
 * OrFailFunctionsTest class
 */
class OrFailFunctionsTest extends TestCase
{
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
        $currentErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $this->assertNull(file_exists_or_fail($this->exampleFile));

        //Failure
        try {
            file_exists_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` does not exist', $e->getMessage());
        }

        error_reporting($currentErrorReporting);
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        $currentErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $this->assertNull(is_dir_or_fail(dirname($this->exampleFile)));

        //Failures
        try {
            is_dir_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` does not exist', $e->getMessage());
        }

        try {
            is_dir_or_fail($this->exampleFile);
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('`' . $this->exampleFile . '` is not a directory', $e->getMessage());
        }

        error_reporting($currentErrorReporting);
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        $currentErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $this->assertNull(is_readable_or_fail($this->exampleFile));

        //Failure
        try {
            is_readable_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` is not readable', $e->getMessage());
        }

        error_reporting($currentErrorReporting);
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        foreach (['string', ['array'], new stdClass, true, 1, 0.1] as $value) {
            try {
                $result = is_true_or_fail($value);
            } catch (Exception $e) {
                $this->fail($e->getMessage());
            } finally {
                $this->assertNull($result);
            }
        }

        foreach ([null, false, 0, '0', []] as $value) {
            try {
                $result = is_true_or_fail($value);
            } catch (Exception $e) {
            } finally {
                $this->assertNull($result);
                $this->assertInstanceof(ErrorException::class, $e);
                $this->assertEquals('The value is not equal to `true`', $e->getMessage());
            }
        }

        //Failure with a custom message
        try {
            $result = is_true_or_fail(false, '`false` is not `true`');
        } catch (Exception $e) {
        } finally {
            $this->assertNull($result);
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('`false` is not `true`', $e->getMessage());
        }

        //Failure with a custom message and exception class
        foreach ([RuntimeException::class, 'RuntimeException'] as $exceptionClass) {
            try {
                $result = is_true_or_fail(false, '`false` is not `true`', $exceptionClass);
            } catch (RuntimeException $e) {
            } finally {
                $this->assertNull($result);
                $this->assertInstanceof(RuntimeException::class, $e);
                $this->assertEquals('`false` is not `true`', $e->getMessage());
            }
        }

        //Failures with bad exception classes
        foreach ([
            [new stdClass, '`$exception` argument must be a string'],
            [stdClass::class, '`stdClass` is not and instance of `Exception`'],
            ['noExisting\Class', 'Class `noExisting\Class` does not exist'],
        ] as $exceptionClasses) {
            try {
                list($exceptionClass, $expectedMessage) = $exceptionClasses;
                $result = is_true_or_fail(false, null, $exceptionClass);
            } catch (Exception $e) {
            } finally {
                $this->assertNull($result);
                $this->assertInstanceof(Notice::class, $e);
                $this->assertEquals($expectedMessage, $e->getMessage());
            }
        }
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        $currentErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $this->assertNull(is_writable_or_fail($this->exampleFile));

        //Failure
        try {
            is_writable_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` is not writable', $e->getMessage());
        }

        error_reporting($currentErrorReporting);
    }
}
