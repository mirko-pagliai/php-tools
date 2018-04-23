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
 */
require_once 'vendor/autoload.php';

//This adds `apache_get_modules()` and `apache_get_version()` functions
require_once 'apache_functions.php';

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('COMPARING_FILES', ROOT . 'tests' . DS . 'comparing_files' . DS);
define('TMP', sys_get_temp_dir() . DS . 'php-tools' . DS);

//@codingStandardsIgnoreLine
@mkdir(TMP, 0777, true);
