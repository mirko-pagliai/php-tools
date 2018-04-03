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
if (!function_exists('is_json')) {
    /**
     * Checks if a string is JSON
     * @param string $string String
     * @return bool
     */
    function is_json($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('is_positive')) {
    /**
     * Checks if a string is a positive number
     * @param string $string String
     * @return bool
     */
    function is_positive($string)
    {
        return is_numeric($string) && $string > 0 && $string == round($string);
    }
}

if (!function_exists('is_url')) {
    /**
     * Checks whether a url is valid
     * @param string $url Url
     * @return bool
     */
    function is_url($url)
    {
        return (bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
    }
}

if (!function_exists('is_win')) {
    /**
     * Returns `true` if the environment is Windows
     * @return bool
     */
    function is_win()
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
}

if (!function_exists('rtr')) {
    /**
     * Returns the relative path (to the ROOT constant) of an absolute path
     * @param string $path Absolute path
     * @return string Relative path
     */
    function rtr($path)
    {
        $root = ROOT;
        $rootLength = strlen($root);
        $isRootSlashTerm = in_array($root[$rootLength - 1], ['/', '\\']);

        if (!$isRootSlashTerm) {
            $root .= preg_match('/^[A-Z]:\\\\/i', $root) || substr($root, 0, 2) === '\\\\' ? '\\' : '/';
        }

        return substr($path, 0, $rootLength) !== $root ? $path : substr($path, $rootLength);
    }
}

if (!function_exists('which')) {
    /**
     * Executes the `which` command and shows the full path of (shell) commands
     * @param string $command Command
     * @return string|null
     */
    function which($command)
    {
        $executable = is_win() ? 'where' : 'which';

        exec(sprintf('%s %s 2>&1', $executable, $command), $path, $exitCode);

        $path = is_win() && !empty($path) ? array_map('escapeshellarg', $path) : $path;

        return $exitCode === 0 && !empty($path[0]) ? $path[0] : null;
    }
}
