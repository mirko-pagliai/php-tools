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

use Exception;

/**
 * "File or directory does not exist" exception
 */
class FileNotExistsException extends Exception
{
    /**
     * @var string|null
     */
    protected $path;

    /**
     * Constructor
     * @param string|null $message The string of the error message
     * @param int $code The code of the error
     * @param \Throwable|null $previous the previous exception
     * @param string|null $path Path of file that do not exist
     * @uses $path
     */
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null, ?string $path = null)
    {
        if (!$message) {
            $message = 'File or directory does not exist';
            if ($path) {
                $message = sprintf('File or directory `%s` does not exist', rtr($path));
            }
        }
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    /**
     * Gets the path of file that do not exist
     * @return string|null
     * @since 1.2.10
     * @uses $path
     */
    public function getFilePath(): ?string
    {
        return $this->path;
    }
}
