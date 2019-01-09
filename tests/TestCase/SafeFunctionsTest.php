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
 * @since       1.0.4
 */
namespace Tools\Test;

use PHPUnit\Framework\Error\Deprecated;
use Tools\TestSuite\TestCase;

/**
 * SafeFunctionsTest class
 */
class SafeFunctionsTest extends TestCase
{
    /**
     * Test for `safe_copy()` safe function
     * @test
     */
    public function testSafeCopy()
    {
        $this->expectException(Deprecated::class);
        safe_copy(create_tmp_file(), TMP . 'copy');
    }

    /**
     * Test for `safe_create_file()` safe function
     * @test
     */
    public function testSafeCreateFile()
    {
        $this->expectException(Deprecated::class);
        safe_create_file(TMP . 'example');
    }

    /**
     * Test for `safe_create_tmp_file()` safe function
     * @test
     */
    public function testSafeCreateTmpFile()
    {
        $this->expectException(Deprecated::class);
        safe_create_tmp_file();
    }

    /**
     * Test for `safe_mkdir()` safe function
     * @test
     */
    public function testSafeMkdir()
    {
        $this->expectException(Deprecated::class);
        safe_mkdir(TMP . 'dir');
    }

    /**
     * Test for `safe_rmdir()` safe function
     * @test
     */
    public function testSafeRmdir()
    {
        $this->expectException(Deprecated::class);
        safe_rmdir(TMP . 'dir');
    }

    /**
     * Test for `safe_rmdir_recursive()` safe function
     * @test
     */
    public function testSafeRmdirRecursive()
    {
        $this->expectException(Deprecated::class);
        safe_rmdir_recursive(TMP . 'dir');
    }

    /**
     * Test for `safe_symlink()` safe function
     * @test
     */
    public function testSafeSymlink()
    {
        $this->expectException(Deprecated::class);
        safe_symlink(create_tmp_file(), TMP . 'link');
    }

    /**
     * Test for `safe_unlink()` safe function
     * @test
     */
    public function testSafeUnlink()
    {
        $this->expectException(Deprecated::class);
        safe_unlink(create_tmp_file());
    }

    /**
     * Test for `safe_unlink_resursive()` safe function
     * @test
     */
    public function testSafeUnlinkRecursive()
    {
        $this->expectException(Deprecated::class);
        safe_unlink_recursive(TMP . 'dir');
    }

    /**
     * Test for `safe_unserialize()` safe function
     * @test
     */
    public function testSafeUnserialize()
    {
        $this->expectException(Deprecated::class);
        safe_unserialize(serialize(['test']));
    }
}
