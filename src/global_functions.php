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
