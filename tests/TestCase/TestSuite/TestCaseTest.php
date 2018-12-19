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
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use RuntimeException;
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
     * Tests for `assertContainsInstanceOf()` method
     * @test
     */
    public function testAssertContainsInstanceOf()
    {
        $this->assertContainsInstanceOf('stdClass', [new stdClass, new stdClass]);
        $this->assertContainsInstanceOf('stdClass', new ExampleOfTraversable([new stdClass, new stdClass]));
    }

    /**
     * Tests for `assertContainsInstanceOf()` method on failure
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @test
     */
    public function testAssertContainsInstanceOfOnFailure()
    {
        $this->assertContainsInstanceOf('stdClass', new stdClass);
    }

    /**
     * Tests for `assertException()` method
     * @test
     */
    public function testAssertException()
    {
        $this->assertException(Exception::class, function () {
            throw new Exception;
        });
        $this->assertException(Exception::class, function () {
            throw new Exception('right exception message');
        });
        $this->assertException(Exception::class, function () {
            throw new Exception('right exception message');
        }, 'right exception message');

        try {
            $this->assertException(Exception::class, function () {
                return true;
            });
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected exception `Exception`, but no exception throw', $e->getMessage());
        }

        try {
            $this->assertException(RuntimeException::class, function () {
                throw new Exception('right exception message');
            });
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith('Expected exception `RuntimeException`, unexpected type `Exception`', $e->getMessage());
        }

        try {
            $this->assertException(Exception::class, function () {
                throw new Exception('wrong exception message');
            }, 'right exception message');
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith(
                'Expected message exception `right exception message`, unexpected message `wrong exception message`',
                $e->getMessage()
            );
        }

        try {
            $this->assertException(Exception::class, function () {
                throw new Exception;
            }, 'right exception message');
        } catch (AssertionFailedError $e) {
        } finally {
            $this->assertStringStartsWith(
                'Expected message exception `right exception message`, but no message for the exception',
                $e->getMessage()
            );
        }
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
        $this->assertImageSize(EXAMPLE_FILES . '400x400.jpg', 400, 400);
    }

    /**
     * Test for `assertFileMime()` method
     * @ŧest
     */
    public function testAssertFileMime()
    {
        $filename = safe_create_tmp_file('this is a test file');
        $this->assertFileMime($filename, 'text/plain');
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
    public function testAssertIsArrayNotEmptyOnFailureForEmptyArray()
    {
        $this->assertIsArrayNotEmpty([]);
    }

    /**
     * Tests for `assertIsArrayNotEmpty()` method, failure with a no array
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @test
     */
    public function testAssertIsArrayNotEmptyOnFailureForNoArray()
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
