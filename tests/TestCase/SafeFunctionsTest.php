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
 * @since       1.0.4
 */
namespace Tools\Test;

use Tools\TestSuite\TestCase;

/**
 * SafeFunctionsTest class
 */
class SafeFunctionsTest extends TestCase
{
    /**
     * Test for all functions
     * @test
     */
    public function testAllFunctions()
    {
        //These functions are deprecated, so it is not necessary to perform
        //  extensive tests
        $errorReporting = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        $this->assertNotEmpty(safe_copy(create_tmp_file(), TMP . 'copy'));
        $this->assertNotEmpty(safe_create_file(TMP . 'example'));
        $this->assertNotEmpty(safe_create_tmp_file());
        $this->assertNotEmpty(safe_mkdir(TMP . 'dir'));
        $this->assertNotEmpty(safe_rmdir(TMP . 'dir'));
        $this->assertNull(safe_rmdir_recursive(TMP . 'dir'));
        $this->assertNotEmpty(safe_symlink(create_tmp_file(), TMP . 'link'));
        $this->assertNotEmpty(safe_unlink(create_tmp_file()));
        $this->assertNull(safe_unlink_recursive(TMP . 'dir'));
        $this->assertNotEmpty(safe_unserialize(serialize(['test'])));
        error_reporting($errorReporting);
    }
}
