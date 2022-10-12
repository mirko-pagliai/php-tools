<?php
/** @noinspection PhpUnhandledExceptionInspection, HttpUrlsUsage */
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

use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Tools\Filesystem;
use Tools\TestSuite\TestCase;

/**
 * FilesystemTest class
 */
class FilesystemTest extends TestCase
{
    /**
     * Test for `addSlashTerm()` method
     * @test
     */
    public function testAddSlashTerm(): void
    {
        $expected = DS . 'tmp' . DS;
        $this->assertSame($expected, Filesystem::instance()->addSlashTerm(DS . 'tmp'));
        $this->assertSame($expected, Filesystem::instance()->addSlashTerm($expected));
    }

    /**
     * Test for `concatenate()` method
     * @test
     */
    public function testConcatenate(): void
    {
        $this->assertSame('dir', Filesystem::instance()->concatenate('dir'));
        $this->assertSame('dir' . DS . 'sub-dir', Filesystem::instance()->concatenate('dir', 'sub-dir'));
        $this->assertSame('dir' . DS . 'sub-dir', Filesystem::instance()->concatenate('dir' . DS, 'sub-dir'));
        $this->assertSame('dir' . DS . 'sub-dir' . DS . 'sub-sub-dir', Filesystem::instance()->concatenate('dir', 'sub-dir', 'sub-sub-dir'));
    }

    /**
     * Test for `createFile()` method
     * @test
     */
    public function testCreateFile(): void
    {
        $filename = TMP . 'dirToBeCreated' . DS . 'exampleFile';
        $this->assertSame($filename, Filesystem::instance()->createFile($filename));
        $this->assertStringEqualsFile($filename, '');

        unlink($filename);
        $this->assertSame($filename, Filesystem::instance()->createFile($filename, 'string'));
        $this->assertStringEqualsFile($filename, 'string');

        $this->skipIf(IS_WIN);

        //Using a no existing directory, but ignoring errors
        $this->assertEmpty(Filesystem::instance()->createFile(DS . 'noExistingDir' . DS . 'file', null, 0777, true));

        //Using a no existing directory
        $this->expectException(IOException::class);
        Filesystem::instance()->createFile(DS . 'noExistingDir' . DS . 'file');
    }

    /**
     * Test for `createTmpFile()` method
     * @test
     */
    public function testCreateTmpFile(): void
    {
        foreach (['', 'string'] as $string) {
            $filename = Filesystem::instance()->createTmpFile($string);
            $pattern = IS_WIN ? '/tmp[\w\d]+\.tmp$/' : '/^' . preg_quote(TMP, '/') . '[\w\d]+$/';
            $this->assertMatchesRegularExpression($pattern, $filename);
            $this->assertStringEqualsFile($filename, $string);
        }
    }

    /**
     * Test for `getDirTree()` method
     * @uses \Tools\Filesystem::getDirTree()
     * @test
     */
    public function testGetDirTree(): void
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

        $this->assertEquals([$expectedDirs, $expectedFiles], Filesystem::instance()->getDirTree(TMP . 'exampleDir'));
        $this->assertEquals([$expectedDirs, $expectedFiles], Filesystem::instance()->getDirTree(TMP . 'exampleDir' . DS));

        //Excludes some files
        foreach ([
            ['file2'],
            ['file2', 'file3'],
            ['.hiddenFile'],
            ['.hiddenFile', 'file2', 'file3'],
        ] as $exceptions) {
            $currentExpectedFiles = array_clean($expectedFiles, fn(string $value): bool => !in_array(basename($value), $exceptions));
            $this->assertEquals([$expectedDirs, $currentExpectedFiles], Filesystem::instance()->getDirTree(TMP . 'exampleDir', $exceptions));
        }

        //Excludes a directory
        [$result] = Filesystem::instance()->getDirTree(TMP . 'exampleDir', 'subDir2');
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2', $result);
        $this->assertNotContains(TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3', $result);

        //Excludes hidden files
        foreach ([true, '.', ['.']] as $exceptions) {
            [$result] = Filesystem::instance()->getDirTree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir', $result);

            [, $result] = Filesystem::instance()->getDirTree(TMP . 'exampleDir', $exceptions);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenDir' . DS . 'file7', $result);
            $this->assertNotContains(TMP . 'exampleDir' . DS . '.hiddenFile', $result);
        }

        //Using a no existing directory, but ignoring errors
        $this->assertSame([[], []], Filesystem::instance()->getDirTree(TMP . 'noExisting', false, true));

        //Using a no existing directory
        $this->expectException(DirectoryNotFoundException::class);
        Filesystem::instance()->getDirTree(TMP . 'noExisting');
    }

    /**
     * Test for `getExtension()` method
     * @test
     */
    public function testGetExtension(): void
    {
        $this->assertNull(Filesystem::instance()->getExtension(''));

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
            $this->assertEquals($expectedExtension, Filesystem::instance()->getExtension($filename));
        }

        foreach ([
            'backup.sql.gz',
            '/backup.sql.gz',
            '/full/path/to/backup.sql.gz',
            'relative/path/to/backup.sql.gz',
            ROOT . 'backup.sql.gz',
            '/withDot./backup.sql.gz',
            'C:\backup.sql.gz',
            'C:\sub-dir\backup.sql.gz',
            'C:\withDot.\backup.sql.gz',
        ] as $filename) {
            $this->assertEquals('sql.gz', Filesystem::instance()->getExtension($filename));
        }

