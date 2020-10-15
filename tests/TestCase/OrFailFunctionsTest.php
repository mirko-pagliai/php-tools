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
use Exception;
use PHPUnit\Framework\Error\Deprecated;
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
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(file_exists_or_fail(create_tmp_file()));

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        file_exists_or_fail(create_tmp_file());
    }

    /**
     * Test for `in_array_or_fail()` "or fail" function
     * @test
     */
    public function testInArrayOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame('a', in_array_or_fail('a', ['a', 'b']));

        $this->assertException(NotInArrayException::class, function () {
            in_array_or_fail('a', []);
        }, 'The value `a` is not in array');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        in_array_or_fail('a', ['a', 'b']);
    }

    /**
     * Test for `is_dir_or_fail()` "or fail" function
     * @test
     */
    public function testIsDirOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame(TMP, is_dir_or_fail(TMP));

        $filename = create_tmp_file();
        $this->assertException(NotDirectoryException::class, function () use ($filename) {
            is_dir_or_fail($filename);
        }, 'Filename `' . $filename . '` is not a directory');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        is_dir_or_fail(TMP);
    }

    /**
     * Test for `is_positive_or_fail()` "or fail" function
     * @test
     */
    public function testIsPositiveOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame(1, is_positive_or_fail(1));

        $this->assertException(NotPositiveException::class, function () {
            is_positive_or_fail(-1);
        }, 'The value `-1` is not a positive');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        is_positive_or_fail(1);
    }

    /**
     * Test for `is_readable_or_fail()` "or fail" function
     * @test
     */
    public function testIsReadableOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(is_readable_or_fail(create_tmp_file()));

        $this->assertException(NotReadableException::class, function () {
            is_readable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` does not exist');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        is_readable_or_fail(create_tmp_file());
    }

    /**
     * Test for `is_true_or_fail()` function
     * @test
     */
    public function testIsTrueOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame('string', is_true_or_fail('string'));

        $this->assertException(Exception::class, function () {
            is_true_or_fail(false);
        }, 'The value is not equal to `true`');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        is_true_or_fail('string');
    }

    /**
     * Test for `is_writable_or_fail()` "or fail" function
     * @test
     */
    public function testIsWritableOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(is_writable_or_fail(create_tmp_file()));

        $this->assertException(NotWritableException::class, function () {
            is_writable_or_fail(TMP . 'noExisting');
        }, 'File or directory `' . TMP . 'noExisting` does not exist');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        is_writable_or_fail(create_tmp_file());
    }

    /**
     * Test for `key_exists_or_fail()` "or fail" function
     * @test
     */
    public function testKeyExistsOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame('a', key_exists_or_fail('a', ['a' => 'alfa', 'b' => 'beta', 'gamma']));

        $this->assertException(KeyNotExistsException::class, function () {
            key_exists_or_fail(['a'], ['d' => 'delta']);
        }, 'Key `a` does not exist');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        key_exists_or_fail('a', ['a' => 'alfa']);
    }

    /**
     * Test for `property_exists_or_fail()` "or fail" function
     * @test
     */
    public function testPropertyExistsOrFail()
    {
        $oldErrorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame('publicProperty', property_exists_or_fail(new ExampleClass(), 'publicProperty'));

        $this->assertException(PropertyNotExistsException::class, function () {
            property_exists_or_fail(new ExampleClass(), ['name']);
        }, 'Property `' . ExampleClass::class . '::$name` does not exist');

        $this->expectException(Deprecated::class);
        error_reporting($oldErrorReporting);
        property_exists_or_fail(new ExampleClass(), 'publicProperty');
    }
}
