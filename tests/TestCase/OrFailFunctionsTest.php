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

use PHPUnit\Framework\TestCase;

/**
 * OrFailFunctionsTest class
 */
class OrFailFunctionsTest extends TestCase
{
    /**
     * @var string
     */
    protected $exampleFile;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->exampleFile = TMP . 'exampleFile';
        file_put_contents($this->exampleFile, null);
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
    }

    /**
     * Test for `file_exists_or_fail()` "or fail" function, with a failure
     * @expectedException ErrorException
     * @expectedExceptionMessage File or directory `/tmp/php-tools/noExisting` does not exist
     * @test
     */
    public function testFileExistsOrFailWithFailure()
    {
        file_exists_or_fail(TMP . 'noExisting');
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        $this->assertNull(is_readable_or_fail($this->exampleFile));
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function, with a failure
     * @expectedException ErrorException
     * @expectedExceptionMessage File or directory `/tmp/php-tools/noExisting` is not readable
     * @test
     */
    public function testIsReadableOrFailWithFailure()
    {
        is_readable_or_fail(TMP . 'noExisting');
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        $this->assertNull(is_writable_or_fail($this->exampleFile));
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function, with a failure
     * @expectedException ErrorException
     * @expectedExceptionMessage File or directory `/tmp/php-tools/noExisting` is not writable
     * @test
     */
    public function testIsWritableOrFailWithFailure()
    {
        is_writable_or_fail(TMP . 'noExisting');
    }
}