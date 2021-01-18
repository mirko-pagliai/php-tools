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
 * @since       1.2.6
 */

namespace Tools\Exception;

use Tools\InvalidValueException;

/**
 * "Not in array" exception.
 */
class NotInArrayException extends InvalidValueException
{
    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The exception code
     * @param int $severity The severity level of the exception
     * @param string $filename The filename where the exception is thrown
     * @param int $lineno The line number where the exception is thrown
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param mixed $value The value that throwed the exception
     */
    public function __construct(?string $message = '', int $code = 0, int $severity = E_ERROR, string $filename = '__FILE__', int $lineno = __LINE__, ?\Exception $previous = null, $value = null)
    {
        if (!$message) {
            $message = is_stringable($value) ? sprintf('Value `%s` is not in the array', (string)$value) : 'Value is not in the array';
        }
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous, $value);
    }
}
