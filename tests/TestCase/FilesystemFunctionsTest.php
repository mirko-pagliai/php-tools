<?php
declare(strict_types=1);

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

use PHPUnit\Framework\Error\Deprecated;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;

/**
 * FilesystemFunctionsTest class
 */
class FilesystemFunctionsTest extends TestCase
{
    /**
     * Test for `add_slash_term()` global function
     * @test
     */
    public function testAddSlashTerm()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame(DS . 'tmp' . DS, add_slash_term(DS . 'tmp'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::addSlashTerm()`');
        add_slash_term(DS . 'tmp');
    }

    /**
     * Test for `create_file()` global function
     * @test
     */
    public function testCreateFile()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(create_file(TMP . 'dirToBeCreated' . DS . 'exampleFile'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::createFile()`');
        create_file(TMP . 'dirToBeCreated' . DS . 'exampleFile');
    }

    /**
     * Test for `create_tmp_file()` global function
     * @test
     */
    public function testCreateTmpFile()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(create_tmp_file());
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::createTmpFile()`');
        create_tmp_file();
    }

    /**
     * Test for `dir_tree()` global function
     * @test
     */
    public function testDirTree()
    {
        createSomeFiles();

        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(dir_tree(TMP . 'exampleDir'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::getDirTree()`');
        dir_tree(TMP . 'exampleDir');
    }

    /**
     * Test for `fileperms_as_octal()` global function
     * @test
     */
    public function testFilepermsAsOctal()
    {
        $this->assertSame(IS_WIN ? '0666' : '0600', fileperms_as_octal((new Filesystem())->createTmpFile()));
    }

    /**
     * Test for `fileperms_to_string()` global function
     * @test
     */
    public function testFilepermsToString()
    {
        $this->assertSame('0755', fileperms_to_string(0755));
        $this->assertSame('0755', fileperms_to_string('0755'));
    }

    /**
     * Test for `get_extension()` global function
     * @test
     */
    public function testGetExtension()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertEquals('sql.bz2', get_extension('backup.sql.bz2'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::getExtension()`');
        get_extension('backup.sql.bz2');
    }

    /**
     * Test for `is_slash_term()` global function
     * @test
     */
    public function testIsSlashTerm()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(is_slash_term('path/'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::isSlashTerm()`');
        $this->assertTrue(is_slash_term('path/'));
    }

    /**
     * Test for `is_writable_resursive()` global function
     * @test
     */
    public function testIsWritableRecursive()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(is_writable_resursive(TMP));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::isWritableResursive()`');
        is_writable_resursive(TMP);
    }

    /**
     * Test for `rmdir_recursive()` global function
     * @test
     */
    public function testRmdirRecursive()
    {
        createSomeFiles();

        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(rmdir_recursive(TMP . 'exampleDir'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::rmdirRecursive()`');
        rmdir_recursive(TMP . 'exampleDir');
    }

    /**
     * Test for `rtr()` global function
     * @test
     */
    public function testRtr()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertSame('my/folder', rtr(ROOT . 'my' . DS . 'folder'));
        error_reporting($current);

        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::rtr()`');
        rtr(ROOT . 'my' . DS . 'folder');
    }

    /**
     * Test for `unlink_resursive()` global function
     * @test
     */
    public function testUnlinkRecursive()
    {
        createSomeFiles();
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(unlink_recursive(TMP . 'exampleDir'));
        error_reporting($current);

        createSomeFiles();
        $this->expectException(Deprecated::class);
        $this->expectExceptionMessage('Deprecated. Use instead `Filesystem::unlinkRecursive()`');
        unlink_recursive(TMP . 'exampleDir');
    }
}
