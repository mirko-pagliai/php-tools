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

use Tools\Exception\NotPositiveException;
use Tools\TestSuite\TestCase;

/**
 * NotPositiveExceptionTest class
 */
class NotPositiveExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException(): void
    {
        try {
            throw new NotPositiveException('', 0, E_ERROR, '__FILE__', __LINE__, null, -1);
        } catch (NotPositiveException $e) {
            $this->assertSame('Value `-1` is not a positive', $e->getMessage());
            $this->assertSame(-1, $e->getValue());
        }

        try {
            throw new NotPositiveException();
        } catch (NotPositiveException $e) {
            $this->assertSame('Value is not a positive', $e->getMessage());
            $this->assertNull($e->getValue());
        }

        try {
            throw new NotPositiveException('', 0, E_ERROR, '__FILE__', __LINE__, null, ['no-stringable']);
        } catch (NotPositiveException $e) {
            $this->assertSame('Value `[\'no-stringable\']` is not a positive', $e->getMessage());
            $this->assertSame(['no-stringable'], $e->getValue());
        }
    }
}
