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
use Tools\Filesystem;
use function Symfony\Component\String\u;

if (!defined('IS_WIN')) {
    define('IS_WIN', DIRECTORY_SEPARATOR === '\\');
}

if (!function_exists('array_to_string')) {
    /**
     * Converts an array to a string.
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
     * Gets the class methods' names. Unlike `get_class_methods()`, this function excludes the methods of the parent class
     * @param class-string $class Class name
     * @return string[]
     * @since 1.0.1
     * @throws \LogicException
     */
    function get_child_methods(string $class): array
    {
        if (!class_exists($class)) {
            throw new LogicException('Class `' . $class . '` does not exist');
        }
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
     * @throws \ReflectionException
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
     * @deprecated 1.7.4 Use instead `json_validate()`
     */
    function is_json(string $string): bool
    {
        deprecationWarning('Deprecated. Use instead `json_validate()`');

        return json_validate($string);
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
     * Arrays that can be converted to strings with `array_to_string ()` are stringable.
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

        return !is_null($var) && (is_scalar($var) || method_exists($var, '__toString'));
    }
}

if (!function_exists('objects_map')) {
    /**
     * Executes an object method for all objects of the given arrays
     * @param object[]|string[] $objects An array of objects. Each object must have the method to be called
     * @param string $method The method to be called for each object
     * @param array $args Optional arguments for the method to be called
     * @return array Returns an array containing all the returned values of the called method applied to each object
     * @throws \LogicException
     * @deprecated 1.7.4 deprecated, it will be removed in a later release
     * @since 1.1.11
     */
    function objects_map(array $objects, string $method, array $args = []): array
    {
        deprecationWarning('`objects_map()` is deprecated and will be removed in a later release');

        return array_map(function ($object) use ($method, $args) {
            /** @var callable $callable */
            $callable = [$object, $method];
            if (!is_callable($callable)) {
                throw new BadMethodCallException(sprintf('Method `%s::%s()` is not callable', is_string($object) ? $object : get_class($object), $method));
            }

            return call_user_func_array($callable, $args);
        }, $objects);
    }
}

if (!function_exists('rtr')) {
    /**
     * Fast and convenient alias for `Filesystem::rtr()`
     * @param string $path Absolute path
     * @return string Relative path
     * @throws \ErrorException
     * @see \Tools\Filesystem::rtr()
     * @since 1.7.4
     */
    function rtr(string $path): string
    {
        return Filesystem::rtr($path);
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
     * @throws \LogicException
     * @deprecated 1.7.5 Use instead `Symfony\Component\Process\ExecutableFinder::find()` method
     * @codeCoverageIgnore
     */
    function which(string $command): string
    {
        deprecationWarning('Deprecated. Use instead `' . ExecutableFinder::class . '::find()` method');

        $executableFinder = (new ExecutableFinder())->find($command);
        if (!$executableFinder) {
            throw new LogicException('Unable to find the executable for the `' . $command . '` command');
        }

        return $executableFinder;
    }
}
