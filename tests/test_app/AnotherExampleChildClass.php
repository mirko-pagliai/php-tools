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

namespace App;

use Exception;

/**
 * Another example class that extends the `ExampleClass` class
 */
class AnotherExampleChildClass extends ExampleClass
{
    public function __get($name)
    {
        return $this->$name;
    }

    public function throwMethod()
    {
        throw new Exception('Exception message...');
    }

    public function childMethod()
    {
    }

    public function anotherChildMethod()
    {
    }
}
