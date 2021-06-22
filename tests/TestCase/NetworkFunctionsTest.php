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

use Tools\TestSuite\TestCase;

/**
 * NetworkFunctionsTest class
 */
class NetworkFunctionsTest extends TestCase
{
    /**
     * Test for `clean_url()` global function
     * @test
     */
    public function testCleanUrl(): void
    {
        foreach ([
            'http://mysite.com',
            'http://mysite.com/',
            'http://mysite.com#fragment',
            'http://mysite.com/#fragment',
        ] as $url) {
            $this->assertMatchesRegularExpression('/^http:\/\/mysite\.com\/?$/', clean_url($url));
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
            $this->assertMatchesRegularExpression('/^\/?relative\/?$/', clean_url($url));
        }

        foreach ([
            'www.mysite.com',
            'http://www.mysite.com',
            'https://www.mysite.com',
            'ftp://www.mysite.com',
        ] as $url) {
            $this->assertMatchesRegularExpression('/^((https?|ftp):\/\/)?mysite\.com$/', clean_url($url, true));
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
     * Test for `get_hostname_from_url()` global function
     * @test
     */
    public function testGetHostnameFromUrl(): void
    {
        $this->assertEmpty(get_hostname_from_url('page.html'));

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
    }

    /**
     * Test for `is_external_url()` global function
     * @test
     */
    public function testIsExternalUrl(): void
    {
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
            $this->assertFalse(is_external_url($url, 'google.com'));
        }

        foreach ([
            '//site.com',
            'http://site.com',
            'http://www.site.com',
            'http://subdomain.google.com',
        ] as $url) {
            $this->assertTrue(is_external_url($url, 'google.com'));
        }
    }

    /**
     * Test for `is_localhost()` global function
     * @test
     */
    public function testIsLocalhost(): void
    {
        $_SERVER['REMOTE_ADDR'] = '8.8.8.8';
        $this->assertFalse(is_localhost());

        foreach (['127.0.0.1', '::1'] as $ip) {
            $_SERVER['REMOTE_ADDR'] = $ip;
            $this->assertTrue(is_localhost());
        }
        unset($_SERVER['REMOTE_ADDR']);
    }

    /**
     * Test for `is_url()` global function
     * @test
     */
    public function testIsUrl(): void
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
            'http://example.com/name-with-brackets(3).jpg',
        ] as $url) {
            $this->assertTrue(is_url($url), 'Failed asserting that `' . $url . '` is a valid url');
        }

        foreach ([
            'example.com',
            'folder',
            DS . 'folder',
            DS . 'folder' . DS,
            DS . 'folder' . DS . 'file.txt',
        ] as $url) {
            $this->assertFalse(is_url($url));
        }
    }

    /**
     * Test for `url_to_absolute()` global function
     * @test
     */
    public function testUrlToAbsolute(): void
    {
        foreach (['http', 'https', 'ftp'] as $scheme) {
            $paths = [
                $scheme . '://localhost/mysite/subdir/anothersubdir',
                $scheme . '://localhost/mysite/subdir/anothersubdir/a_file.html',
            ];

            foreach ($paths as $path) {
                foreach ([
                    'http://localhost/mysite' => 'http://localhost/mysite',
                    'http://localhost/mysite/page.html' => 'http://localhost/mysite/page.html',
                    '//localhost/mysite' => $scheme . '://localhost/mysite',
                    'page2.html' => $scheme . '://localhost/mysite/subdir/anothersubdir/page2.html',
                    '/page3.html' => $scheme . '://localhost/page3.html',
                    '../page4.html' => $scheme . '://localhost/mysite/subdir/page4.html',
                    '../../page5.html' => $scheme . '://localhost/mysite/page5.html',
                    'http://external.com' => 'http://external.com',
                ] as $url => $expected) {
                    $this->assertSame($expected, url_to_absolute($path, $url));
                }
            }
        }

        $this->assertSame('http://example.com/page6.html', url_to_absolute('http://example.com', 'page6.html'));
    }
}
