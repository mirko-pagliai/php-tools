<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Tools\Test;

use PHPUnit\Framework\Error\Deprecated;
use Tools\Apache;
use Tools\TestSuite\TestCase;

/**
 * ApacheTest class
 */
class ApacheTest extends TestCase
{
    /**
     * Test for all functions
     * @test
     */
    public function testAllMethods()
    {
        //The class is deprecated, so it is not necessary to perform
        //  extensive tests
        $errorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertTrue(Apache::isEnabled('mod_rewrite'));
        $this->assertFalse(Apache::isEnabled('mod_noExisting'));
        $this->assertEquals('1.3.29', Apache::version());
        error_reporting($errorReporting);

        $this->assertException(Deprecated::class, function () {
            Apache::isEnabled('mod_rewrite');
        });
        $this->assertException(Deprecated::class, function () {
            Apache::version();
        });
    }
}
