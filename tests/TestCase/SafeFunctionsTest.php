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
        $source = safe_create_tmp_file();
        $dest = TMP . 'copy_' . md5(time());
        $this->assertFileNotExists($dest);
        $this->assertTrue(safe_copy($source, $dest));
        $this->assertFileExists($dest);
    }

    /**
     * Test for `safe_create_file()` safe function
     * @test
     */
    public function testSafeCreateFile()
    {
        $filename = TMP . 'dirToBeCreated' . DS . 'exampleFile';
        $this->assertTrue(safe_create_file($filename));
        $this->assertStringEqualsFile($filename, '');
    }

    /**
     * Test for `safe_create_tmp_file()` safe function
     * @test
     */
    public function testSafeCreateTmpFile()
    {
        $filename = safe_create_tmp_file();
        $this->assertRegexp(sprintf('/^%s[\w\d\.]+$/', preg_quote(TMP, '/')), $filename);
        $this->assertStringEqualsFile($filename, '');
    }

    /**
     * Test for `safe_mkdir()` safe function
     * @test
     */
    public function testSafeMkdir()
    {
        $dir = TMP . 'dir_' . md5(time());
        $this->assertTrue(safe_mkdir($dir));
        $this->assertDirectoryExists($dir);
    }

    /**
     * Test for `safe_rmdir()` safe function
     * @test
     */
    public function testSafeRmdir()
    {
        $dir = TMP . 'dir_' . md5(time());
        safe_mkdir($dir);
        $this->assertTrue(safe_rmdir($dir));
        $this->assertDirectoryNotExists($dir);
    }

    /**
     * Test for `safe_rmdir_recursive()` safe function
     * @test
     */
    public function testSafeRmdirRecursive()
    {
        $files = createSomeFiles();
        safe_rmdir_recursive(TMP . 'exampleDir');
        $this->assertFileNotExists($files);
        array_map([$this, 'assertDirectoryNotExists'], array_unique(array_filter($files, 'is_dir')));
    }

    /**
     * Test for `safe_symlink()` safe function
     * @test
     */
    public function testSafeSymlink()
    {
        $link = TMP . 'link_' . md5(time());
        $this->assertFileNotExists($link);
        $this->assertTrue(safe_symlink(safe_create_tmp_file(), $link));
        $this->assertFileExists($link);
        $this->assertTrue(is_link($link));
    }

    /**
     * Test for `safe_unlink()` safe function
     * @test
     */
    public function testSafeUnlink()
    {
        $file = safe_create_tmp_file();
        $this->assertTrue(safe_unlink($file));
        $this->assertFileNotExists($file);
    }

    /**
     * Test for `safe_unlink_resursive()` safe function
     * @test
     */
    public function testSafeUnlinkRecursive()
    {
        //Creates some files and some symlinks
        $files = createSomeFiles();
        foreach ([safe_create_tmp_file(), safe_create_tmp_file()] as $filename) {
            $link = TMP . 'exampleDir' . DS . 'link_to_' . basename($filename);
            safe_symlink($filename, $link);
            $files[] = $link;
        }
        safe_unlink_recursive(TMP . 'exampleDir');

        //Files no longer exist, but directories still exist
        $this->assertFileNotExists($files);
        array_map([$this, 'assertDirectoryExists'], array_map('dirname', $files));
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
