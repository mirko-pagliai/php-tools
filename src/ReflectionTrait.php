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
namespace Tools;

use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A Reflection trait
 */
trait ReflectionTrait
{
    /**
     * Internal method to get the `ReflectionMethod` instance
     * @param object $object Instantiated object that we will run method on
     * @param string $methodName Method name
     * @return ReflectionMethod
     */
    protected function getMethodInstance(&$object, $methodName)
    {
        $method = new ReflectionMethod(get_class($object), $methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Gets all properties as array with property names as keys.
     *
     * If the object is a mock, it removes the properties added by PHPUnit.
     * @param object $object Object from which to get properties
     * @param int|null $filter The optional filter, for filtering desired property
     *  types. It's configured using the ReflectionProperty constants, and
     *  defaults to all property types
     * @return array
     * @since 1.1.4
     */
    protected function getProperties(&$object, $filter = null)
    {
        $filter = $filter ?: ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE;
        $properties = (new ReflectionClass($object))->getProperties($filter);

        //Removes the properties added by PHPUnit if the object is a mock
        if ($object instanceof PHPUnit_Framework_MockObject_MockObject) {
            $properties = array_filter($properties, function ($property) {
                return substr($property->getName(), 0, 9) !== '__phpunit';
            });
        }

        $keys = array_map(function ($property) {
            return $property->getName();
        }, $properties);

        $properties = array_map(function ($property) use ($object) {
            $property->setAccessible(true);

            return $property->getValue($object);
        }, $properties);

        return array_combine($keys, $properties);
    }

    /**
     * Internal method to get the `ReflectionProperty` instance
     * @param object $object Instantiated object that has the property
     * @param string $name Property name
     * @return ReflectionProperty
     */
    protected function getPropertyInstance(&$object, $name)
    {
        $property = new ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Gets a property value
     * @param object $object Instantiated object that has the property
     * @param string $name Property name
     * @return mixed Property value
     * @uses getPropertyInstance()
     */
    public function getProperty(&$object, $name)
    {
        return $this->getPropertyInstance($object, $name)->getValue($object);
    }

    /**
     * Invokes a method
     * @param object $object Instantiated object that we will run method on
     * @param string $methodName Method name
     * @param array $parameters Array of parameters to pass into method
     * @return mixed Method return
     * @uses getMethodInstance()
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        return $this->getMethodInstance($object, $methodName)->invokeArgs($object, $parameters);
    }

    /**
     * Sets a property value
     * @param object $object Instantiated object that has the property
     * @param string $name Property name
     * @param mixed $value Value you want to set
     * @return mixed Old property value
     * @uses getPropertyInstance()
     */
    public function setProperty(&$object, $name, $value)
    {
        $property = $this->getPropertyInstance($object, $name);
        $oldValue = $property->getValue($object);
        $property->setValue($object, $value);

        return $oldValue;
    }
}
