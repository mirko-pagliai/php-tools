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
 * @since       1.1.10
 */
namespace Tools\Exception;

use Exception;

/**
 * Array key not exists exception
 */
class KeyNotExistsException extends Exception
{
    /**
     * Constructor
     * @param string $message The string of the error message
     * @param int|null $code The code of the error
     * @param \Exception|null $previous the previous exception
     */
    public function __construct($message = 'Key does not exist', $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
