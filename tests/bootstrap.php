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

require_once 'vendor/autoload.php';

use Tools\Filesystem;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', dirname(__DIR__) . DS);
define('TMP', sys_get_temp_dir() . DS . 'php-tools' . DS);

if (!file_exists(TMP)) {
    mkdir(TMP, 0777, true);
}

if (!function_exists('createSomeFiles')) {
    /**
     * Function to create some files for tests
     * @param array<string> $files Files
     * @return array<string>
     */
    function createSomeFiles(array $files = []): array
    {
        $dir = TMP . 'exampleDir' . DS;
        if (!file_exists($dir)) {
            mkdir($dir . 'emptyDir', 0777, true);
        }

        $files = $files ?: [
            $dir . '.hiddenDir' . DS . 'file7',
            $dir . '.hiddenFile',
            $dir . 'file1',
            $dir . 'subDir1' . DS . 'file2',
            $dir . 'subDir1' . DS . 'file3',
            $dir . 'subDir2' . DS . 'file4',
            $dir . 'subDir2' . DS . 'file5',
            $dir . 'subDir2' . DS . 'subDir3' . DS . 'file6',
        ];

        return array_map([Filesystem::instance(), 'createFile'], $files);
    }
}

require_once ROOT . 'src' . DS . 'deprecation_functions.php';
