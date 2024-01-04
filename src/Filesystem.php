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
 * @since       1.4.4
 */

namespace Tools;

use InvalidArgumentException;
use LogicException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Provides basic utility to manipulate the file system.
 */
class Filesystem extends BaseFilesystem
{
    /**
     * @var \Tools\Filesystem
     */
    protected static Filesystem $_instance;

    /**
     * Adds the slash term to a path, if it doesn't have one
     * @param string $path Path
     * @return string Path with the slash term
     */
    public static function addSlashTerm(string $path): string
    {
        $isSlashTerm = in_array($path[strlen($path) - 1], ['/', '\\']);

        return $path . ($isSlashTerm ? '' : DS);
    }

    /**
     * Concatenates various paths together, adding the right slash term
     * @param string ...$paths Various paths to be concatenated
     * @return string The path concatenated
     * @since 1.4.5
     */
    public static function concatenate(string ...$paths): string
    {
        $end = array_pop($paths);

        /** @var callable $addSlashTerm */
        $addSlashTerm = [self::class, 'addSlashTerm'];

        return implode('', array_map($addSlashTerm, $paths)) . $end;
    }

    /**
     * Creates a file. Alias for `mkdir()` and `file_put_contents()`.
     *
     * It also recursively creates the directory where the file will be created.
     * @param string $filename Path to the file where to write the data
     * @param string|resource $data The data to write. Can be either a string or a resource
     * @param int $dirMode Mode for the directory, if it does not exist
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return string
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public static function createFile(mixed $filename, mixed $data = '', int $dirMode = 0777, bool $ignoreErrors = false): string
    {
        try {
            self::instance()->mkdir(dirname($filename), $dirMode);
            self::instance()->dumpFile($filename, $data);

            return $filename;
        } catch (IOException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return '';
        }
    }

    /**
     * Creates a temporary file. Alias for `tempnam()` and `file_put_contents()`.
     *
     * You can pass a directory where to create the file. If `null`, the file will be created in `TMP`, if the constant
     * is defined, otherwise in the temporary directory of the system.
     * @param string|resource $data The data to write. Can be either a string or a resource
     * @param string|null $dir The directory where the temporary filename will be created
     * @param string $prefix The prefix of the generated temporary filename
     * @return string Path of temporary filename
     * @throws \LogicException
     */
    public static function createTmpFile(mixed $data = '', ?string $dir = null, string $prefix = 'tmp'): string
    {
        $filename = tempnam($dir ?: (defined('TMP') ? TMP : sys_get_temp_dir()), $prefix) ?: '';
        if (!$filename) {
            throw new LogicException('It is not possible to create a temporary file');
        }

        return self::createFile($filename, $data);
    }

    /**
     * Returns an array of nested directories and files in each directory
     * @param string $path The directory path to build the tree from
     * @param string|string[]|bool $exceptions Either an array files/folders to exclude or `true` to not grab dot files/folders
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return string[][] Array of nested directories and files in each directory
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     */
    public static function getDirTree(string $path, string|bool|array $exceptions = false, bool $ignoreErrors = false): array
    {
        $path = $path === DS ? DS : rtrim($path, DS);
        $finder = new Finder();
        $exceptions = (array)(is_bool($exceptions) ? ($exceptions ? ['.'] : []) : $exceptions);

        $finder->ignoreDotFiles(false);
        if (in_array('.', $exceptions)) {
            unset($exceptions[array_search('.', $exceptions)]);
            $finder->ignoreDotFiles(true);
        }

        try {
            /**
             * Internal method.
             *
             * Takes a `Finder` instance, sorts by names and runs the `getPathname()` method on all elements, returning an array
             * @param \Symfony\Component\Finder\Finder $finder A `Finder` instance
             * @return string[]
             */
            $extractPaths = fn(Finder $finder): array =>
                 array_values(array_map(fn(SplFileInfo $file): string => $file->getPathname(), iterator_to_array($finder->sortByName())));

            $finder->directories()->ignoreUnreadableDirs()->in($path);
            if ($exceptions) {
                $finder->exclude($exceptions);
            }

            $dirs = $extractPaths($finder);
            array_unshift($dirs, rtrim($path, DS));

            $finder->files()->in($path);
            if ($exceptions) {
                $exceptions = array_map(fn($exception): string => preg_quote($exception, '/'), $exceptions);
                $finder->notName('/(' . implode('|', $exceptions) . ')/');
            }

            return [$dirs, $extractPaths($finder)];
        } catch (DirectoryNotFoundException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return [[], []];
        }
    }

