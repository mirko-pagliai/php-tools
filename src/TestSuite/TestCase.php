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
 * @since       1.1.11
 */

namespace Tools\TestSuite;

use Closure;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Tools\Filesystem;

/**
 * TestCase class
 */
abstract class TestCase extends PHPUnitTestCase
{
    use ReflectionTrait;
    use TestTrait;

    /**
     * Teardown any static object changes and restore them.
     *
     * It empties the temporary file directory.
     * @return void
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if (rtrim(TMP, DS) !== rtrim(sys_get_temp_dir(), DS)) {
            Filesystem::instance()->unlinkRecursive(TMP);
        }
    }

    /**
     * Helper method for check deprecation methods
     * @param \Closure $callable callable function that will receive asserts
     * @return void
     * @since 1.8.0
     * @codeCoverageIgnore
     */
    public function deprecated(Closure $callable): void
    {
        $deprecation = false;
        $previousHandler = set_error_handler(
            function ($code, $message, $file, $line, $context = null) use (&$previousHandler, &$deprecation): bool {
                if ($code == E_USER_DEPRECATED) {
                    $deprecation = true;

                    return true;
                }
                if ($previousHandler) {
                    return $previousHandler($code, $message, $file, $line, $context);
                }

                return false;
            }
        );
        try {
            $callable();
        } finally {
        }
        $this->assertTrue($deprecation, 'Should have at least one deprecation warning');
    }
}
