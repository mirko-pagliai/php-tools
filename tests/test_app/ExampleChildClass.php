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

use Exception;

/**
 * An example class that extends the `ExampleClass` class
 */
class ExampleChildClass extends ExampleClass
{
    /**
     * @return void
     * @throws \Exception
     */
    public function throwMethod(): void
    {
        throw new Exception('Exception message...');
    }

    public function childMethod(): void
    {
    }

    public function anotherChildMethod(): void
    {
    }
}
