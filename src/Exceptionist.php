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

namespace Tools;

use ArgumentCountError;
use BadMethodCallException;
use ErrorException;
use Exception;
use Throwable;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Exception\PropertyNotExistsException;
use Tools\Filesystem;
use TypeError;

/**
 * Exceptionist.
 * @method static mixed fileNotExists(string $filename, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static array isArray($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isBool($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isCallable($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static string isDir(string $filename, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isFloat($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isInt($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isIterable($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isNotArray($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isNotPositive($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isNull($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isObject($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isPositive($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isResource($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isString($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
 * @method static mixed isUrl($value, string $message = '', \Throwable|class-string<\Throwable> $exception = \Exception::class)
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
    public static function __callStatic(string $name, array $arguments)
    {
        $negative = $result = false;
        $phpName = uncamelcase($name);

        //Handles calls containing with the "Not" word (e.g. `isNotArray()` or `fileNotExists()`)
        if (preg_match('/^(\w+)Not([A-Z]\w*)$/', $name, $matches)) {
            $negative = true;
            $phpName = uncamelcase($matches[1] . $matches[2]);
        }

        //Calls the PHP function and gets the result
        [$arguments, $message, $exception] = $arguments + [[], '', Exception::class];
        try {
            if (!is_callable($phpName)) {
                throw new Exception(sprintf('Function `%s()` does not exist', $phpName));
            }
            $rFunction = new \ReflectionFunction($phpName);
            $result = call_user_func_array($phpName, $rFunction->getNumberOfParameters() > 1 && is_array($arguments) ? $arguments : [$arguments]);
        } catch (ArgumentCountError | Exception | TypeError $e) {
            trigger_error(sprintf('Error calling `%s()`: %s', $phpName, $e->getMessage()));
        }

        //Calls `isFalse()` or `isTrue()` method, with that result and returns arguments
        if (!$message) {
            $message = sprintf('`%s::%s()` returned `false`', __CLASS__, $name);
        }
        /** @var callable $callback */
        $callback = [__CLASS__, $negative ? 'isFalse' : 'isTrue'];
        forward_static_call($callback, $result, $message, $exception);

        return $arguments;
    }

    /**
     * Checks whether an array key exists.
     *
     * If you pass an array of keys, they will all be checked.
     * @param string|int|array<string|int> $key Key to check or an array of keys
     * @param array $array An array with keys to check
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return mixed
     * @throws \Tools\Exception\KeyNotExistsException
     */
    public static function arrayKeyExists($key, array $array, ?string $message = '', $exception = KeyNotExistsException::class)
    {
        foreach ((array)$key as $name) {
            self::isTrue(array_key_exists($name, $array), $message ?: 'Key `' . $name . '` does not exist', $exception);
        }

        return $key;
    }

    /**
     * Checks whether a file or directory exists
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     */
    public static function fileExists(string $filename, ?string $message = '', $exception = FileNotExistsException::class): string
    {
        self::isTrue(file_exists($filename), $message ?: 'File or directory `' . Filesystem::instance()->rtr($filename) . '` does not exist', $exception);

        return $filename;
    }

    /**
     * Checks if a value exists in an array
     * @param mixed $needle The searched value
     * @param array $haystack The array
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return mixed
     * @since 1.5.8
     * @throws \Tools\Exception\NotInArrayException
     */
    public static function inArray($needle, array $haystack, ?string $message = '', $exception = NotInArrayException::class)
    {
        $message = $message ?: 'The value' . (is_stringable($needle) ? ' `' . (string)$needle . '`' : '') . ' does not exist in array' . (is_stringable($haystack) ? ' `' . array_to_string($haystack) . '`' : '');
        self::isTrue(in_array($needle, $haystack, true), $message, $exception);

        return $needle;
    }

    /**
     * Checks whether a value is `false`
     * @param mixed $value The value you want to check
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return mixed
     * @since 1.5.10
     * @throws \Exception
     */
    public static function isFalse($value, ?string $message = '', $exception = ErrorException::class)
    {
        self::isTrue(!$value, $message, $exception);

        return $value;
    }

    /**
     * Checks whether an object is an instance of `$class`
     * @param object $object The object you want to check
     * @param string $class The class that the object should be an instance of
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return object
     * @since 1.4.7
     * @throws \Tools\Exception\ObjectWrongInstanceException
     */
    public static function isInstanceOf(object $object, string $class, ?string $message = '', $exception = ObjectWrongInstanceException::class): object
    {
        self::isTrue($object instanceof $class, $message ?: sprintf('Object `%s` is not an instance of `%s`', get_class($object), $class), $exception);

        return $object;
    }

    /**
     * Checks whether a file or directory exists and is readable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotReadableException
     */
    public static function isReadable(string $filename, ?string $message = '', $exception = NotReadableException::class): string
    {
        self::fileExists($filename, $message, $exception);

        self::isTrue(is_readable($filename), $message ?: sprintf('File or directory `%s` is not readable', Filesystem::instance()->rtr($filename)), $exception);

        return $filename;
    }

    /**
     * Checks whether a file or directory exists and is writable
     * @param string $filename Path to the file or directory
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return string
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotWritableException
     */
    public static function isWritable(string $filename, ?string $message = '', $exception = NotWritableException::class): string
    {
        self::fileExists($filename, $message, $exception);

        self::isTrue(is_writable($filename), $message ?: sprintf('File or directory `%s` is not writable', Filesystem::instance()->rtr($filename)), $exception);

        return $filename;
    }

    /**
     * Checks whether a class method exists
     * @param class-string|object $object An object instance or a class name
     * @param string $methodName The method name
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return array<string> Array with class name and method name
     * @since 1.4.3
     * @throws \BadMethodCallException
     */
    public static function methodExists($object, string $methodName, ?string $message = '', $exception = BadMethodCallException::class): array
    {
        $object = is_string($object) ? $object : get_class($object);
        self::isTrue(method_exists($object, $methodName), $message ?: sprintf('Method `%s::%s()` does not exist', $object, $methodName), $exception);

        return [$object, $methodName];
    }

    /**
     * Checks whether an object proprerty exists.
     *
     * If the object owns the `has()` method, it uses that method. Otherwise it
     *  use the `property_exists()` function.
     * @param object $object The class name or an object of the class to test for
     * @param string|array<string> $property Name of the property or an array of names
     * @param string|null $message The failure message that will be appended to
     *  the generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return string|array<string>
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    public static function objectPropertyExists(object $object, $property, ?string $message = '', $exception = PropertyNotExistsException::class)
    {
        foreach ((array)$property as $name) {
            $result = method_exists($object, 'has') ? $object->has($name) : property_exists($object, $name);
            self::isTrue($result, $message ?: sprintf('Property `%s::$%s` does not exist', get_class($object), $name), $exception);
        }

        return $property;
    }

    /**
     * Checks whether a value is `true`
     * @param mixed $value The value you want to check
     * @param string|null $message The failure message that will be appended to the
     *  generated message
     * @param \Throwable|class-string<\Throwable> $exception The exception class you want to set
     * @return mixed
     * @throws \Exception
     */
    public static function isTrue($value, ?string $message = '', $exception = ErrorException::class)
    {
        if ($value) {
            return $value;
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

        if ($exception instanceof \Throwable) {
            throw $exception;
        }

        //Tries to set file and line that throwned the exception
        if ($exception == ErrorException::class || is_subclass_of($exception, ErrorException::class)) {
            foreach (debug_backtrace() as $backtrace) {
                if (array_key_exists('file', $backtrace) && array_key_exists('line', $backtrace) && $backtrace['file'] != __FILE__) {
                    throw new $exception($message, 0, E_ERROR, $backtrace['file'], $backtrace['line']);
                }
            }
        }

        throw new $exception($message);
    }
}
