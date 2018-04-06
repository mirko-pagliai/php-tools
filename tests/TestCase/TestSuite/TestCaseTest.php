<?php
/**
 * This file is part of cakephp-thumber.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-thumber
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Tools\Test\TestSuite;

use App\ExampleClass;
use PHPUnit\Framework\TestCase;
use Tools\TestSuite\TestCaseTrait;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    use TestCaseTrait;

    /**
     * Test for `assertFileExtension()` method
     * @ŧest
     */
    public function testAssertFileExtension()
    {
        foreach ([
            'jpg' => 'file.jpg',
            'jpg' => 'file.JPG',
            'jpeg' => 'file.jpeg',
            'jpg' => 'path/to/file.jpg',
            'jpg' => '/full/path/to/file.jpg',
        ] as $extension => $filename) {
            $this->assertFileExtension($extension, $filename);
        }
    }

    /**
     * Test for `assertImageSize()` method
     * @ŧest
     */
    public function testAssertImageSize()
    {
        $this->assertImageSize(COMPARING_FILES . '400x400.jpg', 400, 400);
    }

    /**
     * Test for `assertFileMime()` method
     * @ŧest
     */
    public function testAssertFileMime()
    {
        //@codingStandardsIgnoreLine
        $filename = @tempnam(TMP, 'test_file.txt');
        file_put_contents($filename, 'this is a test file');

        $this->assertFileMime($filename, 'text/plain');

        //@codingStandardsIgnoreLine
        @unlink($filename);
    }

    /**
     * Test for `assertSameMethods()` method
     * @ŧest
     */
    public function testAssertSameMethods()
    {
        $exampleClass = new ExampleClass;

        $this->assertSameMethods($exampleClass, ExampleClass::class);
        $this->assertSameMethods($exampleClass, get_class($exampleClass));

        $copyExampleClass = &$exampleClass;

        $this->assertSameMethods($exampleClass, $copyExampleClass);
    }
}