        foreach ([
            'http://example.com/backup.sql.gz',
            'http://example.com/backup.sql.gz#fragment',
            'http://example.com/backup.sql.gz?',
            'http://example.com/backup.sql.gz?name=value',
        ] as $url) {
            $this->assertEquals('sql.gz', Filesystem::instance()->getExtension($url));
        }
    }

    /**
     * Test for `getRoot()` method
     * @test
     */
    public function testGetRoot(): void
    {
        $this->assertSame(ROOT, Filesystem::instance()->getRoot());

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));
        $this->assertSame(rtrim(ROOT, DS), Filesystem::instance()->getRoot());
    }

    /**
     * Test for `instance()` method
     * @test
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(Filesystem::class, Filesystem::instance());
        $this->assertSame(ROOT . 'myDir', Filesystem::instance()->concatenate(ROOT, 'myDir'));
    }

    /**
     * Test for `isSlashTerm()` method
     * @test
     */
    public function testIsSlashTerm(): void
    {
        foreach ([
            'path/',
            '/path/',
            'path\\',
            '\\path\\',
        ] as $path) {
            $this->assertTrue(Filesystem::instance()->isSlashTerm($path));
        }

        foreach ([
            'path',
            '/path',
            '\\path',
            'path.ext',
            '/path.ext',
        ] as $path) {
            $this->assertFalse(Filesystem::instance()->isSlashTerm($path));
        }
    }

    /**
     * Test for `isWritableRecursive()` method
     * @uses \Tools\Filesystem::isWritableRecursive()
     * @test
     */
    public function testIsWritableRecursive(): void
    {
        $this->assertTrue(Filesystem::instance()->isWritableRecursive(TMP));

        if (!IS_WIN) {
            $this->assertFalse(Filesystem::instance()->isWritableRecursive(DS . 'bin'));
        }

        //Using a no existing directory, but ignoring errors
        $this->assertFalse(Filesystem::instance()->isWritableRecursive(TMP . 'noExisting', true, true));

        //Using a no existing directory
        $this->expectException(DirectoryNotFoundException::class);
        $this->assertFalse(Filesystem::instance()->isWritableRecursive(TMP . 'noExisting'));
    }

    /**
     * Test for `makePathAbsolute()` method
     * @test
     */
    public function testMakePathAbsolute(): void
    {
        $this->assertSame(TMP . 'dir', Filesystem::instance()->makePathAbsolute(TMP . 'dir', TMP));
        $this->assertSame(TMP . 'dir', Filesystem::instance()->makePathAbsolute('dir', TMP));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The start path `relativePath` is not absolute');
        Filesystem::instance()->makePathAbsolute('dir', 'relativePath');
    }

    /**
     * Test for `makePathRelative()` method
     * @test
     */
    public function testMakePathRelative(): void
    {
        $endPath = DS . 'a' . DS . 'b' . DS . 'c' . DS . 'd';
        $startPath = DS . 'a' . DS . 'b';
        $expected = 'c' . DS . 'd';

        $this->assertSame($expected, Filesystem::instance()->makePathRelative($endPath, $startPath));
        $this->assertSame($expected, Filesystem::instance()->makePathRelative($endPath . DS, $startPath . DS));
        $this->assertSame($expected, Filesystem::instance()->makePathRelative($endPath, $startPath . DS));
        $this->assertSame($expected, Filesystem::instance()->makePathRelative($endPath . DS, $startPath));
        $this->assertSame($expected . DS . 'file.php', Filesystem::instance()->makePathRelative($startPath . DS . 'c' . DS . 'd' . DS . 'file.php', $startPath));
    }

    /**
     * Test for `normalizePath()` method
     * @uses \Tools\Filesystem::normalizePath()
     * @test
     */
    public function testNormalizePath(): void
    {
        foreach (['path/to/normalize', 'path\\to\\normalize',] as $path) {
            $this->assertSame('path' . DS . 'to' . DS . 'normalize', Filesystem::instance()->normalizePath($path));
        }
    }

    /**
     * Test for `rmdirRecursive()` method
     * @test
     */
    public function testRmdirRecursive(): void
    {
        createSomeFiles();
        $this->assertTrue(Filesystem::instance()->rmdirRecursive(TMP . 'exampleDir'));
        $this->assertDirectoryDoesNotExist(TMP . 'exampleDir');

        //Does not delete a file
        $filename = Filesystem::instance()->createTmpFile();
        $this->assertFalse(Filesystem::instance()->rmdirRecursive($filename));
        $this->assertFileExists($filename);
    }

    /**
     * Test for `rtr()` method
     * @test
     */
    public function testRtr(): void
    {
        $this->assertSame('my' . DS . 'folder', Filesystem::instance()->rtr(ROOT . 'my' . DS . 'folder'));

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));
        $this->assertSame('my' . DS . 'folder', Filesystem::instance()->rtr(ROOT . 'my' . DS . 'folder'));
    }

    /**
     * Test for `unlinkRecursive()` method
     * @uses \Tools\Filesystem::unlinkRecursive()
     * @test
     */
    public function testUnlinkRecursive(): void
    {
        //Creates some files and some links
        $files = createSomeFiles();
        if (!IS_WIN) {
            foreach ([Filesystem::instance()->createTmpFile(), Filesystem::instance()->createTmpFile()] as $filename) {
                $link = TMP . 'exampleDir' . DS . 'link_to_' . basename($filename);
                symlink($filename, $link);
                $files[] = $link;
            }
        }

        $this->assertTrue(Filesystem::instance()->unlinkRecursive(TMP . 'exampleDir'));
        array_map([$this, 'assertFileDoesNotExist'], $files);
        $this->assertDirectoryExists(TMP . 'exampleDir');

        //Using a no existing directory, but ignoring errors
        $this->assertFalse(Filesystem::instance()->unlinkRecursive(TMP . 'noExisting', false, true));

        //Using a no existing directory
        $this->expectException(DirectoryNotFoundException::class);
        Filesystem::instance()->unlinkRecursive(TMP . 'noExisting');
    }
}
