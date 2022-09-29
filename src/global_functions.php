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

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Tools\Exceptionist;
use function Symfony\Component\String\u;

if (!defined('IS_WIN')) {
    define('IS_WIN', DIRECTORY_SEPARATOR === '\\');
}

if (!function_exists('array_to_string')) {
    /**
     * Convers an array to a string.
     *
     * For example, from `['a', 1, 0.5, 'c']` to `['a', '1', '0.5', 'c']`.
     * @param array $array Array you want to convert
     * @return string
     * @throws \LogicException In case the array contains non-stringable values
     * @since 1.5.8
     */
    function array_to_string(array $array): string
    {
        return '[' . implode(', ', array_map(function ($v): string {
            if (is_array($v) || is_bool($v) || !is_stringable($v)) {
                throw new LogicException('Cannot convert array to string, some values are not stringable');
            }

            return '\'' . $v . '\'';
        }, $array)) . ']';
    }
}

if (!function_exists('get_child_methods')) {
    /**
     * Gets the class methods' names, but unlike the `get_class_methods()`
     *  function, this function excludes the methods of the parent class
     * @param class-string $class Class name
     * @return array<class-string>
     * @since 1.0.1
     * @throws \LogicException|\Throwable
     */
    function get_child_methods(string $class): array
    {
        Exceptionist::classExists($class, 'Class `' . $class . '` does not exist', LogicException::class);
        $methods = get_class_methods($class);
        $parentClass = get_parent_class($class);

        return array_values($parentClass ? array_diff($methods, get_class_methods($parentClass)) : $methods);
    }
}

if (!function_exists('get_class_short_name')) {
    /**
     * Gets class short name (the part without the namespace)
     * @param mixed $class Classname or object
     * @return string
     * @since 1.0.2
     */
    function get_class_short_name($class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }
}

if (!function_exists('is_html')) {
    /**
     * Checks if a string is HTML
     * @param string $string String
     * @return bool
     * @since 1.1.13
     */
    function is_html(string $string): bool
    {
        return strcasecmp($string, strip_tags($string)) !== 0;
    }
}

if (!function_exists('is_json')) {
    /**
     * Checks if a string is JSON
     * @param string $string String
     * @return bool
     */
    function is_json(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('is_positive')) {
    /**
     * Checks if a string is a positive number
     * @param float|string|int $string String
     * @return bool
     */
    function is_positive($string): bool
    {
        return is_numeric($string) && $string > 0 && $string == round((float)$string);
    }
}

if (!function_exists('is_stringable')) {
    /**
     * Checks is a value can be converted to string.
     *
     * Arrays that can be converted to strings with `array_to_string ()` are
     *  stringable.
     * @param mixed $var A var you want to check
     * @return bool
     * @since 1.2.5
     */
    function is_stringable($var): bool
    {
        if (is_array($var)) {
            try {
                return (bool)array_to_string($var);
            } catch (LogicException $e) {
                return false;
            }
        }

        return is_null($var) ? false : is_scalar($var) || method_exists($var, '__toString');
    }
}

if (!function_exists('objects_map')) {
    /**
     * Executes an object method for all objects of the given arrays
     * @param array<object> $objects An array of objects. Each object must have
     *  the method to be called
     * @param string $method The method to be called for each object
     * @param array $args Optional arguments for the method to be called
     * @return array Returns an array containing all the returned values of the
     *  called method applied to each object
     * @since 1.1.11
     * @throws \BadMethodCallException
     */
    function objects_map(array $objects, string $method, array $args = []): array
    {
        return array_map(function (object $object) use ($method, $args) {
            Exceptionist::isTrue(method_exists($object, '__call') || is_callable([$object, $method]), sprintf(
                'Class `%s` does not have a method `%s`',
                get_class($object),
                $method
            ), \BadMethodCallException::class);

            return call_user_func_array([$object, $method], $args);
        }, $objects);
    }
}

if (!function_exists('slug')) {
    /**
     * Gets a slug from a string
     * @param string $string The string you want to generate the slug from
     * @param bool $lowerCase With `true` the string will be lowercase
     * @return string
     * @see https://symfony.com/doc/current/components/string.html#slugger
     * @since 1.4.1
     */
    function slug(string $string, bool $lowerCase = true): string
    {
        $slug = (string)(new AsciiSlugger())->slug($string);

        return $lowerCase ? strtolower($slug) : $slug;
    }
}

if (!function_exists('uncamelcase')) {
    /**
     * Gets an "uncamelcase" string.
     *
     * For example, from `thisIsAString` to `this_is_a_string`.
     * @param string $string The string you want to uncamelcase
     * @return string
     * @since 1.4.2
     */
    function uncamelcase(string $string): string
    {
        return (string)u($string)->snake();
    }
}

if (!function_exists('which')) {
    /**
     * Finds the executable of a command, like `which` on Unix systems
     * @param string $command Command
     * @return string
     * @throws \Exception
     */
    function which(string $command): string
    {
        return Exceptionist::isTrue((new ExecutableFinder())->find($command), 'Unable to find the executable for the `' . $command . '` command');
    }
}
