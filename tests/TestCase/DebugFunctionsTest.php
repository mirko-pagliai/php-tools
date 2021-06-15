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
     * @requires OS Linux
     * @test
     */
    public function testDebug(): void
    {
        $expected = '
' . __FILE__ . ' (line ' . (__LINE__ + 5) . ')
########## DEBUG ##########
"my var"
###########################';
        ob_start();
        debug('my var');
        $output = ob_get_clean();
        $this->assertEquals($expected, $output);
    }
}
