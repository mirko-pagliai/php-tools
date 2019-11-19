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
 * @since       1.2.5
 */

namespace Tools\Exception;

use Tools\Exception\InvalidValueException;

/**
 * "Not positive value" exception
 */
class NotPositiveException extends InvalidValueException
{
    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param mixed $value The value that throwed the exception
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $value = null)
    {
        if (!$message) {
            $message = 'Value is not a positive';
            if ($value && is_stringable($value)) {
                $message = sprintf('Value `%s` is not a positive', (string)$value);
            }
        }
        parent::__construct($message, $code, $previous, $value);
    }
}
