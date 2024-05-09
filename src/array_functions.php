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

if (!function_exists('array_value_first')) {
    /**
     * Returns the first value of an array
     * @param array $array Array
     * @return mixed
     * @since 1.1.12
     */
    function array_value_first(array $array): mixed
    {
        return $array ? array_values($array)[0] : null;
    }
}

if (!function_exists('array_value_first_recursive')) {
    /**
     * Returns the first value of an array recursively.
     *
     * In other words, it returns the first value found that is not an array.
     * @param array $array Array
     * @return mixed
     * @since 1.1.12
     */
    function array_value_first_recursive(array $array): mixed
    {
        $value = array_value_first($array);

        return is_array($value) ? array_value_first_recursive($value) : $value;
    }
}

if (!function_exists('array_value_last')) {
    /**
     * Returns the last value of an array
     * @param array $array Array
     * @return mixed
     * @since 1.1.12
     */
    function array_value_last(array $array): mixed
    {
        return $array ? array_values(array_slice($array, -1))[0] : null;
    }
}

if (!function_exists('array_value_last_recursive')) {
    /**
     * Returns the last value of an array recursively.
     *
     * In other words, it returns the last value found that is not an array.
     * @param array $array Array
     * @return mixed
     * @since 1.1.12
     */
    function array_value_last_recursive(array $array): mixed
    {
        $value = array_value_last($array);

        return is_array($value) ? array_value_last_recursive($value) : $value;
    }
}

if (!function_exists('is_array_key_last')) {
    /**
     * Returns `true` if `$key` is the first key of the array
     * @param int|string $key Key you want to check
     * @param array $array Array
     * @return bool
     * @since 1.7.6
     */
    function is_array_key_first(int|string $key, array $array): bool
    {
        return $key === array_key_first($array);
    }
}

if (!function_exists('is_array_key_last')) {
    /**
     * Returns `true` if `$key` is the last key of the array
     * @param int|string $key Key you want to check
     * @param array $array Array
     * @return bool
     * @since 1.7.5
     */
    function is_array_key_last(int|string $key, array $array): bool
    {
        return $key === array_key_last($array);
    }
}
