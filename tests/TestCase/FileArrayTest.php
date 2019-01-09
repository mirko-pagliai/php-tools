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

use Tools\Exception\KeyNotExistsException;
use Tools\FileArray;
use Tools\TestSuite\TestCase;

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
    protected $example = ['first', 'second', 'third', 'fourth', 'fifth'];

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->FileArray = new FileArray(safe_create_tmp_file(), $this->example);
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

        $this->expectException(KeyNotExistsException::class);
        $this->expectExceptionMessage('Key `noExisting` does not exist');
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

        $this->expectException(KeyNotExistsException::class);
        $this->expectExceptionMessage('Key `noExisting` does not exist');
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
        $this->assertEquals($this->example, $this->FileArray->read());

        //With invalid array or no existing file, in any case returns a empty array
        $this->assertEquals([], (new FileArray(safe_create_tmp_file('a string')))->read());
        $this->assertEquals([], (new FileArray(TMP . 'noExisting'))->read());
    }

    /**
     * Test for `take()` method
     * @test
     */
    public function testTake()
    {
        $result = $this->FileArray->take(2);
        $this->assertInstanceof(FileArray::class, $result);
        $this->assertEquals(['first', 'second'], $this->FileArray->read());
    }

    /**
     * Test for `take()` method, with `$from` argument
     * @test
     */
    public function testTakeWithFromArg()
    {
        $result = $this->FileArray->take(2, 3);
        $this->assertInstanceof(FileArray::class, $result);
        $this->assertEquals(['fourth', 'fifth'], $this->FileArray->read());
    }

    /**
     * Test for `write()` method
     * @test
     */
    public function testWrite()
    {
        $this->assertTrue($this->FileArray->write());
        $this->assertEquals($this->example, $this->FileArray->read());
    }
}
