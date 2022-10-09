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
use ErrorException;
use Exception;
use ReflectionFunction;
use Tools\Exception\FileNotExistsException;
use Tools\Exception\KeyNotExistsException;
use Tools\Exception\MethodNotExistsException;
use Tools\Exception\NotInArrayException;
use Tools\Exception\NotReadableException;
use Tools\Exception\NotWritableException;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Exception\PropertyNotExistsException;
use TypeError;

/**
 * The `Exceptionist`.
 * @template E of \Exception
 * @method static string classExists(string $className, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static string fileNotExists(string $filename, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static array isArray($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static bool isBool($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static callable isCallable($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static string isDir(string $filename, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static float isFloat($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static int isInt($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static iterable isIterable($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static mixed isNotArray($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static mixed isNotPositive($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static null isNull($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static object isObject($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static int isPositive($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static resource isResource($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static string isString($value, string $message = '', \Exception|string $exception = \Exception::class)
 * @method static string isUrl(string $value, string $message = '', \Exception|string $exception = \Exception::class)
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
        [$arguments, $message, $exception] = $arguments + [[], '', ErrorException::class];
        try {
            if (!is_callable($phpName)) {
                throw new Exception(sprintf('Function `%s()` does not exist', $phpName));
            }
            $rFunction = new ReflectionFunction($phpName);
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
     * @template Keys of array-key|array-key[]
     * @param Keys $key Key to check or an array of keys
     * @param array $array An array with keys to check
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return Keys
     * @throws \Tools\Exception\KeyNotExistsException|\Exception
     */
    public static function arrayKeyExists($key, array $array, string $message = '', $exception = KeyNotExistsException::class)
    {
        if (func_num_args() > 3) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        foreach ((array)$key as $name) {
            self::isTrue(array_key_exists($name, $array), $message ?: 'Key `' . $name . '` does not exist', $exception);
        }

        return $key;
    }

    /**
     * Checks whether a file or directory exists
     * @template ExistingFilename as string
     * @param ExistingFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return ExistingFilename
     * @throws \Tools\Exception\FileNotExistsException|\Exception
     */
    public static function fileExists(string $filename, string $message = '', $exception = FileNotExistsException::class): string
    {
        if (func_num_args() > 2) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        self::isTrue(file_exists($filename), $message ?: 'File or directory `' . Filesystem::instance()->rtr($filename) . '` does not exist', $exception);

        return $filename;
    }

    /**
     * Checks if a value exists in an array
     * @template Needle
     * @param Needle $needle The searched value
     * @param array $haystack The array
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return Needle
     * @throws \Tools\Exception\NotInArrayException|\Exception
     * @since 1.5.8
     */
    public static function inArray($needle, array $haystack, string $message = '', $exception = NotInArrayException::class)
    {
        if (func_num_args() > 3) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        $message = $message ?: 'The value' . (is_stringable($needle) ? ' `' . $needle . '`' : '') . ' does not exist in array' . (is_stringable($haystack) ? ' `' . array_to_string($haystack) . '`' : '');
        self::isTrue(in_array($needle, $haystack, true), $message, $exception);

        return $needle;
    }

    /**
     * Checks whether a value is `false`
     * @template FalseValue
     * @param FalseValue $value The value you want to check
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return FalseValue
     * @throws \Exception
     * @since 1.5.10
     */
    public static function isFalse($value, string $message = '', $exception = ErrorException::class)
    {
        self::isTrue(!$value, $message, $exception);

        return $value;
    }

    /**
     * Checks whether an object is an instance of `$class`
     * @template InstancedObject as object
     * @param InstancedObject $object The object you want to check
     * @param string $class The class that the object should be an instance of
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return InstancedObject
     * @throws \Tools\Exception\ObjectWrongInstanceException|\Exception
     * @since 1.4.7
     */
    public static function isInstanceOf(object $object, string $class, string $message = '', $exception = ObjectWrongInstanceException::class): object
    {
        if (func_num_args() > 3) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        self::isTrue($object instanceof $class, $message ?: sprintf('Object `%s` is not an instance of `%s`', get_class($object), $class), $exception);

        return $object;
    }

    /**
     * Checks whether a file or directory exists and is readable
     * @template ReadableFilename as string
     * @param ReadableFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return ReadableFilename
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotReadableException
     * @throws \Exception
     */
    public static function isReadable(string $filename, string $message = '', $exception = NotReadableException::class): string
    {
        if (func_num_args() > 2) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        self::fileExists($filename, $message, $exception);
        self::isTrue(is_readable($filename), $message ?: sprintf('File or directory `%s` is not readable', Filesystem::instance()->rtr($filename)), $exception);

        return $filename;
    }

    /**
     * Checks whether a file or directory exists and is writable
     * @template WritableFilename as string
     * @param WritableFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return WritableFilename
     * @throws \Tools\Exception\FileNotExistsException
     * @throws \Tools\Exception\NotWritableException
     * @throws \Exception
     */
    public static function isWritable(string $filename, string $message = '', $exception = NotWritableException::class): string
    {
        if (func_num_args() > 2) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        self::fileExists($filename, $message, $exception);
        self::isTrue(is_writable($filename), $message ?: sprintf('File or directory `%s` is not writable', Filesystem::instance()->rtr($filename)), $exception);

        return $filename;
    }

    /**
     * Checks whether a class method exists
     * @template ExistingMethod of string
     * @template RelativeObject as object
     * @param class-string<RelativeObject>|RelativeObject $object An object instance or a class name
     * @param ExistingMethod $methodName The method name
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return array{class-string<RelativeObject>, ExistingMethod} Array with class name and method name
     * @throws \Tools\Exception\MethodNotExistsException|\Exception
     * @since 1.4.3
     */
    public static function methodExists($object, string $methodName, string $message = '', $exception = MethodNotExistsException::class): array
    {
        if (func_num_args() > 3) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        $object = is_string($object) ? $object : get_class($object);
        self::isTrue(method_exists($object, $methodName), $message ?: sprintf('Method `%s::%s()` does not exist', $object, $methodName), $exception);

        return [$object, $methodName];
    }

    /**
     * Checks whether an object property exists.
     *
     * If the object owns the `has()` method, it uses that method. Otherwise, it
     *  uses the `property_exists()` function.
     * @template ExistingProperty of string|string[]
     * @param object $object The class name or an object of the class to test for
     * @param ExistingProperty $property Name of the property or an array of names
     * @param string $message The failure message that will be appended to the generated message
     * @param E|class-string<E> $exception The exception class you want to set
     * @return ExistingProperty
     * @throws \Tools\Exception\PropertyNotExistsException|\Exception
     */
    public static function objectPropertyExists(object $object, $property, string $message = '', $exception = PropertyNotExistsException::class)
    {
        if (func_num_args() > 3) {
            deprecationWarning('The `$exception` parameter is deprecated and will be removed in a later release');
        }
        foreach ((array)$property as $name) {
            $result = method_exists($object, 'has') ? $object->has($name) : property_exists($object, $name);
            self::isTrue($result, $message ?: sprintf('Property `%s::$%s` does not exist', get_class($object), $name), $exception);
        }

        return $property;
    }

    /**
     * Checks whether a value is `true`
     * @template TrueValue
     * @param TrueValue $value The value you want to check
     * @param string $message The failure message that will be appended to the generated message
     * @param \Exception|class-string<\Exception> $exception The exception class you want to set
     * @return TrueValue
     * @throws \Exception
     * @noinspection PhpConditionAlreadyCheckedInspection
     */
    public static function isTrue($value, string $message = '', $exception = ErrorException::class)
    {
        if ($value) {
            return $value;
        }

        if (!$exception instanceof Exception && !is_string($exception)) {
            trigger_error('`$exception` parameter must be an instance of `Exception` or a class string');
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
                $message = 'Value `' . $value . '` is not equal to `true`';
            }
        }

        if ($exception instanceof Exception) {
            throw $exception;
        }

        //Tries to set file and line that thrown the exception
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
