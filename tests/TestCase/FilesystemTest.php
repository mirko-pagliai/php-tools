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

use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;

/**
 * FilesystemTest class
 */
class FilesystemTest extends TestCase
{
    /**
     * @var \Tools\Filesystem
     */
    protected $Filesystem;

    /**
     * This method is called before each test
     */
    public function setUp()
    {
        parent::setUp();

        $this->Filesystem = new Filesystem();
    }

    /**
     * Test for `addSlashTerm()` method
     * @test
     */
    public function testAddSlashTerm()
    {
        $expected = DS . 'tmp' . DS;
        $this->assertSame($expected, $this->Filesystem->addSlashTerm(DS . 'tmp'));
        $this->assertSame($expected, $this->Filesystem->addSlashTerm($expected));
    }

    /**
     * Test for `concatenate()` method
     * @test
     */
    public function testConcatenate()
    {
        $this->assertSame('dir', $this->Filesystem->concatenate('dir'));
        $this->assertSame('dir' . DS . 'subdir', $this->Filesystem->concatenate('dir', 'subdir'));
        $this->assertSame('dir' . DS . 'subdir', $this->Filesystem->concatenate('dir' . DS, 'subdir'));
        $this->assertSame('dir' . DS . 'subdir' . DS . 'subsubdir', $this->Filesystem->concatenate('dir', 'subdir', 'subsubdir'));
    }

    /**
     * Test for `createFile()` method
     * @test
     */
    public function testCreateFile()
    {
        $filename = TMP . 'dirToBeCreated' . DS . 'exampleFile';
        $this->assertTrue($this->Filesystem->createFile($filename));
        $this->assertStringEqualsFile($filename, '');

        unlink($filename);
        $this->assertTrue($this->Filesystem->createFile($filename, 'string'));
        $this->assertStringEqualsFile($filename, 'string');

        $this->skipIf(IS_WIN);

        //Using a no existing directory, but ignoring errors
        $this->assertFalse($this->Filesystem->createFile(DS . 'noExistingDir' . DS . 'file', null, 0777, true));

        //Using a no existing directory
        $this->expectException(IOException::class);
        $this->Filesystem->createFile(DS . 'noExistingDir' . DS . 'file');
    }

    /**
     * Test for `createTmpFile()` method
     * @test
     */
    public function testCreateTmpFile()
    {
        foreach (['', 'string'] as $string) {
            $filename = $this->Filesystem->createTmpFile($string);
            $this->assertMatchesRegularExpression(sprintf('/^%s[\w\d\.]+$/', preg_quote(TMP, '/')), $filename);
            $this->assertStringEqualsFile($filename, $string);
        }
    }

    /**
     * Test for `getDirTree()` method
     * @test
     */
    public function testGetDirTree()
    {
        $expectedDirs = [
            TMP . 'exampleDir',
            TMP . 'exampleDir' . DS . '.hiddenDir',
            TMP . 'exampleDir' . DS . 'emptyDir',
            TMP . 'exampleDir' . DS . 'subDir1',
            TMP . 'exampleDir' . DS . 'subDir2',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3',
        ];
        $expectedFiles = createSomeFiles();

        $this->assertEquals([$expectedDirs, $expectedFiles], $this->Filesystem->getDirTree(TMP . 'exampleDir'));
        $this->assertEquals([$expectedDirs, $expectedFiles], $this->Filesystem->getDirTree(TMP . 'exampleDir' . DS));

        //Excludes some files
        foreach ([
            ['file2'],
            ['file2', 'file3'],
            ['.hiddenFile'],
            ['.hiddenFile', 'file2', 'file3'],
        ] as $exceptions) {
            $currentExpectedFiles = array_clean($expectedFiles, function ($value) use ($exceptions) {
                return !in_array(basename($value), $exceptions);
            });
            $this->assertEquals([$expectedDirs, $currentExpectedFiles], $this->Filesystem->getDirTree(TMP . 'exampleDir', $exceptions));
        }

        //Excludes a directory
        list($result) = $this->Filesystem->getDirTree(TMP . 'exampleDir', 'subDir2');
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2', $result);
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3', $result);

        //Excludes hidden files
        foreach ([true, '.', ['.']] as $exceptions) {
            list($result) = $this->Filesystem->getDirTree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir', $result);

            list(, $result) = $this->Filesystem->getDirTree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir' . DS . 'file7', $result);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenFile', $result);
        }

        //Using a no existing directory, but ignoring errors
        $this->assertSame([[], []], $this->Filesystem->getDirTree(TMP . 'noExisting', false, true));

