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

use Tools\Exception\FileException;

/**
 * "Filename is not a directory" exception
 */
class NotDirectoryException extends FileException
{
    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $path Path of the file that throwed the exception
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $path = null)
    {
        if (!$message) {
            $message = 'Filename is not a directory';
            if ($path) {
                $message = sprintf('Filename `%s` is not a directory', rtr($path));
            }
        }
        parent::__construct($message, $code, $previous, $path);
    }
}
