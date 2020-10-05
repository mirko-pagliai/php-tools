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
 * @since       1.1.7
 */

namespace Tools\Exception;

use Tools\FileException;

/**
 * "File or directory is not writable" exception.
 */
class NotWritableException extends FileException
{
    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $path Path of the file that throwed the exception
     */
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null, ?string $path = null)
    {
        if (!$message) {
            $message = $path ? sprintf('Filename `%s` is not writable', rtr($path)) : 'Filename is not writable';
        }
        parent::__construct($message, $code, $previous, $path);
    }
}
