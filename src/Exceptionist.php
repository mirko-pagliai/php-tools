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

namespace Tools;

use ErrorException;
use Exception;
use Throwable;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotReadableException;
use Tools\Exception\PropertyNotExistsException;

/**
 * Exceptionist
 * @method array isArray(array $args, string $message, $exception)
 * @method string isDir(string $filename, string $message, $exception)
 * @method mixed isPositive($value, string $message, $exception)
 * @since 1.4.1
 */
class Exceptionist
{
    /**
     * `__callStatic()` magic method.
     *
     * This method allows you to use any PHP function, using the name in
     *  "CamelCase". For example:
     * ```
     * Exceptionist::inArray(['a', ['a', 'b', 'c']], 'Value is not in array', \LogicException::class);
     * ```
     *
     * This will verify the function:
     * ```
     * in_array('a', ['a', 'b', 'c']);
     * ```
     *
     * And it will throw a `LogicException` exception with the passed message,
     *  if the function returns `false`.
     *
     * Another example:
     * ```
     * Exceptionist::isDir('/my/dir/path', 'This is not a directory', \RuntimeException::class);
     * ```
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        //Gets the PHP function name
        $name = uncamelcase($name);
        if (!function_exists($name)) {
            trigger_error(sprintf('Function `%s()` does not exist', $name));
        }

        //Splits and orders arguments
        [$arguments, $message, $exception] = $arguments + [[], '', Exception::class];
        //Calls the PHP function and gets the result
        try {
            $result = call_user_func_array($name, (array)$arguments);
        } catch (Exception $e) {
            trigger_error(sprintf('Error calling `%s()`: %s', $name, $e->getMessage()));
        }

        //Calls the `isTrue` method with that result and returns arguments
        forward_static_call([__CLASS__, 'isTrue'], $result, $message, $exception);

        return $arguments;
    }

    /**
     * Checks whether an array key exists.
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
    public static function arrayKeyExists($key, array $array, $message = '', $exception = KeyNotExistsException::class)
    {
        foreach ((array)$key as $name) {
            $result = array_key_exists($name, $array);
            self::isTrue($result, $message ?: sprintf('Key `%s` does not exist', $name), $exception);
        }

        return $key;
    }

    /**
     * Checks whether a file or directory exists
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     */
    public static function fileExists($filename, $message = '', $exception = FileNotExistsException::class)
    {
        $message = $message ?: sprintf('File or directory `%s` does not exist', rtr($filename));
        self::isTrue(file_exists($filename), $message, $exception);

        return $filename;
    }

    /**
     * Checks whether a file or directory exists and is readable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotReadableException
     */
    public static function isReadable($filename, $message = '', $exception = NotReadableException::class)
    {
        self::fileExists($filename, $message, $exception);

        $message = $message ?: sprintf('File or directory `%s` is not readable', rtr($filename));
        self::isTrue(is_readable($filename), $message, $exception);

        return $filename;
    }

    /**
     * Checks whether a file or directory exists and is writable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotWritableException
     */
    public static function isWritable($filename, $message = '', $exception = NotReadableException::class)
    {
        self::fileExists($filename, $message, $exception);

        $message = $message ?: sprintf('File or directory `%s` is not writable', rtr($filename));
        self::isTrue(is_writable($filename), $message, $exception);

        return $filename;
    }

    /**
     * Checks whether an object proprerty exists.
     *
     * If the object owns the `has()` method, it uses that method. Otherwise it
     *  use the `property_exists()` function.
     * @param object|string $object The class name or an object of the class to test for
     * @param string|array $property Name of the property or an array of names
     * @param string $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    public static function objectPropertyExists($object, $property, $message = '', $exception = PropertyNotExistsException::class)
    {
        foreach ((array)$property as $name) {
            $result = method_exists($object, 'has') ? $object->has($name) : property_exists($object, $name);
            self::isTrue($result, $message ?: sprintf('Object does not have `%s` property', $name), $exception);
        }

        return $property;
    }

    /**
     * Checks whether a value is `true`
     * @param mixed $value The value you want to check
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|string $exception The exception class you want to set
     * @return mixed
     * @throws \Exception
     */
    public static function isTrue($value, $message = '', $exception = ErrorException::class)
    {
        if ($value) {
            return $value;
        }
        if ($message instanceof Throwable || (is_string($message) && class_exists($message))) {
            [$exception, $message] = [$message, ''];
        }
        if (!$exception instanceof Throwable && !is_string($exception)) {
            trigger_error('`$exception` parameter must be an instance of `Throwable` or a string');
        }

        if (!$message) {
            $message = 'The value is not equal to `true`';
            if ($value === false) {
                $message = '`false` is not equal to `true`';
            } elseif (is_null($value)) {
                $message = '`null` is not equal to `true`';
            } elseif (is_array($value)) {
                $message = 'An empty array is not equal to `true`';
            } elseif ($value === '') {
                $message = 'An empty string is not equal to `true`';
            } elseif (is_stringable($value)) {
                $message = 'Value `' . (string)$value . '` is not equal to `true`';
            }
        }

        throw $exception instanceof Throwable ? $exception : new $exception($message);
    }
}
