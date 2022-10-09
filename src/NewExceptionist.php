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
use ReflectionException;
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
 * @method static string classExists(string $className, string $message = '', string $exception = \ErrorException::class)
 * @method static string fileNotExists(string $filename, string $message = '', string $exception = \ErrorException::class)
 * @method static array isArray($value, string $message = '', string $exception = \ErrorException::class)
 * @method static bool isBool($value, string $message = '', string $exception = \ErrorException::class)
 * @method static callable isCallable($value, string $message = '', string $exception = \ErrorException::class)
 * @method static string isDir(string $filename, string $message = '', string $exception = \ErrorException::class)
 * @method static float isFloat($value, string $message = '', string $exception = \ErrorException::class)
 * @method static int isInt($value, string $message = '', string $exception = \ErrorException::class)
 * @method static iterable isIterable($value, string $message = '', string $exception = \ErrorException::class)
 * @method static mixed isNotArray($value, string $message = '', string $exception = \ErrorException::class)
 * @method static mixed isNotPositive($value, string $message = '', string $exception = \ErrorException::class)
 * @method static null isNull($value, string $message = '', string $exception = \ErrorException::class)
 * @method static object isObject($value, string $message = '', string $exception = \ErrorException::class)
 * @method static int isPositive($value, string $message = '', string $exception = \ErrorException::class)
 * @method static resource isResource($value, string $message = '', string $exception = \ErrorException::class)
 * @method static string isString($value, string $message = '', string $exception = \ErrorException::class)
 * @method static string isUrl(string $value, string $message = '', string $exception = \ErrorException::class)
 * @todo write that it does not accept exceptions already instantiated, but only as class-name
 * @todo write that messages are not nullable
 * @todo do something because methods (except `isFalse()` and `isTrue()`) no longer accept an exception as a parameter, but use the default
 */
