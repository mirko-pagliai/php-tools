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

use Exception;
use stdClass;
use Tools\TestSuite\TestCase;

class ExamplesTest extends TestCase
{
    /**
     * @test
     */
    public function testSomeAssertions()
    {
        $this->assertArrayKeysEqual(['a', 'b'], ['a' => 'alfa', 'b' => 'beta']);

        $MyChildInstance = new MyChild();
        $this->assertException(Exception::class, [$MyChildInstance, 'second']);
        $this->assertException(Exception::class, [$MyChildInstance, 'second'], 'Exception message...');

        $this->assertFileExtension('jpg', 'file.jpg');
        $this->assertFileExtension('jpeg', 'FILE.JPEG');
        $this->assertFileExtension(['jpg', 'jpeg'], 'file.jpg');

        $file = create_tmp_file('string');
        $this->assertFileMime('text/plain', $file);
        $this->assertFileMime(['inode/x-empty', 'text/plain'], $file);

        $file = create_tmp_file();
        $this->assertFilePerms(IS_WIN ? '0666' : '0600', $file);
        $this->assertFilePerms(IS_WIN ? 0666 : 0600, $file);
        $this->assertFilePerms([0666, 0600], $file);

        $filename = TMP . 'pic.jpg';
        imagejpeg(imagecreatetruecolor(120, 20), $filename);
        $this->assertImageSize(120, 20, $filename);
        $this->assertImageSize('120', '20', $filename);

        $this->assertIsArray([]);
        $this->assertIsArray(['a', 'b']);

        $this->assertIsArrayNotEmpty(['a', 'b']);

        $this->assertIsInt(1);

        $this->assertIsObject(new stdClass());

        $this->assertIsString('string');

        $object = new stdClass();
        $object->a = 'alfa';
        $object->b = 'beta';
        $this->assertObjectPropertiesEqual(['a', 'b'], $object);

        $this->assertSameMethods(MyChild::class, AnotherChild::class);
    }
}

class MyParent
{
    public function first()
    {
    }
}

class MyChild extends MyParent
{
    public function first()
    {
    }

    public function second()
    {
        throw new Exception('Exception message...');
    }
}

class AnotherChild extends MyParent
{
    public function second()
    {
    }
}