    /**
     * Gets the extension from a filename.
     *
     * Unlike other functions, this removes query string and fragments (if the filename is an url) and knows how to
     * recognize extensions made up of several parts (eg, `sql.gz`).
     * @param string $filename Filename
     * @return string|null
     */
    public static function getExtension(string $filename): ?string
    {
        //Gets the basename and, if the filename is an url, removes query string and fragments (#)
        $filename = parse_url(basename($filename), PHP_URL_PATH);
        if (!$filename) {
            return null;
        }

        //On Windows, finds the occurrence of the last slash
        $pos = strripos($filename, '\\');
        if ($pos !== false) {
            $filename = substr($filename, $pos + 1);
        }

        //Finds the occurrence of the first point. The offset is 1 to preserve the hidden files
        $pos = strpos($filename, '.', 1);

        return $pos === false ? null : strtolower(substr($filename, $pos + 1));
    }

    /**
     * Gets the root path.
     *
     * The root path must be set with the `ROOT` environment variable (using `putenv()`) or the `ROOT` constant.
     * @return string
     * @throws \LogicException
     */
    public static function getRoot(): string
    {
        $root = getenv('ROOT');
        if (!$root && !defined('ROOT')) {
            throw new LogicException('No root path has been set. The root path must be set with the `ROOT` environment variable (using the `putenv()` function) or the `ROOT` constant');
        }

        return $root ?: ROOT;
    }

    /**
     * Returns the `Filesystem` instance
     * @return self
     * @since 1.4.7
     */
    public static function instance(): Filesystem
    {
        return self::$_instance ??= new Filesystem();
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
    public static function isWritableRecursive(string $dirname, bool $checkOnlyDir = true, bool $ignoreErrors = false): bool
    {
        try {
            [$directories, $files] = self::getDirTree($dirname);
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
        } catch (DirectoryNotFoundException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }

    /**
     * Makes a relative path `$endPath` absolute, prepending `$startPath`
     * @param string $endPath An end path to be made absolute
     * @param string $startPath A start path to prepend
     * @return string
     * @since 1.4.5
     * @throws \InvalidArgumentException
     */
    public static function makePathAbsolute(string $endPath, string $startPath): string
    {
        if (!self::instance()->isAbsolutePath($startPath)) {
            throw new InvalidArgumentException('The start path `' . $startPath . '` is not absolute');
        }

        return self::instance()->isAbsolutePath($endPath) ? $endPath : self::concatenate($startPath, $endPath);
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path
     * @param string $endPath Absolute path of target
     * @param string $startPath Absolute path where traversal begins
     * @return string Path of target relative to starting path
     * @since 1.5.10
     */
    public function makePathRelative(string $endPath, string $startPath): string
    {
        return self::normalizePath(rtrim(parent::makePathRelative($endPath, $startPath), '/\\'));
    }

    /**
     * Normalizes the path, applying the right slash term
     * @param string $path Path you want normalized
     * @return string Normalized path
     * @since 1.4.5
     */
    public static function normalizePath(string $path): string
    {
        return str_replace(['/', '\\'], DS, $path);
    }

    /**
     * Removes the directory itself and all its contents, including subdirectories and files.
     *
     * To remove only files contained in a directory and its subdirectories, leaving the directories unaltered, use the
     * `unlinkRecursive()` method instead.
     * @param string $dirname Path to the directory
     * @return bool
     * @see \Tools\Filesystem::unlinkRecursive()
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public static function rmdirRecursive(string $dirname): bool
    {
        if (!is_dir($dirname)) {
            return false;
        }
        self::instance()->remove($dirname);

        return true;
    }

    /**
     * Returns a path relative to the root path
     * @param string $path Absolute path
     * @return string Relative path
     */
    public static function rtr(string $path): string
    {
        if (self::instance()->isAbsolutePath($path) && str_starts_with($path, self::getRoot())) {
            $path = self::normalizePath(self::instance()->makePathRelative($path, self::getRoot()));
        }

        return rtrim($path, DS);
    }

    /**
     * Recursively removes all the files contained in a directory and within its subdirectories. This function only
     * removes the files, leaving the directories unaltered.
     *
     * To remove the directory itself and all its contents, use the rmdirRecursive()` method instead.
     * @param string $dirname The directory path
     * @param string|string[]|bool $exceptions Either an array of files to exclude or `true` to not grab dot files
     * @param bool $ignoreErrors With `true`, errors will be ignored
     * @return bool
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\Finder\Exception\DirectoryNotFoundException
     * @see \Tools\Filesystem::rmdirRecursive()
     */
    public static function unlinkRecursive(string $dirname, string|array|bool $exceptions = false, bool $ignoreErrors = false): bool
    {
        try {
            [, $files] = self::getDirTree($dirname, $exceptions);
            self::instance()->remove($files);

            return true;
        } catch (IOException | DirectoryNotFoundException $e) {
            if (!$ignoreErrors) {
                throw $e;
            }

            return false;
        }
    }
}
