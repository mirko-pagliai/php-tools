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
     * @return void
     * @throws \ErrorException
     */
    function file_exists_or_fail($filename)
    {
        if (!file_exists($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` does not exist', rtr($filename)));
        }
    }
}

if (!function_exists('is_dir_or_fail')) {
    /**
     * Tells whether the filename is a directory and throws an exception if the
     *  filename is not a directory
     * @param string $filename Path to the directory
     * @return void
     * @throws \ErrorException
     */
    function is_dir_or_fail($filename)
    {
        file_exists_or_fail($filename);

        if (!is_dir($filename)) {
            throw new \ErrorException(sprintf('`%s` is not a directory', rtr($filename)));
        }
    }
}

if (!function_exists('is_readable_or_fail')) {
    /**
     * Tells whether a file exists and is readable and throws an exception if
     *  the file is not readable
     * @param string $filename Path to the file or directory
     * @return void
     * @throws \ErrorException
     */
    function is_readable_or_fail($filename)
    {
        if (!is_readable($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` is not readable', rtr($filename)));
        }
    }
}

if (!function_exists('is_true_or_fail')) {
    /**
     * Throws an exception if the value is not equal to `true`
     * @param mixed $value The value you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @param string|Expection|null $exception The exception class you want to
     *  set. If `null`, a default `ErrorException` will be used
     * @return void
     * @since 1.1.7
     * @throws ErrorException
     */
    function is_true_or_fail($value, $message = 'The value is not equal to `true`', $exception = null)
    {
        if ((bool)$value) {
            return;
        }

        if (is_string($exception) && class_exists($exception)) {
            $exception = new $exception;

            if (!$exception instanceof \Exception) {
                trigger_error('Invalid Exception');
            }
        } else {
            $exception = \ErrorException::class;
        }

        throw new $exception($message);
    }
}

if (!function_exists('is_writable_or_fail')) {
    /**
     * Tells whether the filename is writable and throws an exception if the
     *  file is not writable
     * @param string $filename Path to the file or directory
     * @return void
     * @throws \ErrorException
     */
    function is_writable_or_fail($filename)
    {
        if (!is_writable($filename)) {
            throw new \ErrorException(sprintf('File or directory `%s` is not writable', rtr($filename)));
        }
    }
}
