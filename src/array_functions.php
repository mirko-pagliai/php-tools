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

if (!function_exists('array_clean')) {
    /**
     * Cleans an array. It filters elements, removes duplicate values and
     *  reorders the keys.
     *
     * Elements will be filtered with the `array_filter()` function. If`$callback`
     *  is `null`, all entries of array equal to `FALSE`  will be removed.
     *
     * The keys will be re-ordered with the `array_values() function only if all
     *  the keys in the original array are numeric.
     * @param array $array Array you want to clean
     * @param callable|null $callback The callback function to filter. If no
     *  callback is supplied, all entries of array equal to `FALSE`  will be
     *  removed
     * @param int $flag Flag determining what arguments are sent to callback
     * @return array
     * @link http://php.net/manual/en/function.array-filter.php
     * @since 1.1.13
     */
    function array_clean(array $array, $callback = null, $flag = 0)
    {
        $keys = array_keys($array);
        $onlyNumKeys = $keys === array_filter($keys, 'is_numeric');
        $array = is_callable($callback) ? array_filter($array, $callback, $flag) : array_filter($array);
        $array = array_unique($array);

        //Performs `array_values()` only if all array keys are numeric
        return $onlyNumKeys ? array_values($array) : $array;
    }
}

if (!function_exists('array_key_first')) {
    /**
     * Returns the first key of an array.
     *
     * This function exists in PHP >= 7.3.
     * @param array $array Array
     * @return string|int Key
     * @link http://php.net/manual/en/function.array-key-first.php
     * @since 1.1.12
     */
    function array_key_first(array $array)
    {
        return array_value_first(array_keys($array));
    }
}

if (!function_exists('array_key_last')) {
    /**
     * Returns the last key of an array.
     *
     * This function exists in PHP >= 7.3.
     * @param array $array Array
     * @return string|int Key
     * @link http://php.net/manual/en/function.array-key-last.php
     * @since 1.1.12
     */
    function array_key_last(array $array)
    {
        return array_value_last(array_keys($array));
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
     */
    function array_unique_recursive(array $array)
    {
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
    function array_value_first(array $array)
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
    function array_value_first_recursive(array $array)
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
    function array_value_last(array $array)
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
    function array_value_last_recursive(array $array)
    {
        $value = array_value_last($array);

        return is_array($value) ? array_value_last_recursive($value) : $value;
    }
}
