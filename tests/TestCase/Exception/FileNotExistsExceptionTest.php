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

namespace Tools\Test\Exception;

use Tools\Exception\FileNotExistsException;
use Tools\TestSuite\TestCase;

/**
 * FileNotExistsExceptionTest class
 */
class FileNotExistsExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        $file = ROOT . 'dir' . DS . 'noExistingFile';
        try {
            throw new FileNotExistsException(null, 0, null, $file);
        } catch (FileNotExistsException $e) {
            $this->assertSame('Filename `dir/noExistingFile` does not exist', $e->getMessage());
            $this->assertSame($file, $e->getFilePath());
        }

        try {
            throw new FileNotExistsException();
        } catch (FileNotExistsException $e) {
            $this->assertSame('Filename does not exist', $e->getMessage());
            $this->assertNull($e->getFilePath());
        }
    }
}
