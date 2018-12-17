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

        file_put_contents($this->exampleFile, 'a string');
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
        $this->assertNull(file_exists_or_fail($this->exampleFile));

        //Failure
        try {
            file_exists_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` does not exist', $e->getMessage());
        }
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
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
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        $this->assertNull(is_readable_or_fail($this->exampleFile));

        //Failure
        try {
            is_readable_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` is not readable', $e->getMessage());
        }
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFailure()
    {
        foreach (['string', ['array'], false, new stdClass, true, 1, 0.1] as $value) {
            try {
                $e = false;
                $result = is_true_or_fail($value);
            } catch (Exception $ex) {
            } finally {
                if ($e && $e instanceof ErrorException) {
                    $this->fail(sprintf('Exception was raised for `%s` value', $value));
                }

                $this->assertNull($result);
            }
        }

        foreach ([null, false, 0, '0', []] as $value) {
            try {
                $e = false;
                $result = is_true_or_fail($value);
            } catch (Exception $e) {
            } finally {
                if (!$e || !$e instanceof ErrorException) {
                    $this->fail(sprintf('Exception was not raised for `%s` value', $value));
                }

                $this->assertNull($result);
                $this->assertInstanceof(ErrorException::class, $e);
                $this->assertEquals('The value is not equal to `true`', $e->getMessage());
            }
        }

        try {
            $result = is_true_or_fail(false, '`false` is not `true`');
        } catch (Exception $e) {
        } finally {
            $this->assertNull($result);
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('`false` is not `true`', $e->getMessage());
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

        //Failure with a custom message and exception classes
        foreach ([RuntimeException::class, 'RuntimeException'] as $exceptionClass) {
            try {
                $result = is_true_or_fail(false, '`false` is not `true`', $exceptionClass);
            } catch (Exception $e) {
            } finally {
                $this->assertNull($result);
                $this->assertInstanceof(RuntimeException::class, $e);
                $this->assertEquals('`false` is not `true`', $e->getMessage());
            }
        }
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        $this->assertNull(is_writable_or_fail($this->exampleFile));

        //Failure
        try {
            is_writable_or_fail(TMP . 'noExisting');
        } catch (Exception $e) {
        } finally {
            $this->assertInstanceof(ErrorException::class, $e);
            $this->assertEquals('File or directory `' . TMP . 'noExisting` is not writable', $e->getMessage());
        }
    }
}
