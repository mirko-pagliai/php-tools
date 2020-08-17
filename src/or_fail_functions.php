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

use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotDirectoryException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotPositiveException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\PropertyNotExistsException;
use Tools\Exceptionist;

if (!function_exists('file_exists_or_fail')) {
    /**
     * Checks whether a file or directory exists and throws an exception if does
     *  not exist
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @deprecated Use `Exceptionist::fileExists()` instead
     * @throws \Tools\Exception\FileNotExistsException
     */
    function file_exists_or_fail($filename, $message = null, $exception = FileNotExistsException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::fileExists()` instead.');

        return Exceptionist::fileExists($filename, $message, $exception);
    }
}

if (!function_exists('in_array_or_fail')) {
    /**
     * Checks if a value exists in an array and throws an exception if the
     *  value is not in array
     * @param mixed $value The searched value
     * @param array $array The array
     * @param string $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @deprecated Use `Exceptionist::inArray()` instead
     * @since 1.2.6
     */
    function in_array_or_fail($value, $array, $message = null, $exception = NotInArrayException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::inArray()` instead.');

        if (!$message && is_stringable($value) == 'The value is not in array') {
            $message = sprintf('The value `%s` is not in array', (string)$value);
        }
        Exceptionist::inArray([$value, $array], $message, $exception);

        return $value;
    }
}

if (!function_exists('is_dir_or_fail')) {
    /**
     * Checks whether the filename is a directory and throws an exception if the
     *  filename is not a directory
     * @param string $filename Path to the directory
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @deprecated Use `Exceptionist::isDir()` instead
     * @throws \Tools\Exception\NotDirectoryException
     */
    function is_dir_or_fail($filename, $message = null, $exception = NotDirectoryException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::isDir()` instead.');

        $message = $message ?: sprintf('Filename `%s` is not a directory', rtr($filename));

        return Exceptionist::isDir($filename, $message, $exception);
    }
}

if (!function_exists('is_positive_or_fail')) {
    /**
     * Checks whether the value is a positive and throws an exception if the
     *  value is not a positive
     * @param mixed $value The value you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @deprecated Use `Exceptionist::isPositive()` instead
     * @since 1.2.5
     * @throws \Tools\Exception\NotPositiveException
     */
    function is_positive_or_fail($value, $message = null, $exception = NotPositiveException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::isPositive()` instead.');

        if (!$message && is_stringable($value)) {
            $message = sprintf('The value `%s` is not a positive', (string)$value);
        }

        return Exceptionist::isPositive($value, $message, $exception);
    }
}

if (!function_exists('is_readable_or_fail')) {
    /**
     * Checks whether a file or directory exists and is readable and throws an
     *  exception if is not readable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @deprecated Use `Exceptionist::isReadable()` instead
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotReadableException
     */
    function is_readable_or_fail($filename, $message = null, $exception = NotReadableException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::isReadable()` instead.');

        return Exceptionist::isReadable($filename, $message, $exception);
    }
}

if (!function_exists('is_true_or_fail')) {
    /**
     * Throws an exception if the value is not equal to `true`.
     *
     * You can also pass the exception as a second parameter, instead of the
     *  message.
     * @param mixed $value The value you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @deprecated Use `Exceptionist::isTrue()` instead
     * @since 1.1.7
     * @throws \Exception
     */
    function is_true_or_fail($value, $message = 'The value is not equal to `true`', $exception = \ErrorException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::isTrue()` instead.');

        return Exceptionist::isTrue($value, $message, $exception);
    }
}

if (!function_exists('is_writable_or_fail')) {
    /**
     * Checks whether a file or directory exists and is writable and throws an
     *  exception if is not writable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @deprecated Use `Exceptionist::isWritable()` instead
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotWritableException
     */
    function is_writable_or_fail($filename, $message = null, $exception = NotWritableException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::isWritable()` instead.');

        return Exceptionist::isWritable($filename, $message, $exception);
    }
}

if (!function_exists('key_exists_or_fail')) {
    /**
     * Checks if the given key or index exists in the array and throws an
     *  exception if the key does not exist.
     *
     * If you pass an array of keys, they will all be checked.
     * @param mixed $key Key to check or an array of keys
     * @param array $array An array with keys to check
     * @param \Throwable|string $message The failure message that will be appended to
     *  the generated message
     * @param string $exception The exception class you want to set
     * @return mixed
     * @deprecated Use `Exceptionist::arrayKeyExists()` instead
     * @throws \Tools\Exception\KeyNotExistsException
     */
    function key_exists_or_fail($key, array $array, $message = null, $exception = KeyNotExistsException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::arrayKeyExists()` instead.');

        return Exceptionist::arrayKeyExists($key, $array, $message, $exception);
    }
}

if (!function_exists('property_exists_or_fail')) {
    /**
     * Checks if a property exists and throws an exception if the property does
     *  not exist.
     *
     * If the object has the `has()` method, it uses that method. Otherwise it
     *  use the `property_exists()` function.
     * @param object|string $object The class name or an object of the class to test for
     * @param string|array $property Name of the property or an array of names
     * @param string $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @deprecated Use `Exceptionist::objectPropertyExists()` instead
     * @since 1.1.14
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    function property_exists_or_fail($object, $property, $message = null, $exception = PropertyNotExistsException::class)
    {
        deprecationWarning('Deprecated. Use `Exceptionist::objectPropertyExists()` instead.');

        return Exceptionist::objectPropertyExists($object, $property, $message, $exception);
    }
}
