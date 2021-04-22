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

use stdClass;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\TestSuite\TestCase;

/**
 * ObjectWrongInstanceExceptionTest class
 */
class ObjectWrongInstanceExceptionTest extends TestCase
{
    /**
     * Test for the exception
     * @test
     */
    public function testException()
    {
        $instance = new stdClass();
        try {
            throw new ObjectWrongInstanceException(null, 0, null, $instance);
        } catch (ObjectWrongInstanceException $e) {
            $this->assertSame('Object `stdClass` is not a right instance', $e->getMessage());
            $this->assertSame($instance, $e->getObject());
        }

        try {
            throw new ObjectWrongInstanceException();
        } catch (ObjectWrongInstanceException $e) {
            $this->assertSame('Object is not a right instance', $e->getMessage());
            $this->assertNull($e->getObject());
        }
    }
}
