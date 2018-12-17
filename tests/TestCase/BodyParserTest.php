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
namespace Tools;

use PHPUnit\Framework\TestCase;
use Tools\BodyParser;
use Tools\ReflectionTrait;

/**
 * BodyParserTest class
 */
class BodyParserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Test for `_turnUrlAsAbsolute()` method
     * @test
     */
    public function testTurnUrlAsAbsolute()
    {
        foreach (['http', 'https', 'ftp'] as $scheme) {
            $urls = [
                'http://localhost/mysite' => 'http://localhost/mysite',
                'http://localhost/mysite/page.html' => 'http://localhost/mysite/page.html',
                '//localhost/mysite' => $scheme . '://localhost/mysite',
                'page2.html' => $scheme . '://localhost/mysite/page2.html',
                '/page3.html' => $scheme . '://localhost/page3.html',
                'http://external' => 'http://external',
            ];

            $BodyParser = new BodyParser(null, $scheme . '://localhost/mysite/page.html');

            foreach ($urls as $url => $expected) {
                $result = $this->invokeMethod($BodyParser, '_turnUrlAsAbsolute', [$url]);
                $this->assertEquals($expected, $result);
            }
        }
    }

    /**
     * Test for `isHtml()` method
     * @test
     */
    public function testIsHtml()
    {
        foreach ([
            '<b>String</b>' => true,
            '</b>' => true,
            '<b>String' => true,
            '<tag>String</tag>' => true,
            'String' => false,
            '' => false,
            null => false,
        ] as $string => $expected) {
            $this->assertEquals($expected, (new BodyParser($string, null))->isHtml());
        }
    }

    /**
     * Test for `extractLinks()` method
     * @test
     */
    public function testExtractLinks()
    {
        $getExtractedLinksMethod = function ($body) {
            return (new BodyParser($body, 'http://localhost'))->extractLinks();
        };

        $expected = [
            'http://localhost/page.html',
            'http://localhost/area.htm',
            'http://localhost/file.mp3',
            'http://localhost/helloworld.swf',
            'http://localhost/frame1.html',
            'http://localhost/frame2.html',
            'http://localhost/pic.jpg',
            'http://localhost/style.css',
            'http://localhost/script.js',
            'http://localhost/file2.mp3',
            'http://localhost/subtitles_en.vtt',
            'http://localhost/movie.mp4',
        ];
        $html = file_get_contents(EXAMPLE_FILES . 'page_with_some_links.html');
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        $html = '<html><body>' . $html . '</body></html>';
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        $html = '<b>No links here!</b>';
        $this->assertEquals([], $getExtractedLinksMethod($html));

        $html = '<a href="page.html">Link</a>' . PHP_EOL . '<a href="http://localhost/page.html">Link</a>';
        $expected = ['http://localhost/page.html'];
        $this->assertEquals($expected, $getExtractedLinksMethod($html));

        //Checks that the result is the same as that saved in the
        //  `extractedLinks` property as a cache
        $expected = ['link.html'];
        $BodyParser = new BodyParser($html, 'http://localhost');
        $this->setProperty($BodyParser, 'extractedLinks', $expected);
        $this->assertEquals($expected, $BodyParser->extractLinks());
    }
}
