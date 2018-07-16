<?php
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Tools\Test\TestSuite;

use App\ExampleClass;
use App\ExampleOfTraversable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tools\TestSuite\TestCaseTrait;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    use TestCaseTrait;

    /**
     * Tests for `assertArrayKeysEqual()` method
     * @test
     */
    public function testAssertArrayKeysEqual()
    {
        $array = ['key1' => 'value1', 'key2' => 'value2'];
        $this->assertArrayKeysEqual(['key1', 'key2'], $array);
    }

    /**
     * Tests for `assertFileExists()` method
     * @test
     */
    public function testAssertFileExists()
    {
        $files = [tempnam(TMP, 'foo'), tempnam(TMP, 'foo2')];

        //As string, array and `Traversable`
        $this->assertFileExists($files[0]);
        $this->assertFileExists($files);
        $this->assertFileExists(new ExampleOfTraversable($files));

        safe_unlink($files[0]);
        safe_unlink($files[1]);
    }

    /**
     * Test for `assertFileExtension()` method
     * @ŧest
     */
    public function testAssertFileExtension()
    {
        foreach ([
            'jpg' => 'file.jpg',
            'jpg' => 'file.JPG',
            'jpeg' => 'file.jpeg',
            'jpg' => 'path/to/file.jpg',
            'jpg' => '/full/path/to/file.jpg',
        ] as $extension => $filename) {
            $this->assertFileExtension($extension, $filename);
        }
    }

    /**
     * Test for `assertImageSize()` method
     * @ŧest
     */
    public function testAssertImageSize()
    {
        $this->assertImageSize(COMPARING_FILES . '400x400.jpg', 400, 400);
    }

    /**
     * Test for `assertFileMime()` method
     * @ŧest
     */
    public function testAssertFileMime()
    {
        //@codingStandardsIgnoreLine
        $filename = tempnam(TMP, 'test_file.txt');
        file_put_contents($filename, 'this is a test file');

        $this->assertFileMime($filename, 'text/plain');

        safe_unlink($filename);
    }

    /**
     * Tests for `assertFileNotExists()` method
     * @test
     */
    public function testAssertFileNotExists()
    {
        $files = [TMP . 'noExisting1', TMP . 'noExisting2'];

        //As string, array and `Traversable`
        $this->assertFileNotExists($files[0]);
        $this->assertFileNotExists($files);
        $this->assertFileNotExists(new ExampleOfTraversable($files));
    }

    /**
     * Tests for `assertFilePerms()` method
     * @group onlyUnix
     * @test
     */
    public function testAssertFilePerms()
    {
        $files = [tempnam(TMP, 'foo'), tempnam(TMP, 'foo2')];

        //As string, array and `Traversable`
        $this->assertFilePerms($files[0], '0600');
        $this->assertFilePerms($files[0], ['0600', '0666']);
        $this->assertFilePerms($files, '0600');
        $this->assertFilePerms($files, ['0600', '0666']);
        $this->assertFilePerms(new ExampleOfTraversable($files), '0600');
        $this->assertFilePerms(new ExampleOfTraversable($files), ['0600', '0666']);

        safe_unlink($files[0]);
        safe_unlink($files[1]);
    }

    /**
     * Tests for `assertInstanceOf` method
     * @test
     */
    public function testAssertInstanceOf()
    {
        $object = new stdClass;
        $this->assertInstanceOf('stdClass', $object);
        $this->assertInstanceOf('stdClass', [$object, &$object]);
    }

    /**
     * Tests for `assertIsArray()` method
     * @test
     */
    public function testAssertIsArray()
    {
        $this->assertIsArray([]);
        $this->assertIsArray([true]);
        $this->assertIsArray((array)'string');
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method
     * @test
     */
    public function testAssertIsArrayNotEmpty()
    {
        $this->assertIsArrayNotEmpty(['value']);
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method, failure with empty array
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @test
     */
    public function testAssertIsArrayNotEmptyFailureForEmptyArray()
    {
        $this->assertIsArrayNotEmpty([]);
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method, failure with a no array
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @test
     */
    public function testAssertIsArrayNotEmptyFailureForNoArray()
    {
        $this->assertIsArrayNotEmpty('string');
    }

    /**
     * Tests for `assertIsInt()` method
     * @test
     */
    public function testAssertIsInt()
    {
        $this->assertIsInt(1);
    }

    /**
     * Tests for `assertIsObject()` method
     * @test
     */
    public function testAssertIsObject()
    {
        $this->assertIsObject(new \stdClass);
        $this->assertIsObject((object)[]);
    }

    /**
     * Tests for `assertIsString()` method
     * @test
     */
    public function testAssertIsString()
    {
        $this->assertIsString('string');
        $this->assertIsString(serialize(['serialized_array']));
    }

    /**
     * Tests for `assertObjectPropertiesEqual()` method
     * @test
     */
    public function testAssertObjectPropertiesEqual()
    {
        $array = ['first' => 'one', 'second' => 'two'];
        $object = new stdClass;
        $object->first = 'first value';
        $object->second = 'second value';

        $this->assertObjectPropertiesEqual(['first', 'second'], $object);
        $this->assertObjectPropertiesEqual(['first', 'second'], (object)$array);
    }

    /**
     * Test for `assertSameMethods()` method
     * @ŧest
     */
    public function testAssertSameMethods()
    {
        $exampleClass = new ExampleClass;

        $this->assertSameMethods($exampleClass, ExampleClass::class);
        $this->assertSameMethods($exampleClass, get_class($exampleClass));

        $copyExampleClass = &$exampleClass;

        $this->assertSameMethods($exampleClass, $copyExampleClass);
    }
}
