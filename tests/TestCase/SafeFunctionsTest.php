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

use PHPUnit\Framework\TestCase;
use Tools\TestSuite\TestCaseTrait;

/**
 * SafeFunctionsTest class
 */
class SafeFunctionsTest extends TestCase
{
    use TestCaseTrait;

    /**
     * Test for `safe_copy()` safe function
     * @test
     */
    public function testSafeCopy()
    {
        $source = TMP . 'dir_' . md5(time());
        $dest = TMP . 'dir_' . md5(time() + 1);
        file_put_contents($source, null);
        $this->assertFileNotExists($dest);

        $this->assertTrue(safe_copy($source, $dest));
        $this->assertFileExists($dest);

        safe_unlink($source);
        safe_unlink($dest);
    }

    /**
     * Test for `safe_mkdir()` safe function
     * @test
     */
    public function testSafeMkdir()
    {
        $dir = TMP . 'dir_' . md5(time());
        $this->assertFileNotExists($dir);

        $this->assertTrue(safe_mkdir($dir, 0777, true));
        $this->assertFileExists($dir);
        $this->assertTrue(is_dir($dir));

        safe_rmdir($dir);
    }

    /**
     * Test for `safe_rmdir()` safe function
     * @test
     */
    public function testSafeRmdir()
    {
        $dir = TMP . 'dir_' . md5(time());
        safe_mkdir($dir, 0777, true);

        $this->assertTrue(safe_rmdir($dir));
        $this->assertFileNotExists($dir);
    }

    /**
     * Test for `safe_symlink()` safe function
     * @test
     */
    public function testSafeSymlink()
    {
        $target = tempnam(TMP, 'safe_file');
        $link = TMP . 'file_' . md5(time());
        $this->assertFileNotExists($link);

        $this->assertTrue(safe_symlink($target, $link));
        $this->assertFileExists($link);
        $this->assertTrue(is_link($link));

        safe_unlink($target);
        safe_unlink($link);
    }

    /**
     * Test for `safe_unlink()` safe function
     * @test
     */
    public function testSafeUnlink()
    {
        $file = tempnam(TMP, 'safe_file');
        $this->assertFileExists($file);

        $this->assertTrue(safe_unlink($file));
        $this->assertFileNotExists($file);
    }

    /**
     * Test for `safe_unserialize()` safe function
     * @test
     */
    public function testSafeUnserialize()
    {
        $expected = ['test'];
        $str = serialize($expected);
        $this->assertEquals($expected, safe_unserialize($str));

        $this->assertFalse(safe_unserialize('invalidString'));
    }
}
