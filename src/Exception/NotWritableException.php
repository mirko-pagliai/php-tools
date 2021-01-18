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
 * @since       1.1.7
 */

namespace Tools\Exception;

use Tools\FileException;
use Tools\Filesystem;

/**
 * "File or directory is not writable" exception.
 */
class NotWritableException extends FileException
{
    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The exception code
     * @param int $severity The severity level of the exception
     * @param string $filename The filename where the exception is thrown
     * @param int $lineno The line number where the exception is thrown
     * @param \Exception|null $previous The previous exception used for the exception chaining
     * @param string|null $path Path of the file that throwed the exception
     */
    public function __construct($message = '', $code = 0, $severity = E_ERROR, $filename = '__FILE__', $lineno = __LINE__, \Exception $previous = null, $path = null)
    {
        if (!$message) {
            $message = $path ? sprintf('Filename `%s` is not writable', (new Filesystem())->rtr($path)) : 'Filename is not writable';
        }
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous, $path);
    }
}
