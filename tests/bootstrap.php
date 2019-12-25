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

//This adds `apache_get_modules()` and `apache_get_version()` functions
require_once 'apache_functions.php';

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('TMP', sys_get_temp_dir() . DS . 'php-tools' . DS);

@mkdir(TMP, 0777, true);

if (!function_exists('createSomeFiles')) {
    /**
     * Function to create some files for tests
     * @param array $files Files
     * @return array
     */
    function createSomeFiles(array $files = [])
    {
        $files = $files ?: [
            TMP . 'exampleDir' . DS . '.hiddenDir' . DS . 'file7',
            TMP . 'exampleDir' . DS . '.hiddenFile',
            TMP . 'exampleDir' . DS . 'file1',
            TMP . 'exampleDir' . DS . 'subDir1' . DS . 'file2',
            TMP . 'exampleDir' . DS . 'subDir1' . DS . 'file3',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'file4',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'file5',
            TMP . 'exampleDir' . DS . 'subDir2' . DS . 'subDir3' . DS . 'file6',
        ];

        //Creates directories and files
        array_walk($files, 'create_file');
        @mkdir(TMP . 'exampleDir' . DS . 'emptyDir', 0777, true);

        return $files;
    }
}