class NewExceptionist
{
    /**
     * Internal method to build and exception
     * @template PassedException of \ErrorException
     * @param string $message The message for the exception
     * @param class-string<PassedException> $exception The exception
     * @return PassedException
     * @todo add `@since` tag
     */
    private static function buildException(string $message, string $exception): ErrorException
    {
        if ($exception !== ErrorException::class && !is_subclass_of($exception, ErrorException::class)) {
            trigger_error('The exception must be an `ErrorException` or must extend it');
        }

        foreach (debug_backtrace() as $backtrace) {
            if (array_key_exists('file', $backtrace) && array_key_exists('line', $backtrace) && $backtrace['file'] != __FILE__) {
                $e = new $exception($message, 0, E_ERROR, $backtrace['file'], $backtrace['line']);
                break;
            }
        }

        return $e ?? new $exception($message);
    }

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
     * @template RealArguments of mixed
     * @param string $name Method name
     * @param array{0: RealArguments, 1?: string, 2?: class-string<\ErrorException>} $arguments Method arguments
     * @return RealArguments
     */
    public static function __callStatic(string $name, array $arguments)
    {
        /**
         * Gets the name of the corresponding php function, for example `isInt()` becomes `is_int()`. For "negative"
         *  functions (e.g. `isNotArray()` or `fileNotExists()`), `$negative` will be `true`.
         */
        $negative = false;
        $phpName = uncamelcase($name);
        if (preg_match('/^(\w+)Not([A-Z]\w*)$/', $name, $matches)) {
            $negative = true;
            $phpName = uncamelcase($matches[1] . $matches[2]);
        }

        //Split the `$arguments` parameter, which contains the arguments and (optional) the message and exception
        $message = $arguments[1] ?? '';
        $exception = $arguments[2] ?? ErrorException::class;
        $arguments = $arguments[0];

        //Calls the PHP function and takes the result
        try {
            $rFunction = new ReflectionFunction($phpName);
            /** @var callable $phpName */
            $result = call_user_func_array($phpName, $rFunction->getNumberOfParameters() > 1 && is_array($arguments) ? $arguments : [$arguments]);
        } catch (ArgumentCountError | ReflectionException | TypeError $e) {
            trigger_error('Error calling `' . $phpName . '()`: ' . $e->getMessage());
        }

        //Now calls `isFalse()` or `isTrue()` method and performs the check, with that result and returns arguments
        if (!$message) {
            $message = sprintf('`%s::%s()` returned `false`', __CLASS__, $name);
        }
        $callback = [__CLASS__, $negative ? 'isFalse' : 'isTrue'];
        forward_static_call($callback, $result ?? false, $message, $exception);

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
     * @return Keys
     * @throws \Tools\Exception\KeyNotExistsException
     */
    public static function arrayKeyExists($key, array $array, string $message = '')
    {
        foreach ((array)$key as $sKey) {
            if (!key_exists($sKey, $array)) {
                /** @var \Tools\Exception\KeyNotExistsException $e */
                $e = self::buildException($message ?: 'Key `' . $sKey . '` does not exist', KeyNotExistsException::class);
                throw $e;
            }
        }

        return $key;
    }

    /**
     * Checks whether a file or directory exists
     * @template ExistingFilename as string
     * @param ExistingFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @return ExistingFilename
     * @throws \Tools\Exception\FileNotExistsException
     */
    public static function fileExists(string $filename, string $message = ''): string
    {
        if (file_exists($filename)) {
            return $filename;
        }

        /** @var \Tools\Exception\FileNotExistsException $e */
        $e = self::buildException($message ?: 'File or directory `' . $filename . '` does not exist', FileNotExistsException::class);
        throw $e;
    }

    /**
     * Checks if a value exists in an array
     * @template Needle
     * @param Needle $needle The searched value
     * @param array $haystack The array
     * @param string $message The failure message that will be appended to the generated message
     * @return Needle
     * @since 1.5.8
     * @throws \Tools\Exception\NotInArrayException
     */
    public static function inArray($needle, array $haystack, string $message = '')
    {
        if (in_array($needle, $haystack, true)) {
            return $needle;
        }

        $message = $message ?: 'The value' . (is_stringable($needle) ? ' `' . $needle . '`' : '') . ' does not exist in array' . (is_stringable($haystack) ? ' `' . array_to_string($haystack) . '`' : '');
        /** @var \Tools\Exception\NotInArrayException $e */
        $e = self::buildException($message, NotInArrayException::class);
        throw $e;
    }

    /**
     * Checks whether an object is an instance of `$class`
     * @template InstancedObject as object
     * @param InstancedObject $object The object you want to check
     * @param class-string $class The class that the object should be an instance of
     * @param string $message The failure message that will be appended to the generated message
     * @return InstancedObject
     * @throws \Tools\Exception\ObjectWrongInstanceException
     * @since 1.4.7
     */
    public static function isInstanceOf(object $object, string $class, string $message = ''): object
    {
        if ($object instanceof $class) {
            return $object;
        }

        /** @var \Tools\Exception\ObjectWrongInstanceException $e */
        $e = self::buildException($message ?: 'Object `' . get_class($object) . '` is not an instance of `' . $class . '`', ObjectWrongInstanceException::class);
        throw $e;
    }

    /**
     * Checks whether a value is `false`
     * @template FalseException of \ErrorException
     * @template FalseValue of mixed
     * @param FalseValue $value The value you want to check
     * @param string $message The failure message that will be appended to the generated message
     * @param class-string<FalseException> $exception The exception class you want to set
     * @return FalseValue
     * @throws FalseException
     * @since 1.5.10
     */
    public static function isFalse($value, string $message = '', string $exception = ErrorException::class)
    {
        self::isTrue(!$value, $message, $exception);

        return $value;
    }

    /**
     * Checks whether a file or directory exists and is readable
     * @template ReadableFilename as string
     * @param ReadableFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @return ReadableFilename
     * @throws \Tools\Exception\NotReadableException
     */
    public static function isReadable(string $filename, string $message = ''): string
    {
        if (is_readable($filename)) {
            return $filename;
        }

        /** @var \Tools\Exception\NotReadableException $e */
        $e = self::buildException($message ?: 'File or directory `' . $filename . '` is not readable', NotReadableException::class);
        throw $e;
    }

    /**
     * Checks whether a value is `true`
     * @template TrueException of \ErrorException
     * @template TrueValue
     * @param TrueValue $value The value you want to check
     * @param string $message The failure message that will be appended to the generated message
     * @param class-string<TrueException> $exception The exception class you want to set
     * @return TrueValue
     * @throws TrueException
     * @noinspection PhpConditionAlreadyCheckedInspection
     */
    public static function isTrue($value, string $message = '', string $exception = ErrorException::class)
    {
        if ($value) {
            return $value;
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

        throw self::buildException($message, $exception);
    }

    /**
     * Checks whether a file or directory exists and is writable
     * @template WritableFilename as string
     * @param WritableFilename $filename Path to the file or directory
     * @param string $message The failure message that will be appended to the generated message
     * @return WritableFilename
     * @throws \Tools\Exception\NotWritableException
     */
    public static function isWritable(string $filename, string $message = ''): string
    {
        if (is_writable($filename)) {
            return $filename;
        }

        /** @var \Tools\Exception\NotWritableException $e */
        $e = self::buildException($message ?: 'File or directory `' . $filename . '` is not writable', NotWritableException::class);
        throw $e;
    }

    /**
     * Checks whether a class method exists
     * @template ExistingMethod of string
     * @template RelativeObject as object
     * @param class-string<RelativeObject>|RelativeObject $object An object instance or a class name
     * @param ExistingMethod $methodName The method name
     * @param string $message The failure message that will be appended to the generated message
     * @return array{class-string<RelativeObject>|RelativeObject, ExistingMethod} Array with class name and method name
     * @throws \Tools\Exception\MethodNotExistsException
     * @since 1.4.3
     */
    public static function methodExists($object, string $methodName, string $message = ''): array
    {
        if (method_exists($object, $methodName)) {
            return [$object, $methodName];
        }

        $objectAsString = is_object($object) ? get_class($object) : $object;

        /** @var \Tools\Exception\MethodNotExistsException $e */
        $e = self::buildException($message ?: sprintf('Method `%s::%s()` does not exist', $objectAsString, $methodName), MethodNotExistsException::class);
        throw $e;
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
     * @return ExistingProperty
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    public static function objectPropertyExists(object $object, $property, string $message = '')
    {
        foreach ((array)$property as $sProperty) {
            $result = method_exists($object, 'has') ? $object->has($sProperty) : property_exists($object, $sProperty);
            if (!$result) {
                /** @var \Tools\Exception\PropertyNotExistsException $e */
                $e = self::buildException($message ?: 'Property `' . get_class($object) . '::$' . $sProperty . '` does not exist', PropertyNotExistsException::class);
                throw $e;
            }
        }

        return $property;
    }
}
