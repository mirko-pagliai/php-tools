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

use Symfony\Component\String\Slugger\AsciiSlugger;
use Tools\Exceptionist;
use function Symfony\Component\String\u;

if (!defined('IS_WIN')) {
    define('IS_WIN', DIRECTORY_SEPARATOR === '\\');
}

if (!function_exists('deprecationWarning')) {
    /**
     * Helper method for outputting deprecation warnings
     * @param string $message The message to output as a deprecation warning
     * @param int $stackFrame The stack frame to include in the error. Defaults to 1
     *   as that should point to application/plugin code
     * @return void
     * @since 1.1.7
     */
    function deprecationWarning(string $message, int $stackFrame = 0): void
    {
        if (!(error_reporting() & E_USER_DEPRECATED)) {
            return;
        }

        $trace = debug_backtrace();
        if (isset($trace[$stackFrame])) {
            $frame = $trace[$stackFrame];
            $frame += ['file' => '[internal]', 'line' => '??'];

            $message = sprintf(
                '%s - %s, line: %s' . "\n" .
                ' You can disable deprecation warnings by setting `error_reporting()` to' .
                ' `E_ALL & ~E_USER_DEPRECATED`.',
                $message,
                $frame['file'],
                $frame['line']
            );
        }

        trigger_error($message, E_USER_DEPRECATED);
    }
}

if (!function_exists('get_child_methods')) {
    /**
     * Gets the class methods' names, but unlike the `get_class_methods()`
     *  function, this function excludes the methods of the parent class
     * @param string $class Class name
     * @return array<string>|null
     * @since 1.0.1
     */
    function get_child_methods(string $class): ?array
    {
        if (!class_exists($class)) {
            return null;
        }

        $methods = get_class_methods($class);
        $parentClass = get_parent_class($class);
        if ($parentClass) {
            $methods = array_diff($methods, get_class_methods($parentClass));
        }

        return array_values($methods);
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
     * Checks is a value can be converted to string
     * @param mixed $var A var you want to check
     * @return bool
     * @since 1.2.5
     */
    function is_stringable($var): bool
    {
        return is_null($var) || is_array($var) ? false : is_scalar($var) || method_exists($var, '__toString');
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
            Exceptionist::isTrue(method_exists($object, '__call') || method_exists($object, $method), sprintf(
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

if (!function_exists('string_ends_with')) {
    /**
     * Checks if a string ends with a string
     * @param string $haystack The string
     * @param string $needle The searched value
     * @return bool
     * @since 1.1.12
     */
    function string_ends_with(string $haystack, string $needle): bool
    {
        $length = strlen($needle);

        return !$length ?: substr($haystack, -$length) === $needle;
    }
}

if (!function_exists('string_contains')) {
    /**
     * Checks if a string contains a string
     * @param string $haystack The string
     * @param string $needle The searched value
     * @return bool
     * @since 1.4.0
     */
    function string_contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('string_starts_with')) {
    /**
     * Checks if a string starts with a string
     * @param string $haystack The string
     * @param string $needle The searched value
     * @return bool
     * @since 1.1.12
     */
    function string_starts_with(string $haystack, string $needle): bool
    {
         return substr($haystack, 0, strlen($needle)) === $needle;
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
     * Executes the `which` command and shows the full path of (shell) commands
     * @param string $command Command
     * @return string|null
     */
    function which(string $command): ?string
    {
        exec(sprintf('%s %s 2>&1', IS_WIN ? 'where' : 'which', $command), $path, $exitCode);
        $path = IS_WIN && !empty($path) ? array_map('escapeshellarg', $path) : $path;

        return $exitCode === 0 && !empty($path[0]) ? $path[0] : null;
    }
}
