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

if (!function_exists('debug')) {
    /**
     * Prints out debug information about given variable.
     *
     * Alias for the `dump()` global function provided by `VarDumper` component.
     * @return void
     * @since 1.2.11
     */
    function debug(): void
    {
        call_user_func_array('dump', func_get_args());
    }
}

if (!function_exists('dd')) {
    /**
     * Prints out debug information about given variable and dies
     * @return void
     * @since 1.2.11
     */
    function dd(): void
    {
        call_user_func_array('dump', func_get_args());
        die(1);
    }
}
