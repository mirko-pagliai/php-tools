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

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Tools\Filesystem;

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
     * It empties the temporary file directory.
     * @return void
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     * @throws \Tools\Exception\MethodNotExistsException
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if (rtrim(TMP, DS) !== rtrim(sys_get_temp_dir(), DS)) {
            Filesystem::instance()->unlinkRecursive(TMP);
        }
    }
}
