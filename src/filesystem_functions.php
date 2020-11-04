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

if (!function_exists('add_slash_term')) {
    /**
     * Adds the slash term to a path, if it doesn't have one
     * @param string $path Path
     * @return string Path with the slash term
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.2.6
     */
    function add_slash_term(string $path): string
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::addSlashTerm()`');

        return (new Filesystem())->addSlashTerm($path);
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
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.1.7
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    function create_file(string $filename, $data = null, int $dirMode = 0777, bool $ignoreErrors = false): bool
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::createFile()`');

        return (new Filesystem())->createFile($filename, $data, $dirMode, $ignoreErrors);
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
     * @return string Path of temporary filename
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.1.7
     */
    function create_tmp_file($data = null, ?string $dir = null, ?string $prefix = 'tmp'): string
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::createTmpFile()`');

        return (new Filesystem())->createTmpFile($data, $dir, $prefix);
    }
}

if (!function_exists('dir_tree')) {
    /**
     * Returns an array of nested directories and files in each directory
     * @param string $path The directory path to build the tree from
     * @param array|bool $exceptions Either an array of filename or folder names
     *  to exclude or boolean true to not grab dot files/folders
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return array Array of nested directories and files in each directory
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.0.7
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    function dir_tree(string $path, $exceptions = false, bool $ignoreErrors = false): array
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::getDirTree()`');

        return (new Filesystem())->getDirTree($path, $exceptions, $ignoreErrors);
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
        return is_string($perms) ? $perms : sprintf('%04o', $perms);
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
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.0.2
     */
    function get_extension(string $filename): ?string
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::getExtension()`');

        return (new Filesystem())->getExtension($filename);
    }
}

if (!function_exists('is_slash_term')) {
    /**
     * Checks if a path ends in a slash (i.e. is slash-terminated)
     * @param string $path Path
     * @return bool
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.0.3
     */
    function is_slash_term(string $path): bool
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::isSlashTerm()`');

        return (new Filesystem())->isSlashTerm($path);
    }
}

if (!function_exists('is_writable_resursive')) {
    /**
     * Tells whether a directory and its subdirectories are writable.
     *
     * It can also check that all the files are writable.
     * @param string $dirname Path to the directory
     * @param bool $checkOnlyDir If `true`, also checks for all files
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @since 1.0.7
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    function is_writable_resursive(string $dirname, bool $checkOnlyDir = true, bool $ignoreErrors = false): bool
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::isWritableResursive()`');

        return (new Filesystem())->isWritableResursive($dirname, $checkOnlyDir, $ignoreErrors);
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
     * @return bool
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @see unlink_recursive()
     * @since 1.0.6
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    function rmdir_recursive(string $dirname): bool
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::rmdirRecursive()`');

        return (new Filesystem())->rmdirRecursive($dirname);
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
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @throws \Exception
     */
    function rtr(string $path): string
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::rtr()`');

        return (new Filesystem())->rtr($path);
    }
}

if (!function_exists('unlink_recursive')) {
    /**
     * Recursively removes all the files contained in a directory and within its
     *  sub-directories. This function only removes the files, leaving the
     *  directories unaltered.
     *
     * To remove the directory itself and all its contents, use the
     *  `rmdir_recursive()` function instead.
     * @param string $dirname The directory path
     * @param array|bool $exceptions Either an array of files to exclude
     *  or boolean true to not grab dot files
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @deprecated Use instead `Filesystem::addSlashTerm()`
     * @see rmdir_recursive()
     * @since 1.0.7
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    function unlink_recursive(string $dirname, $exceptions = false, bool $ignoreErrors = false): bool
    {
        deprecationWarning('Deprecated. Use instead `Filesystem::unlinkRecursive()`');

        return (new Filesystem())->unlinkRecursive($dirname, $exceptions, $ignoreErrors);
    }
}
