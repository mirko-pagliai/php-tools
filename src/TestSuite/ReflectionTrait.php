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

namespace Tools\TestSuite;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A `Reflection` trait.
 */
trait ReflectionTrait
{
    /**
     * Internal method to get the `ReflectionMethod` instance
     * @param object $object Instantiated object that we will run method on
     * @param string $name Method name
     * @return \ReflectionMethod
     */
    protected function _getMethodInstance(object &$object, string $name): ReflectionMethod
    {
        $method = new ReflectionMethod(get_class($object), $name);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Internal method to get the `ReflectionProperty` instance
     * @param object $object Instantiated object that has the property
     * @param string $name Property name
     * @return \ReflectionProperty
     */
    protected function _getPropertyInstance(object &$object, string $name): ReflectionProperty
    {
        $property = new ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Gets all properties as array with property names as keys.
     *
     * If the object is a mock, it removes the properties added by PHPUnit.
     * @param string|object $object Instantiated object from which to get
     *  properties or class name
     * @param int $filter The optional filter, for filtering desired property
     *  types. It's configured using `ReflectionProperty` constants, and
     *  default is public, protected and private properties
     * @return array<string, string> Property names as keys and property values as values
     * @link http://php.net/manual/en/class.reflectionproperty.php#reflectionproperty.constants.modifiers
     * @since 1.1.4
     */
    protected function getProperties($object, int $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE): array
    {
        $object = is_object($object) ? $object : new $object();
        $properties = (new ReflectionClass($object))->getProperties($filter);

        //Removes properties added by PHPUnit, if the object is a mock
        $properties = array_filter($properties, function ($property) {
            return !string_starts_with($property->getName(), '__phpunit');
        });

        $values = array_map(function (ReflectionProperty $property) use ($object) {
            $property->setAccessible(true);

            return $property->getValue($object);
        }, $properties);

        return array_combine(objects_map($properties, 'getName'), $values) ?: [];
    }

    /**
     * Gets a property value
     * @param string|object $object Instantiated object that has the property
     *  or class name
     * @param string $name Property name
     * @return mixed Property value
     * @uses _getPropertyInstance()
     */
    protected function getProperty($object, string $name)
    {
        $object = is_object($object) ? $object : new $object();

        return $this->_getPropertyInstance($object, $name)->getValue($object);
    }

    /**
     * Invokes a method
     * @param string|object $object Instantiated object that we will run method
     *  on or class name
     * @param string $methodName Method name
     * @param array $parameters Array of parameters to pass into method
     * @return mixed Method return
     * @uses _getMethodInstance()
     */
    protected function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $object = is_object($object) ? $object : new $object();

        return $this->_getMethodInstance($object, $methodName)->invokeArgs($object, $parameters);
    }

    /**
     * Sets a property value
     * @param object $object Instantiated object that has the property
     * @param string $name Property name
     * @param mixed $value Value you want to set
     * @return mixed Old property value
     * @uses _getPropertyInstance()
     */
    protected function setProperty(object &$object, string $name, $value)
    {
        $property = $this->_getPropertyInstance($object, $name);
        $oldValue = $property->getValue($object);
        $property->setValue($object, $value);

        return $oldValue;
    }
}
