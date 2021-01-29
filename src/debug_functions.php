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

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

VarDumper::setHandler(function ($var) {
    $template = '
%s
########## DEBUG ##########
%s###########################';
    $cloner = new VarCloner();
    if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
        $backtrace = array_reverse(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        $backtrace = array_values(array_filter($backtrace, function ($current) {
            return key_exists('file', $current);
        }));
        $key = array_search(__FILE__, array_column($backtrace, 'file'));
        $key = $key ? (int)$key - 1 : count($backtrace) - 3;
        $lineInfo = sprintf('%s (line %s)', $backtrace[$key]['file'], $backtrace[$key]['line']);
        $dumper = new CliDumper();
        printf($template, $lineInfo, $dumper->dump($cloner->cloneVar($var), true));
    } else {
        $dumper = new HtmlDumper();
        $dumper->dump($cloner->cloneVar($var));
    }
});

if (!function_exists('debug') && function_exists('dump')) {
    /**
     * Prints out debug information about given variable.
     *
     * Alias for the `dump()` global function provided by `VarDumper` component.
     * @param mixed $var Variable you want to debug
     * @return void
     * @link https://symfony.com/doc/current/components/var_dumper.html#the-dump-function
     * @since 1.2.11
     */
    function debug($var): void
    {
        call_user_func('dump', $var);
    }
}

if (!function_exists('dd') && function_exists('dump')) {
    /**
     * Prints out debug information about given variable and dies
     * @param mixed $var Variable you want to debug
     * @return void
     * @since 1.2.11
     */
    function dd($var): void
    {
        call_user_func('dump', $var);
        die(1);
    }
}
