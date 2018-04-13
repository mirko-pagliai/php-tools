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

        if (substr($host, 0, 4) === 'www.') {
            return substr($host, 4);
        }

        return $host;
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
        return (bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $string);
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
