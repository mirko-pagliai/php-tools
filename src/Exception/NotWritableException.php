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

use ErrorException;

/**
 * "File or directory is not writable" exception.
 * @deprecated 1.8.1 This exception class is deprecated and will be removed in a later release
 */
class NotWritableException extends ErrorException
{
    /**
     * @inheritDoc
     */
    public function __construct($message = "", $code = 0, $severity = 1, $filename = null, $line = null, Throwable $previous = null)
    {
        deprecationWarning('`' . __CLASS__ . '` is deprecated and will be removed in a later release');

        parent::__construct($message, $code, $severity, $filename, $line, $previous);
    }
}
