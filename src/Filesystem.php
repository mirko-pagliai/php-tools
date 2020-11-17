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
 * @since       1.4.4
 */

namespace Tools;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\Finder\Finder;
use Tools\Exceptionist;

/**
 * Provides basic utility to manipulate the file system.
 */
class Filesystem extends BaseFilesystem
{
    /**
     * Adds the slash term to a path, if it doesn't have one
     * @param string $path Path
     * @return string Path with the slash term
     */
    public function addSlashTerm($path)
    {
        return $this->isSlashTerm($path) ? $path : $path . DS;
    }

    /**
     * Concatenates various paths together, adding the right slash term
     * @param string $paths Various paths to be concatenated
     * @return string The path concatenated
     * @since 1.4.5
     */
    public function concatenate(...$paths)
    {
        $end = array_pop($paths);

        return implode('', array_map([$this, 'addSlashTerm'], $paths)) . $end;
    }

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
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function createFile($filename, $data = null, $dirMode = 0777, $ignoreErrors = false)
    {
        try {
            $this->mkdir(dirname($filename), $dirMode);
            $this->dumpFile($filename, $data);

            return true;
        } catch (IOException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }

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
     */
    public function createTmpFile($data = null, $dir = null, $prefix = 'tmp')
    {
        $filename = @tempnam($dir ?: (defined('TMP') ? TMP : sys_get_temp_dir()), $prefix);
        $this->createFile($filename, $data);

        return $filename;
    }

    /**
     * Returns an array of nested directories and files in each directory
     * @param string $path The directory path to build the tree from
     * @param array|bool $exceptions Either an array of filename or folder names
     *  to exclude or boolean true to not grab dot files/folders
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return array Array of nested directories and files in each directory
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    public function getDirTree($path, $exceptions = false, $ignoreErrors = false)
    {
        $path = $path === DS ? DS : rtrim($path, DS);
        $finder = new Finder();
        $exceptions = (array)(is_bool($exceptions) ? ($exceptions ? ['.'] : []) : $exceptions);

        $skipHidden = false;
        $finder->ignoreDotFiles(false);
        if (in_array('.', $exceptions)) {
            $skipHidden = true;
            unset($exceptions[array_search('.', $exceptions)]);
            $finder->ignoreDotFiles(true);
        }

        try {
            $finder->directories()->ignoreUnreadableDirs()->in($path);
            if ($exceptions) {
                $finder->exclude($exceptions);
            }
            $dirs = objects_map(array_values(iterator_to_array($finder->sortByName())), 'getPathname');
            array_unshift($dirs, rtrim($path, DS));

            $finder->files()->in($path);
            if ($exceptions) {
                $exceptions = array_map(function ($exception) {
                    return preg_quote($exception, '/');
                }, $exceptions);
                $finder->notName('/(' . implode('|', $exceptions) . ')/');
            }
            $files = objects_map(array_values(iterator_to_array($finder->sortByName())), 'getPathname');

            return [$dirs, $files];
        } catch (InvalidArgumentException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return [[], []];
        }
    }

    /**
     * Gets the extension from a filename.
     *
     * Unlike other functions, this removes query string and fragments (if the
     *  filename is an url) and knows how to recognize extensions made up of
     *  several parts (eg, `sql.gz`).
     * @param string $filename Filename
     * @return string|null
     */
    public function getExtension($filename)
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

    /**
     * Gets the root path.
     *
     * The root path must be set with the `ROOT` environment variable (using the
     *  `putenv()` function) or the `ROOT` constant.
     * @return string
     * @throws \Exception
     */
    public function getRoot()
    {
        $root = getenv('ROOT');
        if (!$root) {
            Exceptionist::isTrue(defined('ROOT'), 'No root path has been set. The root path must be set with the `ROOT` environment variable (using the `putenv()` function) or the `ROOT` constant');
            $root = ROOT;
        }

        return $root;
    }

    /**
     * Makes a relative path `$endPath` absolute, prepending `$startPath`
     * @param string $endPath An end path to be made absolute
     * @param string $startPath A start path to prepend
     * @return string
     * @since 1.4.5
     * @throws \InvalidArgumentException
     */
    public function makePathAbsolute($endPath, $startPath)
    {
        if (!$this->isAbsolutePath($startPath)) {
            throw new InvalidArgumentException(sprintf('The start path `%s` is not absolute', $startPath));
        }
        if ($this->isAbsolutePath($endPath)) {
            return $endPath;
        }

        return $this->concatenate($startPath, $endPath);
    }

    /**
     * Normalizes the path, applying the right slash term
     * @param string $path Path you want normalized
     * @return string Normalized path
     * @since 1.4.5
     */
    public function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DS, $path);
    }

    /**
     * Checks if a path ends in a slash (i.e. is slash-terminated)
     * @param string $path Path
     * @return bool
     */
    public function isSlashTerm($path)
    {
        return in_array($path[strlen($path) - 1], ['/', '\\']);
    }

    /**
     * Tells whether a directory and its subdirectories are writable.
     *
     * It can also check that all the files are writable.
     * @param string $dirname Path to the directory
     * @param bool $checkOnlyDir If `true`, also checks for all files
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    public function isWritableResursive($dirname, $checkOnlyDir = true, $ignoreErrors = false)
    {
        try {
            list($directories, $files) = $this->getDirTree($dirname);
            $items = $checkOnlyDir ? $directories : array_merge($directories, $files);

            if (!in_array($dirname, $items)) {
                $items[] = $dirname;
            }

            foreach ($items as $item) {
                if (!is_readable($item) || !is_writable($item)) {
                    return false;
                }
            }

            return true;
        } catch (InvalidArgumentException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }

    /**
     * Removes the directory itself and all its contents, including
     *  subdirectories and files.
     *
     * To remove only files contained in a directory and its sub-directories,
     *  leaving the directories unaltered, use the `unlinkRecursive()`
     *  method instead.
     * @param string $dirname Path to the directory
     * @return bool
     * @see \Tools\Filesystem::unlinkRecursive()
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function rmdirRecursive($dirname)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $this->remove($dirname);

        return true;
    }

    /**
     * Returns a path relative to the root path
     * @param string $path Absolute path
     * @return string Relative path
     */
    public function rtr($path)
    {
        $root = $this->getRoot();
        if ($this->isAbsolutePath($path) && string_starts_with($path, $root)) {
            $path = $this->normalizePath($this->makePathRelative($path, $root));
        }

        return rtrim($path, DS);
    }

    /**
     * Recursively removes all the files contained in a directory and within its
     *  sub-directories. This function only removes the files, leaving the
     *  directories unaltered.
     *
     * To remove the directory itself and all its contents, use the
     *  `rmdirRecursive()` method instead.
     * @param string $dirname The directory path
     * @param array|bool $exceptions Either an array of files to exclude
     *  or boolean true to not grab dot files
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @see \Tools\Filesystem::rmdirRecursive()
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    public function unlinkRecursive($dirname, $exceptions = false, $ignoreErrors = false)
    {
        try {
            list(, $files) = $this->getDirTree($dirname, $exceptions);
            $this->remove($files);

            return true;
        } catch (IOException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        } catch (InvalidArgumentException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }
}
