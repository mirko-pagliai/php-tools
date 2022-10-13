<?php /** @noinspection PhpMissingFieldTypeInspection, PhpUnusedPrivateFieldInspection */
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
    private string $privateProperty = 'this is a private property';

    protected $firstProperty;

    protected ?string $secondProperty = 'a protected property';

    public string $publicProperty = 'this is public';

    public static string $staticProperty = 'a static property';

    /**
     * @param mixed $var
     * @return mixed
     */
    protected function protectedMethod($var = null)
    {
        return $var ?: 'a protected method';
    }

    /**
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return mixed
     */
    public function setProperty(string $propertyName, $propertyValue)
    {
        $this->$propertyName = $propertyValue;

        return $propertyValue;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->$name;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function has(string $property): bool
    {
        return property_exists($this, $property);
    }
}
