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

use ErrorException;

/**
 * Abstract exception for exceptions that are throwed by an invalid value.
 */
abstract class InvalidValueException extends ErrorException
{
    /**
     * Value
     * @var mixed
     */
    protected $value = null;

    /**
     * Constructor
     * @param string $message The string of the error message
     * @param int $code The exception code
     * @param int $severity The severity level of the exception
     * @param string $filename The filename where the exception is thrown
     * @param int $lineno The line number where the exception is thrown
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param mixed $value The value that throwed the exception
     */
    public function __construct($message = '', $code = 0, $severity = E_ERROR, $filename = '__FILE__', $lineno = __LINE__, \Exception $previous = null, $value = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
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
