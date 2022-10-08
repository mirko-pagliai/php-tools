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
 * @since       1.6.4
 */

namespace Tools\Exception;

use ErrorException;
use Exception;

/**
 * "Method not exists" exception.
 */
class MethodNotExistsException extends ErrorException
{
    /**
     * Method name
     * @var string|null
     */
    protected ?string $method;

    /**
     * Constructor
     * @param string $message The string of the error message
     * @param int $code The exception code
     * @param int $severity The severity level of the exception
     * @param string $filename The filename where the exception is thrown
     * @param int $lineno The line number where the exception is thrown
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param string|null $method Method that does not exist
     */
    public function __construct(string $message = '', int $code = 0, int $severity = E_ERROR, string $filename = '__FILE__', int $lineno = __LINE__, ?Exception $previous = null, ?string $method = null)
    {
        parent::__construct($message ?: ($method ? 'Method `' . $method . '` does not exist' : 'Method does not exist'), $code, $severity, $filename, $lineno, $previous);
        $this->method = $method;
    }

    /**
     * Gets the method name that does not exist
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }
}
