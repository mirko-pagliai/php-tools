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

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `get_child_methods()` global function
     * @test
     */
    public function testGetChildMethods()
    {
        $expected = ['childMethod', 'anotherChildMethod'];
        $this->assertEquals($expected, get_child_methods('\App\ExampleChildClass'));

        //This class has no parent, so the result is similar to the `get_class_methods()` method
        $expected = get_class_methods('\App\ExampleClass');
        $this->assertEquals($expected, get_child_methods('\App\ExampleClass'));

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
