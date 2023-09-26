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
 * DeprecationFunctionsTest class
 */
class DeprecationFunctionsTest extends TestCase
{
    /**
     * @test
     * @uses \deprecationWarning()
     */
    public function testDeprecationWarning(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        deprecationWarning('This method is deprecated');
        error_reporting($current);

        $this->deprecated(function () {
            deprecationWarning('This method is deprecated');
        });
    }
}
