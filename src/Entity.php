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
 * @since       1.1.15
 */

namespace Tools;

use ArrayAccess;

/**
 * An Entity class.
 *
 * It exposes the methods for retrieving and storing properties associated.
 */
abstract class Entity implements ArrayAccess
{
    /**
     * Properties
     * @var array
     */
    protected $properties;

    /**
     * Initializes the internal properties
     * @param array $properties Properties to set
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    /**
     * Called by `var_dump()` when dumping the object to get the properties that
     *  should be shown
     * @return array
     * @uses toArray()
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    /**
     * Magic method for reading data from inaccessible properties
     * @param string $property Property name
     * @return mixed Property value
     * @uses get()
     */
    public function __get(string $property)
    {
        return $this->get($property);
    }

    /**
     * Checks if a property exists
     * @param string $property Property name
     * @return bool
     */
    public function has(string $property): bool
    {
        return array_key_exists($property, $this->properties);
    }

    /**
     * Magic method for reading data from inaccessible properties
     * @param string $property Property name
     * @param mixed $default Default value if the property does not exist
     * @return mixed Property value
     * @uses has()
     */
    public function get(string $property, $default = null)
    {
        return $this->has($property) ? $this->properties[$property] : $default;
    }

    /**
     * Implements `isset($entity);`
     * @param mixed $offset The offset to check
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->properties[$offset]);
    }

    /**
     * Implements `$entity[$offset];`
     * @param mixed $offset The offset to get
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->properties[$offset];
    }

    /**
     * Implements `$entity[$offset] = $value;`
     * @param mixed $offset The offset to set
     * @param mixed $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->properties[$offset] = $value;
    }

    /**
     * Implements `unset($result[$offset]);`
     * @param mixed $offset The offset to remove
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->properties[$offset]);
    }

    /**
     * Sets a single property inside this entity
     * @param string|array $property The name of property to set or a list of
     *  properties with their respective values
     * @param mixed $value The value to set to the property
     * @return $this
     */
    public function set($property, $value = null)
    {
        if (is_string($property) && $value != '') {
            $property = [$property => $value];
        }

        foreach ($property as $name => $value) {
            $this->properties[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns an array with all the properties that have been set to this entity
     * @return array
     */
    public function toArray(): array
    {
        $properties = $this->properties;

        foreach ($properties as $name => $value) {
            if ($value instanceof Entity) {
                $properties[$name] = $value->toArray();
            }
        }

        return $properties;
    }
}
