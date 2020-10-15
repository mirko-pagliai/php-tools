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
 * @since       1.1.11
 */

namespace Tools\TestSuite;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Tools\ReflectionTrait;

/**
 * TestCase class.
 */
abstract class TestCase extends PHPUnitTestCase
{
    use ReflectionTrait;
    use TestTrait;

    /**
     * Teardown any static object changes and restore them.
     *
     * It empties the temporary files directory.
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        if (add_slash_term(TMP) !== add_slash_term(sys_get_temp_dir())) {
            unlink_recursive(TMP);
        }
    }

    /**
     * Sets up an expectation for an exception to be raised by the code under test.
     *
     * This provides backward compatibility for versions of `phpunit` lower than 8.5.
     * @param string $regularExpression Expected regular expression for the exception message
     * @return void
     * @todo To be removed in a future release
     */
    public function expectExceptionMessageMatches($regularExpression)
    {
        $methodToCall = method_exists(PHPUnitTestCase::class, 'expectExceptionMessageMatches') ? [parent::class, 'expectExceptionMessageMatches'] : [$this, 'expectExceptionMessageRegExp'];
        call_user_func($methodToCall, $regularExpression);
    }
}
