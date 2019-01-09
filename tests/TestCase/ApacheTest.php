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

use Tools\Apache;
use Tools\TestSuite\TestCase;

/**
 * ApacheTest class
 */
class ApacheTest extends TestCase
{
    /**
     * Tests for `isEnabled()` method
     * @test
     */
    public function testIsEnabled()
    {
        $this->assertTrue(Apache::isEnabled('mod_rewrite'));
        $this->assertFalse(Apache::isEnabled('mod_noExisting'));
    }

    /**
     * Tests for `version()` method
     * @test
     */
    public function testVersion()
    {
        $this->assertEquals('1.3.29', Apache::version());
    }
}
