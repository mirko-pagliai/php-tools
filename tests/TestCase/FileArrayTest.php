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
use Tools\FileArray;

/**
 * FileArrayTest class
 */
class FileArrayTest extends TestCase
{
    /**
     * @var \Tools\FileArray
     */
    protected $FileArray;

    /**
     * @var array
     */
    protected $example;

    /**
     * @var string
     */
    protected $file;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->file = TMP . 'file_array_test';
        $this->example = ['first', 'second', 'third', 'fourth', 'fifth'];
        $this->FileArray = new FileArray($this->file, $this->example);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_unlink($this->file);
    }

    /**
     * Test for `__construct()` method, using a not writable file
     * @expectedException Exception
     * @expectedExceptionMessageRegExp /^File or directory `[\w\d\:\/\-\\]+noExistingDir` is not writable$/
     * @test
     */
    public function testConstructNoWritableFile()
    {
        new FileArray(TMP . 'noExistingDir' . DS . 'noExistingFile');
    }

    /**
     * Test for `__construct()` method, with a file that already exists
     * @test
     */
    public function testConstructFileAlreadyExists()
    {
        file_put_contents($this->file, null);

        $this->assertEquals([], (new FileArray($this->file))->read());
    }

    /**
     * Test for `append()` method
     * @test
     */
    public function testAppend()
    {
        $result = $this->FileArray->append('last');
        $this->assertInstanceof(FileArray::class, $result);
        $this->assertEquals(array_merge($this->example, ['last']), $this->FileArray->read());
    }

    /**
     * Test for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $result = $this->FileArray->delete(1)->delete(2);
        $this->assertInstanceof(FileArray::class, $result);
        $this->assertEquals(['first', 'third', 'fifth'], $this->FileArray->read());
    }

    /**
     * Test for `delete()` method, with a no existing key
     * @expectedException Exception
     * @expectedExceptionMessage Key `noExisting` does not exist
     * @test
     */
    public function testDeleteNoExistingKey()
    {
        $this->FileArray->delete('noExisting');
    }

    /**
     * Test for `exists()` method
     * @test
     */
    public function testExists()
    {
        $this->assertTrue($this->FileArray->exists(0));
        $this->assertFalse($this->FileArray->exists(100));
    }

    /**
     * Test for `get()` method
     * @test
     */
    public function testGet()
    {
        $this->assertEquals('first', $this->FileArray->get(0));
        $this->assertEquals('third', $this->FileArray->get(2));
    }

    /**
     * Test for `get()` method, with a no existing key
     * @expectedException Exception
     * @expectedExceptionMessage Key `noExisting` does not exist
     * @test
     */
    public function testGetNoExistingKey()
    {
        $this->FileArray->get('noExisting');
    }

    /**
     * Test for `prepend()` method
     * @test
     */
    public function testPrepend()
    {
        $result = $this->FileArray->prepend('anotherFirst');
        $this->assertInstanceof(FileArray::class, $result);
        $this->assertEquals(array_merge(['anotherFirst'], $this->example), $this->FileArray->read());
    }

    /**
     * Test for `read()` method
     * @test
     */
    public function testRead()
    {
        $this->assertNotEmpty($this->FileArray->read());

        //With invalid array, in any case returns a empty array
        file_put_contents($this->file, 'a string');
        $this->assertEquals([], (new FileArray($this->file))->read());
    }

    /**
     * Test for `write()` method
     * @test
     */
    public function testWrite()
    {
        $result = $this->FileArray->write();
        $this->assertTrue($result);
        $this->assertEquals('["first","second","third","fourth","fifth"]', file_get_contents($this->file));
    }
}
