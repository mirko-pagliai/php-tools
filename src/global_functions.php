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

use Tools\Filesystem;

if (!defined('IS_WIN')) {
    define('IS_WIN', DIRECTORY_SEPARATOR === '\\');
}

if (!function_exists('get_child_methods')) {
    /**
     * Gets the class methods' names. Unlike `get_class_methods()`, this function excludes the methods of the parent class
     * @param string $class Class name
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
     * @param class-string|object $class Classname or object
     * @return string
     * @throws \ReflectionException
     * @since 1.0.2
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpUndefinedClassInspection
     */
    function get_class_short_name(string|object $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }
}

if (!function_exists('is_positive')) {
    /**
     * Checks if a string is a positive number
     * @param float|string|int $string String
     * @return bool
     */
    function is_positive(float|string|int $string): bool
    {
        return is_numeric($string) && $string > 0 && $string == round((float)$string);
    }
}

if (!function_exists('rtr')) {
    /**
     * Fast and convenient alias for `Filesystem::rtr()`
     * @param string $path Absolute path
     * @return string Relative path
     * @see \Tools\Filesystem::rtr()
     * @since 1.7.4
     */
    function rtr(string $path): string
    {
        return Filesystem::rtr($path);
    }
}
