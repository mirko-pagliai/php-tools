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

if (!defined('IS_WIN')) {
    define('IS_WIN', DIRECTORY_SEPARATOR === '\\');
}

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
    function array_clean(array $array, ?callable $callback = null, int $flag = 0): array
    {
        $keys = array_keys($array);
        $hasOnlyNumericKeys = $keys === array_filter($keys, 'is_numeric');
        $array = is_callable($callback) ? array_filter($array, $callback, $flag) : array_filter($array);
        $array = array_unique($array);

        //Performs `array_values()` only if all array keys are numeric
        return $hasOnlyNumericKeys ? array_values($array) : $array;
    }
}

if (!function_exists('array_key_first')) {
    /**
     * Returns the first key of an array.
     *
     * This function exists in PHP >= 7.3.
     * @param array $array Array
     * @return mixed
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
     * @return mixed
     * @link http://php.net/manual/en/function.array-key-last.php
     * @since 1.1.12
     */
    function array_key_last(array $array)
    {
        return array_value_last(array_keys($array));
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

if (!function_exists('clean_url')) {
    /**
     * Cleans an url. It removes all unnecessary parts, as fragment (#),
     *  trailing slash and `www` prefix
     * @param string $url Url
     * @param bool $removeWWW Removes the www prefix
     * @param bool $removeTrailingSlash Removes the trailing slash
     * @return string
     * @since 1.0.3
     */
    function clean_url(string $url, bool $removeWWW = false, bool $removeTrailingSlash = false): string
    {
        $url = preg_replace('/(\#.*)$/', '', $url);

        if ($removeWWW) {
            $url = preg_replace('/^((http|https|ftp):\/\/)?www\./', '$1', $url);
        }

        return $removeTrailingSlash ? rtrim($url, '/') : $url;
    }
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
    function deprecationWarning(string $message, int $stackFrame = 1): void
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
     * @return array|null
     * @since 1.0.1
     */
    function get_child_methods(string $class): ?array
    {
        $methods = get_class_methods($class);
        $parentClass = get_parent_class($class);

        if ($parentClass) {
            $methods = array_diff($methods, get_class_methods($parentClass));
        }

        return is_array($methods) ? array_values($methods) : null;
    }
}

if (!function_exists('get_class_short_name')) {
    /**
     * Gets the short name of the class, the part without the namespace
     * @param mixed $class Class as name or object
     * @return string
     * @since 1.0.2
     */
    function get_class_short_name($class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }
}

if (!function_exists('get_hostname_from_url')) {
    /**
     * Gets the host name from an url.
     *
     * It also removes the `www` prefix.
     * @param string $url Url
     * @return string|null
     * @since 1.0.2
     */
    function get_hostname_from_url(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        return string_starts_with($host ?? '', 'www.') ? substr($host, 4) : $host;
    }
}

if (!function_exists('is_external_url')) {
    /**
     * Checks if an url is external, relative to the passed hostname
     * @param string $url Url to check
     * @param string $hostname Hostname for the comparison
     * @return bool
     * @since 1.0.4
     */
    function is_external_url(string $url, string $hostname): bool
    {
        $hostForUrl = get_hostname_from_url($url);

        //Url with the same host and relative url are not external
        return $hostForUrl && strcasecmp($hostForUrl, $hostname) !== 0;
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
     * @param string|int $string String
     * @return bool
     */
    function is_positive($string): bool
    {
        return is_numeric($string) && $string > 0 && $string == round($string);
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
        return method_exists($var, '__toString') || (is_scalar($var) && !is_null($var));
    }
}

if (!function_exists('is_url')) {
    /**
     * Checks if a string is a valid url
     * @param string $string String
     * @return bool
     */
    function is_url(string $string): bool
    {
        return (bool)preg_match(
            "/^\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;\(\)]*[-a-z0-9+&@#\/%=~_|\(\)]$/i",
            $string
        );
    }
}

if (!function_exists('objects_map')) {
    /**
     * Executes an object method for all objects of the given arrays
     * @param array $objects An array of objects. Each object must have the
     *  method to be called
     * @param string $method The method to be called for each object
     * @param array $args Optional arguments for the method to be called
     * @return array Returns an array containing all the returned values of the
     *  called method applied to each object
     * @since 1.1.11
     * @throws \BadMethodCallException
     */
    function objects_map(array $objects, string $method, array $args = []): array
    {
        return array_map(function ($object) use ($method, $args) {
            is_true_or_fail(method_exists($object, $method), sprintf(
                'Class `%s` does not have a method `%s`',
                get_class($object),
                $method
            ), \BadMethodCallException::class);

            return call_user_func_array([$object, $method], $args);
        }, $objects);
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

if (!function_exists('url_to_absolute')) {
    /**
     * Builds an absolute url
     * @param string $path Basic path, on which to construct the absolute url
     * @param string $relative Relative url to join
     * @return string
     * @since 1.1.16
     */
    function url_to_absolute(string $path, string $relative): string
    {
        $path = clean_url($path, false, true);
        $path = preg_match('/^(\w+:\/\/.+)\/[^\.\/]+\.[^\.\/]+$/', $path, $matches) ? $matches[1] : $path;

        return \phpUri::parse($path . '/')->join($relative);
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
