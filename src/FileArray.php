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
 * @since       1.0.10
 */
namespace Tools;

/**
 * This class allows you to read and write arrays using text files
 */
class FileArray
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $filename;

    /**
     * Construct
     * @param string $filename Filename
     * @uses read()
     * @uses $data
     * @uses $filename
     */
    public function __construct($filename)
    {
        is_writable_or_fail(is_file($filename) ? $filename : dirname($filename));

        $this->filename = $filename;
        $this->data = $this->read();
    }

    /**
     * Appends data to existing data
     * @param mixed $data Data
     * @return $this
     * @uses $data
     */
    public function append($data)
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Prepends data to existing data
     * @param mixed $data Data
     * @return $this
     * @uses $data
     */
    public function prepend($data)
    {
        $existing = $this->data;
        array_unshift($existing, $data);

        $this->data = $existing;

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
     * @uses $data
     * @uses $filename
     */
    public function read()
    {
        if ($this->data) {
            return $this->data;
        }

        if (!file_exists($this->filename)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->filename), true);

        return $data ?: [];
    }

    /**
     * Writes data to the file
     * @return bool
     * @uses $data
     * @uses $filename
     */
    public function write()
    {
        return (bool)file_put_contents($this->filename, json_encode($this->data));
    }
}
