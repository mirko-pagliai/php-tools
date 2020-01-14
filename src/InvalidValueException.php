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
 * @since       1.2.12
 */

namespace Tools;

use Exception;

/**
 * Abstract exception for exceptions that are throwed by an invalid value
 */
abstract class InvalidValueException extends Exception
{
    /**
     * Value
     * @var mixed
     */
    protected $value = null;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param mixed $value The value that throwed the exception
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $value = null)
    {
        parent::__construct($message, $code, $previous);
        $this->value = $value;
    }

    /**
     * Gets the value that throwed the exception
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
