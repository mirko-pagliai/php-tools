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

use PHPUnit\Framework\MockObject\MockObject;
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
     * @param int $filter The optional filter, for filtering desired property
     *  types. It's configured using `ReflectionProperty` constants, and
     *  default is public, protected and private properties
     * @return array Property names as keys and property values as values
     * @link http://php.net/manual/en/class.reflectionproperty.php#reflectionproperty.constants.modifiers
     * @since 1.1.4
     */
    protected function getProperties(&$object, $filter = 256 | 512 | 1024)
    {
        $properties = (new ReflectionClass($object))->getProperties($filter);

        //Removes properties added by PHPUnit, if the object is a mock
        $properties = $object instanceof MockObject ? array_filter($properties, function ($property) {
            return !starts_with($property->getName(), '__phpunit');
        }) : $properties;

        $values = array_map(function ($property) use ($object) {
            $property->setAccessible(true);

            return $property->getValue($object);
        }, $properties);

        return array_combine(objects_map($properties, 'getName'), $values);
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
    protected function getProperty(&$object, $name)
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
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
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
    protected function setProperty(&$object, $name, $value)
    {
        $property = $this->getPropertyInstance($object, $name);
        $oldValue = $property->getValue($object);
        $property->setValue($object, $value);

        return $oldValue;
    }
}
