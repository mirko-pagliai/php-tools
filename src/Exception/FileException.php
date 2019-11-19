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
 * @since       1.2.12
 */

namespace Tools\Exception;

use Exception;

/**
 * Abstract exception for exceptions that are throwed by a file
 */
abstract class FileException extends Exception
{
    /**
     * Path
     * @var string|null
     */
    protected $path;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $path Path of the file that throwed the exception
     * @uses $path
     */
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null, ?string $path = null)
    {
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    /**
     * Gets the path of the file that throwed the exception
     * @return string|null
     * @uses $path
     */
    public function getFilePath(): ?string
    {
        return $this->path;
    }
}
