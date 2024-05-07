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

if (!function_exists('array_clean')) {
    /**
     * Cleans an array, filtering elements, removing duplicate values and reordering the keys.
     *
     * Elements will be filtered with the `array_filter()` function. If `$callback` is `null`, all entries of array
     *  equal to `false` will be removed.
     *
     * The keys will be re-ordered with `array_values()` only if all the keys in the original array are numeric.
     * @param array $array Array you want to clean
     * @param callable|null $callback The callback function to filter. If no callback is supplied, all entries of array
     *  equal to `false` will be removed
     * @param int $flag Flag determining what arguments are sent to callback
     * @return array
     * @link http://php.net/manual/en/function.array-filter.php
     * @since 1.1.13
     * @deprecated `array_clean()` is deprecated and will be removed in a future release
     */
    function array_clean(array $array, ?callable $callback = null, int $flag = 0): array
    {
        trigger_deprecation('php-tools', '1.9.4', '`array_clean()` is deprecated and will be removed in a future release');

        $array = array_unique($callback ? array_filter($array, $callback, $flag) : array_filter($array));
        $keys = array_keys($array);

        //Performs `array_values()` only if all array keys are numeric
        return $keys === array_filter($keys, 'is_numeric') ? array_values($array) : $array;
    }
}

if (!function_exists('array_has_only_numeric_keys')) {
    /**
     * Returns `true` if the array has only numeric keys
     * @param array $array Array
     * @return bool
     * @since 1.4.6
     * @deprecated `array_has_only_numeric_keys()` is deprecated and will be removed in a future release
     */
    function array_has_only_numeric_keys(array $array): bool
    {
        trigger_deprecation('php-tools', '1.9.4', '`array_has_only_numeric_keys()` is deprecated and will be removed in a future release');

        $keys = array_keys($array);

        return $keys === array_filter($keys, 'is_numeric');
    }
}

if (!function_exists('array_unique_recursive')) {
    /**
     * `array_unique()` for multidimensional arrays.
     *
     * Removes duplicate values from an array.
     * @param array $array Multidimensional array
     * @return array
     * @since 1.4.3
     * @deprecated `array_unique_recursive()` is deprecated and will be removed in a future release
     */
    function array_unique_recursive(array $array): array
    {
        trigger_deprecation('php-tools', '1.9.4', '`array_unique_recursive()` is deprecated and will be removed in a future release');

        return array_values(array_map('unserialize', array_unique(array_map('serialize', $array))));
    }
}

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
