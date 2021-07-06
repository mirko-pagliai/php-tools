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
 * @since       1.1.10
 */

namespace Tools\Exception;

use ErrorException;
use Exception;

/**
 * "Array key does not exist" exception.
 */
class KeyNotExistsException extends ErrorException
{
    /**
     * Key name
     * @var string|null
     */
    protected $key;

    /**
     * Constructor
     * @param string $message The string of the error message
     * @param int $code The exception code
     * @param int $severity The severity level of the exception
     * @param string $filename The filename where the exception is thrown
     * @param int $lineno The line number where the exception is thrown
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param string|null $key Name of the key that do not exist
     */
    public function __construct(string $message = '', int $code = 0, int $severity = E_ERROR, string $filename = '__FILE__', int $lineno = __LINE__, ?Exception $previous = null, ?string $key = null)
    {
        if (!$message) {
            $message = $key ? sprintf('Array key `%s` does not exist', $key) : 'Array key does not exist';
        }
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
        $this->key = $key;
    }

    /**
     * Gets the name of the key that do not exist
     * @return string|null
     * @since 1.2.11
     */
    public function getKeyName(): ?string
    {
        return $this->key;
    }
}
