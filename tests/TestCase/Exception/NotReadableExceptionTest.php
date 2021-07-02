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

namespace Tools\Test\Exception;

use Tools\Exception\NotReadableException;
use Tools\TestSuite\TestCase;

/**
 * NotReadableExceptionTest class
 */
class NotReadableExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException(): void
    {
        $file = ROOT . 'dir' . DS . 'notReadableFile';
        try {
            throw new NotReadableException('', 0, E_ERROR, '__FILE__', __LINE__, null, $file);
        } catch (NotReadableException $e) {
            $this->assertSame('Filename `dir' . DS . 'notReadableFile` is not readable', $e->getMessage());
            $this->assertSame($file, $e->getFilePath());
        }

        try {
            throw new NotReadableException();
        } catch (NotReadableException $e) {
            $this->assertSame('Filename is not readable', $e->getMessage());
            $this->assertNull($e->getFilePath());
        }
    }
}