        //Using a no existing directory
        $this->expectException(InvalidArgumentException::class);
        $this->Filesystem->getDirTree(TMP . 'noExisting');
    }

    /**
     * Test for `getExtension()` method
     * @test
     */
    public function testGetExtension()
    {
        $this->assertNull($this->Filesystem->getExtension(''));

        foreach ([
            'backup.sql' => 'sql',
            'backup.sql.bz2' => 'sql.bz2',
            'backup.sql.gz' => 'sql.gz',
            'text.txt' => 'txt',
            'TEXT.TXT' => 'txt',
            'noExtension' => null,
            'txt' => null,
            '.txt' => null,
            '.hiddenFile' => null,
        ] as $filename => $expectedExtension) {
            $this->assertEquals($expectedExtension, $this->Filesystem->getExtension($filename));
        }

        foreach ([
            'backup.sql.gz',
            '/backup.sql.gz',
            '/full/path/to/backup.sql.gz',
            'relative/path/to/backup.sql.gz',
            ROOT . 'backup.sql.gz',
            '/withDot./backup.sql.gz',
            'C:\backup.sql.gz',
            'C:\subdir\backup.sql.gz',
            'C:\withDot.\backup.sql.gz',
        ] as $filename) {
            $this->assertEquals('sql.gz', $this->Filesystem->getExtension($filename));
        }

        foreach ([
            'http://example.com/backup.sql.gz',
            'http://example.com/backup.sql.gz#fragment',
            'http://example.com/backup.sql.gz?',
            'http://example.com/backup.sql.gz?name=value',
        ] as $url) {
            $this->assertEquals('sql.gz', $this->Filesystem->getExtension($url));
        }
    }

    /**
     * Test for `getRoot()` method
     * @test
     */
    public function testGetRoot()
    {
        $this->assertSame(ROOT, $this->Filesystem->getRoot());

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));
        $this->assertSame(rtrim(ROOT, DS), $this->Filesystem->getRoot());
    }

    /**
     * Test for `isSlashTerm()` method
     * @test
     */
    public function testIsSlashTerm()
    {
        foreach ([
            'path/',
            '/path/',
            'path\\',
            '\\path\\',
        ] as $path) {
            $this->assertTrue($this->Filesystem->isSlashTerm($path));
        }

        foreach ([
            'path',
            '/path',
            '\\path',
            'path.ext',
            '/path.ext',
        ] as $path) {
            $this->assertFalse($this->Filesystem->isSlashTerm($path));
        }
    }

    /**
     * Test for `isWritableResursive()` method
     * @test
     */
    public function testIsWritableRecursive()
    {
        $this->assertTrue($this->Filesystem->isWritableResursive(TMP));

        if (!IS_WIN) {
            $this->assertFalse($this->Filesystem->isWritableResursive(DS . 'bin'));
        }

        //Using a no existing directory, but ignoring errors
        $this->assertFalse($this->Filesystem->isWritableResursive(TMP . 'noExisting', true, true));

        //Using a no existing directory
        $this->expectException(InvalidArgumentException::class);
        $this->assertFalse($this->Filesystem->isWritableResursive(TMP . 'noExisting'));
    }

    /**
     * Test for `makePathAbsolute()` method
     * @test
     */
    public function testMakePathAbsolute()
    {
        $this->assertSame(TMP . 'dir', $this->Filesystem->makePathAbsolute(TMP . 'dir', TMP));
        $this->assertSame(TMP . 'dir', $this->Filesystem->makePathAbsolute('dir', TMP));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The start path `relativePath` is not absolute');
        $this->Filesystem->makePathAbsolute('dir', 'relativePath');
    }

    /**
     * Test for `normalizePath()` method
     * @test
     */
    public function testNormalizePath()
    {
        foreach ([
            'path/to/normalize',
            'path\\to\\normalize',
        ] as $path) {
            $this->assertSame('path' . DS . 'to' . DS . 'normalize', $this->Filesystem->normalizePath($path));
        }
    }

    /**
     * Test for `rmdirRecursive()` method
     * @test
     */
    public function testRmdirRecursive()
    {
        createSomeFiles();
        $this->assertTrue($this->Filesystem->rmdirRecursive(TMP . 'exampleDir'));
        $this->assertDirectoryDoesNotExist(TMP . 'exampleDir');

        //Does not delete a file
        $filename = $this->Filesystem->createTmpFile();
        $this->assertFalse($this->Filesystem->rmdirRecursive($filename));
        $this->assertFileExists($filename);
    }

    /**
     * Test for `rtr()` method
     * @test
     */
    public function testRtr()
    {
        $this->assertSame('my' . DS . 'folder', $this->Filesystem->rtr(ROOT . 'my' . DS . 'folder'));

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));
        $this->assertSame('my' . DS . 'folder', $this->Filesystem->rtr(ROOT . 'my' . DS . 'folder'));
    }

    /**
     * Test for `unlinkResursive()` method
     * @test
     */
    public function testUnlinkRecursive()
    {
        //Creates some files and some links
        $files = createSomeFiles();
        if (!IS_WIN) {
            foreach ([$this->Filesystem->createTmpFile(), $this->Filesystem->createTmpFile()] as $filename) {
                $link = TMP . 'exampleDir' . DS . 'link_to_' . basename($filename);
                symlink($filename, $link);
                $files[] = $link;
            }
        }

        $this->assertTrue($this->Filesystem->unlinkRecursive(TMP . 'exampleDir'));
        array_map([$this, 'assertFileDoesNotExist'], $files);
        $this->assertDirectoryExists(TMP . 'exampleDir');

        //Using a no existing directory, but ignoring errors
        $this->assertFalse($this->Filesystem->unlinkRecursive(TMP . 'noExisting', false, true));

        //Using a no existing directory
        $this->expectException(InvalidArgumentException::class);
        $this->Filesystem->unlinkRecursive(TMP . 'noExisting');
    }
}
