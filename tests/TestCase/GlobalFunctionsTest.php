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

use PHPUnit\Framework\TestCase;
use Tools\TestSuite\TestCaseTrait;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    use TestCaseTrait;

    /**
     * Test for `clean_url()` global function
     * @test
     */
    public function testCleanUrl()
    {
        foreach ([
            'http://mysite.com',
            'http://mysite.com/',
            'http://mysite.com#fragment',
            'http://mysite.com/#fragment',
        ] as $url) {
            $this->assertRegExp('/^http:\/\/mysite\.com\/?$/', clean_url($url));
        }

        foreach ([
            'relative',
            '/relative',
            'relative/',
            '/relative/',
            'relative#fragment',
            'relative/#fragment',
            '/relative#fragment',
            '/relative/#fragment',
        ] as $url) {
            $this->assertRegExp('/^\/?relative\/?$/', clean_url($url));
        }

        foreach ([
            'www.mysite.com',
            'http://www.mysite.com',
            'https://www.mysite.com',
            'ftp://www.mysite.com',
        ] as $url) {
            $this->assertRegExp('/^((https?|ftp):\/\/)?mysite\.com$/', clean_url($url, true));
        }

        foreach ([
            'http://mysite.com',
            'http://mysite.com/',
            'http://www.mysite.com',
            'http://www.mysite.com/',
        ] as $url) {
            $this->assertEquals('http://mysite.com', clean_url($url, true, true));
        }
    }

    /**
     * Test for `dir_tree()` global function
     * @test
     */
    public function testDirTree()
    {
        $files = $this->createSomeFiles();

        $expectedDirs = [
            TMP . 'exampleDir',
            TMP . 'exampleDir' . DS . '.hiddenDir',
            TMP . 'exampleDir' . DS . 'emptyDir',
            TMP . 'exampleDir' . DS . 'subDir1',
            TMP . 'exampleDir' . DS . 'subDir2',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3',
        ];
        $expectedFiles = [
            TMP . 'exampleDir' . DS . '.hiddenDir' . DS . 'file7',
            TMP . 'exampleDir' . DS . '.hiddenFile',
            TMP . 'exampleDir' . DS . 'file1',
            TMP . 'exampleDir' . DS . 'subDir1' . DS . 'file2',
            TMP . 'exampleDir' . DS . 'subDir1' . DS . 'file3',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'file4',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'file5',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3' . DS . 'file6',
        ];

        foreach ([
            TMP . 'exampleDir',
            TMP . 'exampleDir' . DS,
        ] as $directory) {
            $result = dir_tree($directory);
            $this->assertCount(2, $result);
            $this->assertEquals($expectedDirs, $result[0]);
            $this->assertEquals($expectedFiles, $result[1]);
        }

        //`$exceptions` as `false`
        $result = dir_tree($directory, false);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedDirs, $result[0]);
        $this->assertEquals($expectedFiles, $result[1]);

        //Excludes some files
        foreach ([
            ['file2'],
            ['file2', 'file3'],
            ['.hiddenFile'],
            ['.hiddenFile', 'file2', 'file3'],
        ] as $exceptions) {
            $currentExpected = array_values(array_filter($expectedFiles, function ($value) use ($exceptions) {
                return !in_array(basename($value), $exceptions);
            }));
            $result = dir_tree(TMP . 'exampleDir', $exceptions);
            $this->assertEquals($currentExpected, $result[1]);
        }

        //Excludes hidden files
        $removeHiddenDirsAndFiles = function ($values) {
            return array_values(array_filter($values, function ($value) {
                return strpos($value, DS . '.') === false;
            }));
        };
        $currentExpectedDirs = $removeHiddenDirsAndFiles($expectedDirs);
        $currentExpectedFiles = $removeHiddenDirsAndFiles($expectedFiles);
        foreach ([true, '.', ['.']] as $exceptions) {
            $result = dir_tree(TMP . 'exampleDir', $exceptions);
            $this->assertCount(2, $result);
            $this->assertEquals($currentExpectedDirs, $result[0]);
            $this->assertEquals($currentExpectedFiles, $result[1]);
        }

        //Using a file or a no existing file
        foreach ([$files[0], TMP . 'noExisting'] as $directory) {
            $this->assertEquals([[], []], dir_tree($directory));
        }

        safe_rmdir_recursive(TMP . 'exampleDir');
    }

    /**
     * Test for `ends_with()` global function
     * @test
     */
    public function testEndsWith()
    {
        $string = 'a test with some words';

        $this->assertTrue(ends_with($string, ''));
        $this->assertTrue(ends_with($string, 's'));
        $this->assertTrue(ends_with($string, 'some words'));
        $this->assertTrue(ends_with($string, $string));

        $this->assertFalse(ends_with($string, ' '));
        $this->assertFalse(ends_with($string, 'b'));
        $this->assertFalse(ends_with($string, 'test'));
    }

    /**
     * Test for `first_value()` global function
     * @test
     */
    public function testFirstValue()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('first', first_value($array));

        $array = array_combine(['a', 'b', 'c'], $array);
        $this->assertEquals('first', first_value($array));
    }

    /**
     * Test for `get_child_methods()` global function
     * @test
     */
    public function testGetChildMethods()
    {
        $expected = ['childMethod', 'anotherChildMethod'];
        $this->assertEquals($expected, get_child_methods('\App\ExampleChildClass'));

        //This class has no parent, so the result is similar to the `get_class_methods()` method
        $this->assertSameMethods('\App\ExampleClass', '\App\ExampleClass');

        //No existing class
        $this->assertNull(get_child_methods('\NoExistingClass'));
    }

    /**
     * Test for `get_class_short_name()` global function
     * @test
     */
    public function testGetClassShortName()
    {
        foreach (['\App\ExampleClass', 'App\ExampleClass'] as $class) {
            $this->assertEquals('ExampleClass', get_class_short_name($class));
        }
    }

    /**
     * Test for `get_extension()` global function
     * @test
     */
    public function testGetExtension()
    {
        $extensions = [
            'backup.sql' => 'sql',
            'backup.sql.bz2' => 'sql.bz2',
            'backup.sql.gz' => 'sql.gz',
            'text.txt' => 'txt',
            'TEXT.TXT' => 'txt',
            'noExtension' => null,
            'txt' => null,
            '.txt' => null,
            '.hiddenFile' => null,
        ];

        foreach ($extensions as $filename => $expectedExtension) {
            $this->assertEquals($expectedExtension, get_extension($filename));
        }

        $filenames = [
            'backup.sql.gz',
            '/backup.sql.gz',
            '/full/path/to/backup.sql.gz',
            'relative/path/to/backup.sql.gz',
            ROOT . 'backup.sql.gz',
            '/withDot./backup.sql.gz',
            'C:\backup.sql.gz',
            'C:\subdir\backup.sql.gz',
            'C:\withDot.\backup.sql.gz',
        ];

        foreach ($filenames as $filename) {
            $this->assertEquals('sql.gz', get_extension($filename));
        }

        $urls = [
            'http://example.com/backup.sql.gz',
            'http://example.com/backup.sql.gz#fragment',
            'http://example.com/backup.sql.gz?',
            'http://example.com/backup.sql.gz?name=value',
        ];

        foreach ($urls as $url) {
            $this->assertEquals('sql.gz', get_extension($url));
        }
    }

    /**
     * Test for `get_hostname_from_url()` global function
     * @test
     */
    public function testGetHostnameFromUrl()
    {
        foreach (['http://127.0.0.1', 'http://127.0.0.1/'] as $url) {
            $this->assertEquals('127.0.0.1', get_hostname_from_url($url));
        }

        foreach (['http://localhost', 'http://localhost/'] as $url) {
            $this->assertEquals('localhost', get_hostname_from_url($url));
        }

        foreach ([
            '//google.com',
            'http://google.com',
            'http://google.com/',
            'http://www.google.com',
            'https://google.com',
            'http://google.com/page',
            'http://google.com/page?name=value',
        ] as $url) {
            $this->assertEquals('google.com', get_hostname_from_url($url));
        }

        $this->assertNull(get_hostname_from_url('page.html'));
    }

    /**
     * Test for `is_external_url()` global function
     * @test
     */
    public function testIsExternalUrl()
    {
        $hostname = 'google.com';

        foreach ([
            '//google.com',
            '//google.com/',
            'http://google.com',
            'http://google.com/',
            'http://www.google.com',
            'http://www.google.com/',
            'http://www.google.com/page.html',
            'https://google.com',
            'relative.html',
            '/relative.html',
        ] as $url) {
            $this->assertFalse(is_external_url($url, $hostname));
        }

        foreach ([
            '//site.com',
            'http://site.com',
            'http://www.site.com',
            'http://subdomain.google.com',
        ] as $url) {
            $this->assertTrue(is_external_url($url, $hostname));
        }
    }

    /**
     * Test for `isJson()` global function
     * @test
     */
    public function testIsJson()
    {
        $this->assertTrue(is_json('{"a":1,"b":2,"c":3,"d":4,"e":5}'));

        foreach ([
            ['alfa' => 'first', 'beta' => 'second'],
            (object)['alfa' => 'first', 'beta' => 'second'],
            'this is a no json string',
        ] as $string) {
            $this->assertFalse(is_json($string));
        }
    }

    /**
     * Test for `is_positive()` global function
     * @test
     */
    public function testIsPositive()
    {
        $this->assertTrue(is_positive(1));

        foreach ([0, -1, 1.1] as $string) {
            $this->assertFalse(is_positive($string));
        }
    }

    /**
     * Test for `is_slash_term()` global function
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
            $this->assertTrue(is_slash_term($path));
        }

        foreach ([
            'path',
            '/path',
            '\\path',
            'path.ext',
            '/path.ext',
        ] as $path) {
            $this->assertFalse(is_slash_term($path));
        }
    }

    /**
     * Test for `is_url()` global function
     * @test
     */
    public function testIsUrl()
    {
        foreach ([
            'https://www.example.com',
            'http://www.example.com',
            'www.example.com',
            'http://example.com',
            'http://example.com/file',
            'http://example.com/file.html',
            'www.example.com/file.html',
            'http://example.com/subdir/file',
            'ftp://www.example.com',
            'ftp://example.com',
            'ftp://example.com/file.html',
        ] as $url) {
            $this->assertTrue(is_url($url));
        }

        foreach ([
            'example.com',
            'folder',
            DIRECTORY_SEPARATOR . 'folder',
            DIRECTORY_SEPARATOR . 'folder' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'folder' . DIRECTORY_SEPARATOR . 'file.txt',
        ] as $url) {
            $this->assertFalse(is_url($url));
        }
    }

    /**
     * Test for `is_win()` global function on Unix
     * @group onlyUnix
     * @test
     */
    public function testIsWinOnUnix()
    {
        $this->assertFalse(is_win());
    }

    /**
     * Test for `is_win()` global function on Windows
     * @group onlyWindows
     * @test
     */
    public function testIsWinOnWin()
    {
        $this->assertTrue(is_win());
    }

    /**
     * Test for `is_writable_resursive()` global function
     * @test
     */
    public function testIsWritableRecursive()
    {
        $this->createSomeFiles();

        $this->assertTrue(is_writable_resursive(TMP . 'exampleDir'));
        $this->assertFalse(is_writable_resursive(TMP . 'noExisting'));

        rmdir_recursive(TMP . 'exampleDir');
    }

    /**
     * Test for `last_value()` global function
     * @test
     */
    public function testLastValue()
    {
        $array = ['first', 'second', 'third'];
        $this->assertEquals('third', last_value($array));

        $array = array_combine(['a', 'b', 'c'], $array);
        $this->assertEquals('third', last_value($array));
    }

    /**
     * Test for `rmdir_recursive()` global function
     * @test
     */
    public function testRmdirRecursive()
    {
        $files = $this->createSomeFiles();

        foreach ($files as $file) {
            $this->assertFileExists($file);
        }

        rmdir_recursive(TMP . 'exampleDir');

        foreach ($files as $file) {
            $this->assertFileNotExists($file);
            $this->assertFileNotExists(dirname($file));
        }

        //Does not delete a file
        $file = TMP . 'exampleDir' . DS . 'exampleFile';
        mkdir(dirname($file), 0777, true);
        file_put_contents($file, null);
        $this->assertFileExists($file);
        rmdir_recursive($file);
        $this->assertFileExists($file);

        safe_unlink($file);
        safe_rmdir(dirname($file));
    }

    /**
     * Test for `rtr()` global function
     * @test
     */
    public function testRtr()
    {
        $values = [
            ROOT . 'my' . DS . 'folder' => 'my' . DS . 'folder',
            'my' . DS . 'folder' => 'my' . DS . 'folder',
            DS . 'my' . DS . 'folder' => DS . 'my' . DS . 'folder',
        ];

        foreach ($values as $result => $expected) {
            $this->assertEquals($expected, rtr($result));
        }

        //Resets the ROOT value, removing the final slash
        putenv('ROOT=' . rtrim(ROOT, DS));

        foreach ($values as $result => $expected) {
            $this->assertEquals($expected, rtr($result));
        }
    }

    /**
     * Test for `starts_with()` global function
     * @test
     */
    public function testStartsWith()
    {
        $string = 'a test with some words';

        $this->assertTrue(starts_with($string, ''));
        $this->assertTrue(starts_with($string, 'a'));
        $this->assertTrue(starts_with($string, 'a test'));
        $this->assertTrue(starts_with($string, $string));

        $this->assertFalse(starts_with($string, ' '));
        $this->assertFalse(starts_with($string, 'b'));
        $this->assertFalse(starts_with($string, 'test'));
    }

    /**
     * Test for `unlink_resursive()` global function
     * @test
     */
    public function testUnlinkRecursive()
    {
        $files = $this->createSomeFiles();

        //Creates some symlinks
        foreach ([0, 1] as $key) {
            $link = dirname($files[0]) . DS . 'link_to_' . basename($files[$key]);
            safe_symlink($files[$key], $link);
            $files[] = $link;
        }

        unlink_recursive(TMP . 'exampleDir');

        //Files no longer exist, but directories still exist
        foreach ($files as $file) {
            $this->assertFileNotExists($file);
            $this->assertFileExists(dirname($file));
        }

        safe_rmdir_recursive(TMP . 'exampleDir');
    }

    /**
     * Test for `which()` global function on Unix
     * @group onlyUnix
     * @test
     */
    public function testWhichOnUnix()
    {
        $this->assertEquals('/bin/cat', which('cat'));
        $this->assertNull(which('noExistingBin'));
    }

    /**
     * Test for `which()` global function on Windows
     * @group onlyWindows
     * @test
     */
    public function testWhichOnWindws()
    {
        $this->assertEquals('"C:\Program Files\Git\usr\bin\cat.exe"', which('cat'));
        $this->assertNull(which('noExistingBin'));
    }
}
