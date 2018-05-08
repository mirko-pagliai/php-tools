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
 * @since       1.0.6
 */
if (!function_exists('file_exists_or_fail')) {
    /**
     * Checks whether a file or directory exists and throws an exception if the
     *  file does not exist
     * @param string $filename Path to the file or directory
     * @throws \ErrorException
     */
    function file_exists_or_fail($filename)
    {
        if (!file_exists($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` does not exist', rtr($filename)));
        }
    }
}

if (!function_exists('is_readable_or_fail')) {
    /**
     * Tells whether a file exists and is readable and throws an exception if
     *  the file is not readable
     * @param string $filename Path to the file or directory
     * @throws \ErrorException
     */
    function is_readable_or_fail($filename)
    {
        if (!is_readable($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` is not readable', rtr($filename)));
        }
    }
}

if (!function_exists('is_writable_or_fail')) {
    /**
     * Tells whether the filename is writable and throws an exception if the
     *  file is not writable
     * @param string $filename Path to the file or directory
     * @throws \ErrorException
     */
    function is_writable_or_fail($filename)
    {
        if (!is_writable($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` is not writable', rtr($filename)));
        }
    }
}

