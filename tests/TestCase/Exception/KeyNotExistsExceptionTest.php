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

use Tools\Exception\KeyNotExistsException;
use Tools\TestSuite\TestCase;

/**
 * KeyNotExistsExceptionTest class
 */
class KeyNotExistsExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException(): void
    {
        try {
            throw new KeyNotExistsException(null, 0, E_ERROR, '__FILE__', __LINE__, null, 'a-key');
        } catch (KeyNotExistsException $e) {
            $this->assertSame('Array key `a-key` does not exist', $e->getMessage());
            $this->assertSame('a-key', $e->getKeyName());
        }

        try {
            throw new KeyNotExistsException();
        } catch (KeyNotExistsException $e) {
            $this->assertSame('Array key does not exist', $e->getMessage());
            $this->assertNull($e->getKeyName());
        }
    }
}
