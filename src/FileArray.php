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
 * @since       1.0.10
 */

namespace Tools;

use Tools\Exceptionist;
use Tools\Filesystem;

/**
 * This class allows you to read and write arrays using text files
 * @deprecated 1.5.14 Deprecated. It will be removed in a future version
 */
class FileArray
{
    /**
     * Data
     * @var array
     */
    protected $data = [];

    /**
     * Filename
     * @var string
     */
    protected $filename;

    /**
     * Construct.
     *
     * If you want to create a file and you have an already prepared array,
     *  instead of reading data from an existing file, you can use the `$data`
     *  parameter.
     * @param string $filename Filename
     * @param array $data Optional initial data
     * @throws \Tools\Exception\NotWritableException
     * @uses read()
     */
    public function __construct(string $filename, array $data = [])
    {
        deprecationWarning('`FileArray` is deprecated will be removed in a future version');

        Exceptionist::isWritable(is_file($filename) ? $filename : dirname($filename));

        $this->filename = $filename;
        $this->data = $data ?: $this->read();
    }

    /**
     * Appends data to existing data
     * @param mixed $data Data
     * @return $this
     */
    public function append($data)
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Deletes a value from its key number.
     *
     * Note that the keys will be re-ordered.
     * @param int $key Key number
     * @return $this
     * @throws \Tools\Exception\KeyNotExistsException
     */
    public function delete(int $key)
    {
        Exceptionist::arrayKeyExists($key, $this->data);
        unset($this->data[$key]);
        $this->data = array_values($this->data);

        return $this;
    }

    /**
     * Checks if a key number exists
     * @param int $key Key number
     * @return bool
     */
    public function exists(int $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Gets a value from its key number
     * @param int $key Key number
     * @return mixed
     * @throws \Tools\Exception\KeyNotExistsException
     */
    public function get(int $key)
    {
        Exceptionist::arrayKeyExists($key, $this->data);

        return $this->data[$key];
    }

    /**
     * Prepends data to existing data
     * @param mixed $data Data
     * @return $this
     */
    public function prepend($data)
    {
        array_unshift($this->data, $data);

        return $this;
    }

    /**
     * Reads data.
     *
     * The first time, the file content is read. The next time the property
     *  value will be returned.
     *
     * If there are no data or if the file does not exist, it still returns an
     *  empty array.
     * @return array
     */
    public function read(): array
    {
        if ($this->data || !file_exists($this->filename)) {
            return $this->data;
        }

        return @unserialize(file_get_contents($this->filename) ?: '') ?: [];
    }

    /**
     * Extract a slice of data, with maximum `$size` values.
     *
     * If a second parameter is passed, it will determine from what position to
     *  start taking values.
     * @param int $size Maximun number of values
     * @param int $from What position to start taking values
     * @return $this
     */
    public function take(int $size, int $from = 0)
    {
        $this->data = array_slice($this->data, $from, $size);

        return $this;
    }

    /**
     * Writes data to the file
     * @return bool
     */
    public function write(): bool
    {
        return Filesystem::instance()->createFile($this->filename, serialize($this->data));
    }
}
