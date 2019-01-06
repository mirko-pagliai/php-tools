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
if (!function_exists('clean_url')) {
    /**
     * Cleans an url, removing all unnecessary parts, as fragment (#),
     *  trailing slash and `www` prefix
     * @param string $url Url
     * @param bool $removeWWW Removes the `www` prefix
     * @param bool $removeTrailingSlash Removes the trailing slash
     * @return string
     * @since 1.0.3
     */
    function clean_url($url, $removeWWW = false, $removeTrailingSlash = false)
    {
        $url = preg_replace('/(\#.*)$/', '', $url);

        if ($removeWWW) {
            $url = preg_replace('/^((http|https|ftp):\/\/)?www\./', '$1', $url);
        }

        if ($removeTrailingSlash) {
            $url = rtrim($url, '/');
        }

        return $url;
    }
}

if (!function_exists('create_file')) {
    /**
     * Creates a file.
     *
     * It also recursively creates the directory where the file will be created.
     * @param string $filename Path to the file where to write the data
     * @param mixed $data The data to write. Can be either a string, an array or
     *  a stream resource
     * @return bool
     * @since 1.1.7
     */
    function create_file($filename, $data = null)
    {
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        return file_put_contents($filename, $data) !== false;
    }
}

if (!function_exists('create_tmp_file')) {
    /**
     * Creates a tenporary file.
     *
     * You can pass a directory where to create the file. If `null`, the file
     *  will be created in `TMP`, if the constant is defined, otherwise in the
     *  temporary directory of the system.
     * @param mixed $data The data to write. Can be either a string, an array or
     *  a stream resource
     * @param string|null $dir The directory where the temporary filename will be created
     * @param string|null $prefix The prefix of the generated temporary filename
     * @return string|bool Path of temporary filename or `false` on failure
     * @since 1.1.7
     */
    function create_tmp_file($data = null, $dir = null, $prefix = 'tmp')
    {
        $filename = tempnam($dir ?: (defined('TMP') ? TMP : sys_get_temp_dir()), $prefix);

        if ($data) {
            file_put_contents($filename, $data);
        }

        return $filename;
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
    function deprecationWarning($message, $stackFrame = 1)
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
    function dir_tree($path, $exceptions = false)
    {
        try {
            $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME | RecursiveDirectoryIterator::CURRENT_AS_SELF | RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);
        } catch (\Exception $e) {
            return [[], []];
        }

        $directories = $files = [];
        $directories[] = rtrim($path, DS);

        if (is_bool($exceptions)) {
            $exceptions = $exceptions ? ['.'] : [];
        }
        $exceptions = (array)$exceptions;

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

if (!function_exists('ends_with')) {
    /**
     * Checks if a string ends with a string
     * @param string $haystack The string
     * @param string $needle The searched value
     * @return bool
     * @since 1.1.6
     */
    function ends_with($haystack, $needle)
    {
        $length = strlen($needle);

        return !$length ?: substr($haystack, -$length) === $needle;
    }
}

if (!function_exists('first_key')) {
    /**
     * Returns the first key of an array
     * @param array $array Array
     * @return int|string
     * @since 1.1.10
     */
    function first_key(array $array)
    {
        return first_value(array_keys($array));
    }
}

if (!function_exists('first_value')) {
    /**
     * Returns the first value of an array
     * @param array $array Array
     * @return mixed
     * @since 1.1.1
     */
    function first_value(array $array)
    {
        return array_values($array)[0];
    }
}

if (!function_exists('first_value_recursive')) {
    /**
     * Returns the first value of an array recursively.
     *
     * In other words, it returns the first value found that is not an array.
     * @param array $array Array
     * @return mixed
     * @since 1.1.10
     */
    function first_value_recursive(array $array)
    {
        $value = first_value($array);

        return is_array($value) ? first_value_recursive($value) : $value;
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
    function get_child_methods($class)
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
    function get_class_short_name($class)
    {
        return (new \ReflectionClass($class))->getShortName();
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
    function get_extension($filename)
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
     * It also removes the `www.` prefix
     * @param string $url Url
     * @return string|null
     * @since 1.0.2
     */
    function get_hostname_from_url($url)
    {
        $host = parse_url($url, PHP_URL_HOST);

        return substr($host, 0, 4) === 'www.' ? substr($host, 4) : $host;
    }
}

if (!function_exists('is_external_url')) {
    /**
     * Checks if an url is external.
     *
     * The check is performed by comparing the URL with the passed hostname.
     * @param string $url Url to check
     * @param string $hostname Hostname for the comparison
     * @return bool
     * @since 1.0.4
     */
    function is_external_url($url, $hostname)
    {
        $currentHost = get_hostname_from_url($url);

        //Url with the same host and relative url are not external
        return $currentHost && strcasecmp($currentHost, $hostname) !== 0;
    }
}

if (!function_exists('is_json')) {
    /**
     * Checks if a string is JSON
     * @param string $string String
     * @return bool
     */
    function is_json($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('is_positive')) {
    /**
     * Checks if a string is a positive number
     * @param string $string String
     * @return bool
     */
    function is_positive($string)
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
    function is_slash_term($path)
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
    function is_url($string)
    {
        return is_string($string)
            && (bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $string);
    }
}

if (!function_exists('is_win')) {
    /**
     * Returns `true` if the environment is Windows
     * @return bool
     */
    function is_win()
    {
        return DIRECTORY_SEPARATOR === '\\';
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
    function is_writable_resursive($dirname, $checkOnlyDir = true)
    {
        list($directories, $files) = dir_tree($dirname);
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

if (!function_exists('last_key')) {
    /**
     * Returns the last key of an array
     * @param array $array Array
     * @return int|string
     * @since 1.1.10
     */
    function last_key(array $array)
    {
        return last_value(array_keys($array));
    }
}

if (!function_exists('last_value')) {
    /**
     * Returns the last value of an array
     * @param array $array Array
     * @return mixed
     * @since 1.1.1
     */
    function last_value(array $array)
    {
        return array_values(array_slice($array, -1))[0];
    }
}

if (!function_exists('last_value_recursive')) {
    /**
     * Returns the last value of an array recursively.
     *
     * In other words, it returns the last value found that is not an array.
     * @param array $array Array
     * @return mixed
     * @since 1.1.10
     */
    function last_value_recursive(array $array)
    {
        $value = last_value($array);

        return is_array($value) ? last_value_recursive($value) : $value;
    }
}

if (!function_exists('rmdir_recursive')) {
    /**
     * Removes a directory and all its contents, including subdirectories and
     *  files.
     *
     * To remove only the files contained in a directory and its
     *  sub-directories, use the `unlink_recursive()` function instead.
     * @param string $dirname Path to the directory
     * @return void
     * @since 1.0.6
     */
    function rmdir_recursive($dirname)
    {
        list($directories, $files) = dir_tree($dirname, false);

        array_map('unlink', $files);
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
    function rtr($path)
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

if (!function_exists('starts_with')) {
    /**
     * Checks if a string starts with a string
     * @param string $haystack The string
     * @param string $needle The searched value
     * @return bool
     * @since 1.1.6
     */
    function starts_with($haystack, $needle)
    {
         return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

if (!function_exists('unlink_recursive')) {
    /**
     * Recursively removes all the files contained in a directory and its
     *  sub-directories
     * @param string $dirname The directory path
     * @param array|bool $exceptions Either an array of files to exclude
     *  or boolean true to not grab dot files
     * @return void
     * @since 1.0.7
     */
    function unlink_recursive($dirname, $exceptions = false)
    {
        list($directories, $files) = dir_tree($dirname, $exceptions);

        //Adds symlinks. `dir_tree()` returns symlinks as directories
        $files += array_filter($directories, 'is_link');

        foreach ($files as $file) {
            is_link($file) && is_dir($file) && is_win() ? rmdir($file) : unlink($file);
        }
    }
}

if (!function_exists('which')) {
    /**
     * Executes the `which` command and shows the full path of (shell) commands
     * @param string $command Command
     * @return string|null
     */
    function which($command)
    {
        $executable = is_win() ? 'where' : 'which';

        exec(sprintf('%s %s 2>&1', $executable, $command), $path, $exitCode);

        $path = is_win() && !empty($path) ? array_map('escapeshellarg', $path) : $path;

        return $exitCode === 0 && !empty($path[0]) ? $path[0] : null;
    }
}
