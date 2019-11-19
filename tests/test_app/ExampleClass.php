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

namespace App;

/**
 * An example class
 */
class ExampleClass
{
    private $privateProperty = 'this is a private property';

    protected $firstProperty;

    protected $secondProperty = 'a protected property';

    public $publicProperty = 'this is public';

    public static $staticProperty = 'a static property';

    protected function protectedMethod($var = null)
    {
        return $var ?: 'a protected method';
    }

    public function setProperty($propertyName, $propertyValue)
    {
        $this->$propertyName = $propertyValue;

        return $propertyValue;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function has($property)
    {
        return property_exists($this, $property);
    }
}
