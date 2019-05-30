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

if (!function_exists('can_be_string')) {
    /**
     * Checks is a value can be converted to string
     * @param mixed $var A var you want to check
     * @return bool
     * @since 1.2.5
     */
    function can_be_string($var)
    {
        return method_exists($var, '__toString') || (is_scalar($var) && !is_null($var));
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

if (!function_exists('create_file')) {
    /**
     * Creates a file. Alias for `mkdir()` and `file_put_contents()`.
     *
     * It also recursively creates the directory where the file will be created.
     * @param string $filename Path to the file where to write the data
     * @param mixed $data The data to write. Can be either a string, an array or
     *  a stream resource
     * @param int $dirMode Mode for the directory, if it does not exist
     * @return bool
     * @since 1.1.7
     */
    function create_file(string $filename, $data = null, int $dirMode = 0777): bool
    {
        @mkdir(dirname($filename), $dirMode, true);

        return file_put_contents($filename, $data) !== false;
    }
}

if (!function_exists('create_tmp_file')) {
    /**
     * Creates a tenporary file. Alias for `tempnam()` and `file_put_contents()`.
     *
     * You can pass a directory where to create the file. If `null`, the file
     *  will be created in `TMP`, if the constant is defined, otherwise in the
     *  temporary directory of the system.
     * @param mixed $data The data to write. Can be either a string, an array or
     *  a stream resource
     * @param string|null $dir The directory where the temporary filename will
     *  be created
     * @param string|null $prefix The prefix of the generated temporary filename
     * @return string|bool Path of temporary filename or `false` on failure
     * @since 1.1.7
     */
    function create_tmp_file($data = null, ?string $dir = null, ?string $prefix = 'tmp')
    {
        $dir = $dir ?: (defined('TMP') ? TMP : sys_get_temp_dir());
        $filename = tempnam($dir, $prefix);

        return create_file($filename, $data) ? $filename : false;
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

if (!function_exists('dir_tree')) {
    /**
     * Returns an array of nested directories and files in each directory
     * @param string $path The directory path to build the tree from
     * @param array|bool $exceptions Either an array of files/folder to exclude
     *  or boolean true to not grab dot files/folders
     * @return array Array of nested directories and files in each directory
     * @since 1.0.7
     */
    function dir_tree(string $path, $exceptions = false): array
    {
        try {
            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME | RecursiveDirectoryIterator::CURRENT_AS_SELF | RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);
        } catch (Exception $e) {
            return [[], []];
        }

        $directories = $files = [];
        $directories[] = rtrim($path, DS);
        $exceptions = (array)(is_bool($exceptions) ? ($exceptions ? ['.'] : []) : $exceptions);

        $skipHidden = false;
        if (in_array('.', $exceptions)) {
            $skipHidden = true;
            unset($exceptions[array_search('.', $exceptions)]);
        }

        foreach ($iterator as $itemPath => $fsIterator) {
            $subPathName = $fsIterator->getSubPathname();

            //Excludes hidden files
            if ($skipHidden && ($subPathName{0} === '.' || strpos($subPathName, DS . '.') !== false)) {
                continue;
            }

            //Excludes the listed files
            if (in_array($fsIterator->getFilename(), $exceptions)) {
                continue;
            }

            if ($fsIterator->isDir()) {
                $directories[] = $itemPath;
            } else {
                $files[] = $itemPath;
            }
        }

        sort($directories);
        sort($files);

        return [$directories, $files];
    }
}

if (!function_exists('fileperms_as_octal')) {
    /**
     * Gets permissions for the given file.
     *
     * Unlike the `fileperms()` function provided by PHP, this function returns
     *  the permissions as four-chars string
     * @link http://php.net/manual/en/function.fileperms.php
     * @param string $filename Path to the file
     * @return string Permissions as four-chars string
     * @since 1.2.0
     */
    function fileperms_as_octal(string $filename): string
    {
        return (string)substr(sprintf('%o', fileperms($filename)), -4);
    }
}

if (!function_exists('fileperms_to_string')) {
    /**
     * Returns permissions from octal value (`0755`) to string (`'0755'`)
     * @param int|string $perms Permissions as octal value
     * @return string Permissions as four-chars string
     * @since 1.2.0
     */
    function fileperms_to_string($perms): string
    {
        return is_string($perms) ? $perms : sprintf("%04o", $perms);
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
     * @param string $class Name of the class
     * @return string
     * @since 1.0.2
     */
    function get_class_short_name(string $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }
}

if (!function_exists('get_extension')) {
    /**
     * Gets the extension from a filename.
     *
     * Unlike other functions, this removes query string and fragments (if the
     *  filename is an url) and knows how to recognize extensions made up of
     *  several parts (eg, `sql.gz`).
     * @param string $filename Filename
     * @return string|null
     * @since 1.0.2
     */
    function get_extension(string $filename): ?string
    {
        //Gets the basename and, if the filename is an url, removes query string
        //  and fragments (#)
        $filename = parse_url(basename($filename), PHP_URL_PATH);

        //On Windows, finds the occurrence of the last slash
        $pos = strripos($filename, '\\');
        if ($pos !== false) {
            $filename = substr($filename, $pos + 1);
        }

        //Finds the occurrence of the first point. The offset is 1, so as to
        //  preserve the hidden files
        $pos = strpos($filename, '.', 1);

        return $pos === false ? null : strtolower(substr($filename, $pos + 1));
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

if (!function_exists('is_slash_term')) {
    /**
     * Checks if a path ends in a slash (i.e. is slash-terminated)
     * @param string $path Path
     * @return bool
     * @since 1.0.3
     */
    function is_slash_term(string $path): bool
    {
        return in_array($path[strlen($path) - 1], ['/', '\\']);
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

if (!function_exists('is_writable_resursive')) {
    /**
     * Tells whether a directory and its subdirectories are writable.
     *
     * It can also check that all the files are writable.
     * @param string $dirname Path to the directory
     * @param bool $checkOnlyDir If `true`, also checks for all files
     * @return bool
     * @since 1.0.7
     */
    function is_writable_resursive(string $dirname, bool $checkOnlyDir = true): bool
    {
        [$directories, $files] = dir_tree($dirname);
        $itemsToCheck = $checkOnlyDir ? $directories : array_merge($directories, $files);

        if (!in_array($dirname, $itemsToCheck)) {
            $itemsToCheck[] = $dirname;
        }

        foreach ($itemsToCheck as $item) {
            if (!is_readable($item) || !is_writable($item)) {
                return false;
            }
        }

        return true;
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

if (!function_exists('rmdir_recursive')) {
    /**
     * Removes the directory itself and all its contents, including
     *  subdirectories and files.
     *
     * To remove only files contained in a directory and its sub-directories,
     *  leaving the directories unaltered, use the `unlink_recursive()`
     *  function instead.
     * @param string $dirname Path to the directory
     * @return void
     * @see unlink_recursive()
     * @since 1.0.6
     */
    function rmdir_recursive(string $dirname): void
    {
        unlink_recursive($dirname);

        [$directories] = dir_tree($dirname, false);
        array_map('rmdir', array_reverse($directories));
    }
}

if (!function_exists('rtr')) {
    /**
     * Returns a path relative to the root.
     *
     * The root path must be set with the `ROOT` environment variable (using the
     *  `putenv()` function) or the `ROOT` constant.
     * @param string $path Absolute path
     * @return string Relative path
     */
    function rtr(string $path): string
    {
        $root = getenv('ROOT') ?: ROOT;
        $rootLength = strlen($root);

        if (!is_slash_term($root)) {
            $root .= preg_match('/^[A-Z]:\\\\/i', $root) || substr($root, 0, 2) === '\\\\' ? '\\' : '/';
            $rootLength = strlen($root);
        }

        return substr($path, 0, $rootLength) !== $root ? $path : substr($path, $rootLength);
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

if (!function_exists('unlink_recursive')) {
    /**
     * Recursively removes all the files contained in a directory and its
     *  sub-directories. This function only removes the files, leaving the
     *  directories unaltered.
     *
     * To remove the directory itself and all its contents, use the
     *  `rmdir_recursive()` function instead.
     * @param string $dirname The directory path
     * @param array|bool $exceptions Either an array of files to exclude
     *  or boolean true to not grab dot files
     * @return void
     * @see rmdir_recursive()
     * @since 1.0.7
     */
    function unlink_recursive(string $dirname, $exceptions = false): void
    {
        [$directories, $files] = dir_tree($dirname, $exceptions);

        //Adds symlinks. `dir_tree()` returns symlinks as directories
        $files += array_filter($directories, 'is_link');

        foreach ($files as $file) {
            is_link($file) && is_dir($file) && IS_WIN ? rmdir($file) : unlink($file);
        }
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
