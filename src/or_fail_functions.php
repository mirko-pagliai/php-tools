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

if (!function_exists('file_exists_or_fail')) {
    /**
     * Checks whether a file or directory exists and throws an exception if the
     *  file does not exist
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     */
    function file_exists_or_fail($filename, $message = null, $exception = FileNotExistsException::class)
    {
        if (!$message) {
            $message = sprintf('File or directory `%s` does not exist', rtr($filename));
        }
        is_true_or_fail(is_readable($filename), $message, $exception);

        return $filename;
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
     * @since 1.2.6
     */
    function in_array_or_fail($value, $array, $message = null, $exception = NotInArrayException::class)
    {
        if (!$message && is_stringable($value) == 'The value is not in array') {
            $message = sprintf('The value `%s` is not in array', (string)$value);
        }
        is_true_or_fail(in_array($value, $array), $message, $exception);

        return $value;
    }
}

if (!function_exists('is_dir_or_fail')) {
    /**
     * Tells whether the filename is a directory and throws an exception if the
     *  filename is not a directory
     * @param string $filename Path to the directory
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\NotDirectoryException
     */
    function is_dir_or_fail($filename, $message = null, $exception = NotDirectoryException::class)
    {
        if (!$message) {
            $message = sprintf('Filename `%s` is not a directory', rtr($filename));
        }
        is_true_or_fail(is_dir($filename), $message, $exception);

        return $filename;
    }
}

if (!function_exists('is_positive_or_fail')) {
    /**
     * Throws an exception if the value is not a positive
     * @param mixed $value The value you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @since 1.2.5
     * @throws \Tools\Exception\NotPositiveException
     */
    function is_positive_or_fail($value, $message = null, $exception = NotPositiveException::class)
    {
        if (!$message && is_stringable($value)) {
            $message = sprintf('The value `%s` is not a positive', (string)$value);
        }
        is_true_or_fail(is_positive($value), $message, $exception);

        return $value;
    }
}

if (!function_exists('is_readable_or_fail')) {
    /**
     * Tells whether a file exists and is readable and throws an exception if
     *  the file is not readable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\NotReadableException
     */
    function is_readable_or_fail($filename, $message = null, $exception = NotReadableException::class)
    {
        if (!$message) {
            $message = sprintf('File or directory `%s` is not readable', rtr($filename));
        }
        is_true_or_fail(is_readable($filename), $message, $exception);

        return $filename;
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
     * @since 1.1.7
     * @throws \Exception
     */
    function is_true_or_fail($value, $message = 'The value is not equal to `true`', $exception = \ErrorException::class)
    {
        if ($value) {
            return $value;
        }

        if ($message instanceof \Exception || (is_string($message) && class_exists($message))) {
            list($exception, $message) = [$message, 'The value is not equal to `true`'];
        }

        if (!$exception instanceof \Exception) {
            if (!is_string($exception)) {
                trigger_error('`$exception` parameter must be a string');
            }
            if (!class_exists($exception)) {
                trigger_error(sprintf('Class `%s` does not exist', $exception));
            }
            $exception = new $exception($message);
        }

        if (!$exception instanceof \Exception) {
            trigger_error(sprintf('`%s` is not and instance of `Exception`', get_class($exception)));
        }

        throw $exception;
    }
}

if (!function_exists('is_writable_or_fail')) {
    /**
     * Tells whether the filename is writable and throws an exception if the
     *  file is not writable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\NotWritableException
     */
    function is_writable_or_fail($filename, $message = null, $exception = NotWritableException::class)
    {
        if (!$message) {
            $message = sprintf('File or directory `%s` is not writable', rtr($filename));
        }
        is_true_or_fail(is_writable($filename), $message, $exception);

        return $filename;
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
     * @throws \Tools\Exception\KeyNotExistsException
     */
    function key_exists_or_fail($key, array $array, $message = null, $exception = KeyNotExistsException::class)
    {
        foreach ((array)$key as $name) {
            $result = array_key_exists($name, $array);
            if (!$result && !$message) {
                $message = sprintf('Key `%s` does not exist', $name);
            }
            is_true_or_fail($result, $message, $exception);
        }

        return $key;
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
     * @since 1.1.14
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    function property_exists_or_fail($object, $property, $message = null, $exception = PropertyNotExistsException::class)
    {
        $messageWasNull = false;
        foreach ((array)$property as $name) {
            if (!$message || $messageWasNull) {
                $messageWasNull = true;
                $message = sprintf('Object does not have `%s` property', $name);
            }
            $result = method_exists($object, 'has') ? $object->has($name) : property_exists($object, $name);
            is_true_or_fail($result, $message, $exception);
        }

        return $property;
    }
}
