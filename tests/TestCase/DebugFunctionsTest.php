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
 * DebugFunctionsTest class
 */
class DebugFunctionsTest extends TestCase
{
    /**
     * Test for `debug()` global function
     * @test
     */
    public function testDebug()
    {
        $expected = PHP_EOL .
            __FILE__ . ' (line ' . (__LINE__ + 5) . ')' . PHP_EOL .
            '########## DEBUG ##########' . PHP_EOL .
            '"my var"' . PHP_EOL .
            '###########################' . PHP_EOL;
        ob_start();
        debug('my var');
        $output = ob_get_clean();
        $this->assertEquals($expected, $output);
    }
}
