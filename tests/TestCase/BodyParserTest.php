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

use Tools\BodyParser;
use Tools\TestSuite\TestCase;

/**
 * BodyParserTest class
 */
class BodyParserTest extends TestCase
{
    /**
     * Test for `_turnUrlAsAbsolute()` method
     * @test
     */
    public function testTurnUrlAsAbsolute()
    {
        foreach (['http', 'https', 'ftp'] as $scheme) {
            $BodyParser = new BodyParser(null, $scheme . '://localhost/mysite/page.html');
            $urls = [
                'http://localhost/mysite' => 'http://localhost/mysite',
                'http://localhost/mysite/page.html' => 'http://localhost/mysite/page.html',
                '//localhost/mysite' => $scheme . '://localhost/mysite',
                'page2.html' => $scheme . '://localhost/mysite/page2.html',
                '/page3.html' => $scheme . '://localhost/page3.html',
                'http://external' => 'http://external',
            ];

            foreach ($urls as $url => $expected) {
                $this->assertEquals($expected, $this->invokeMethod($BodyParser, '_turnUrlAsAbsolute', [$url]));
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
        $getExtractedLinksMethod = function ($html) {
            return (new BodyParser($html, 'http://localhost'))->extractLinks();
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
        $html = '<a href="/page.html#fragment">Link</a>
<map name="example"><area href="area.htm"></map>
<audio src="/file.mp3"></audio>
<embed src="helloworld.swf">
<frame src="frame1.html"></frame>
<iframe src="frame2.html"></iframe>
<img src="pic.jpg" />
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="script.js" />
<audio><source src="file2.mp3" type="audio/mpeg"></audio>
<video><track src="subtitles_en.vtt"></video>
<video src="//localhost/movie.mp4"></video>';
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
